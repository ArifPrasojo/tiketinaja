<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct(private BookingService $bookingService)
    {
        $this->init();
    }

    public function init(): void
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = (bool) config('midtrans.is_production', false);
        Config::$isSanitized = (bool) config('midtrans.is_sanitized', true);
        Config::$is3ds = (bool) config('midtrans.is_3ds', true);
    }

    public function isConfigured(): bool
    {
        return !empty(config('midtrans.server_key')) && !empty(config('midtrans.client_key'));
    }

    public function getOrCreateSnapToken(Booking $booking): ?string
    {
        if (!$this->isConfigured()) {
            return null;
        }

        $booking->loadMissing(['payment', 'items.court', 'user', 'promo']);

        if (!$booking->payment) {
            return null;
        }

        // Jika waktu 15 menit sudah lewat, jangan izinkan Snap token
        if ($booking->expires_at && $booking->expires_at->isPast()) {
            return null;
        }

        if ($booking->payment->snap_token) {
            return $booking->payment->snap_token;
        }

        $grossAmount = (int) $booking->payment->amount;
        if ($grossAmount <= 0) {
            return null;
        }

        $itemDetails = [];
        foreach ($booking->items as $item) {
            $name = $item->court?->name ?? 'Sesi Lapangan';
            $timeSlot = $item->start_time . '-' . $item->end_time;
            $itemDetails[] = [
                'id'       => 'ITEM-' . $item->id,
                'price'    => (int) $item->price,
                'quantity' => 1,
                'name'     => mb_substr($name . ' (' . $timeSlot . ')', 0, 50),
            ];
        }

        if ($booking->discount_amount > 0) {
            $promoCode = $booking->promo?->code ? ' (' . $booking->promo->code . ')' : '';
            $itemDetails[] = [
                'id'       => 'DISCOUNT',
                'price'    => -((int) $booking->discount_amount),
                'quantity' => 1,
                'name'     => mb_substr('Diskon Promo' . $promoCode, 0, 50),
            ];
        }

        $sumItems = array_sum(array_map(fn ($i) => $i['price'] * $i['quantity'], $itemDetails));
        if ($sumItems !== $grossAmount) {
            $itemDetails = [
                [
                    'id'       => $booking->booking_code,
                    'price'    => $grossAmount,
                    'quantity' => 1,
                    'name'     => mb_substr('Booking Lapangan ' . ($booking->venue?->name ?? ''), 0, 50),
                ]
            ];
        }

        $remainingMinutes = 15;
        if ($booking->expires_at) {
            $diff = (int) now()->diffInMinutes($booking->expires_at, false);
            $remainingMinutes = max(1, min($diff, 15));
        }

        $params = [
            'transaction_details' => [
                'order_id'     => $booking->payment->payment_code,
                'gross_amount' => $grossAmount,
            ],
            'item_details'        => $itemDetails,
            'customer_details'    => [
                'first_name' => $booking->user?->name ?? 'Customer',
                'email'      => $booking->user?->email ?? 'customer@example.com',
                'phone'      => $booking->user?->phone ?? '',
            ],
            'expiry'              => [
                'start_time' => now()->format('Y-m-d H:i:s O'),
                'unit'       => 'minute',
                'duration'   => $remainingMinutes,
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            $booking->payment->update(['snap_token' => $snapToken]);
            return $snapToken;
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Token generation error: ' . $e->getMessage(), [
                'booking_id' => $booking->id,
                'order_id'   => $booking->payment->payment_code,
            ]);
            return null;
        }
    }

    public function verifySignature(string $orderId, string $statusCode, string $grossAmount, string $signatureKey): bool
    {
        $serverKey = config('midtrans.server_key');
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        return hash_equals($expectedSignature, $signatureKey);
    }

    public function handleNotification(array $payload): array
    {
        $orderId           = $payload['order_id'] ?? '';
        $statusCode        = (string) ($payload['status_code'] ?? '');
        $grossAmount       = (string) ($payload['gross_amount'] ?? '');
        $signatureKey      = $payload['signature_key'] ?? '';
        $transactionStatus = $payload['transaction_status'] ?? '';
        $fraudStatus       = $payload['fraud_status'] ?? 'accept';
        $paymentType       = $payload['payment_type'] ?? 'midtrans';

        if (!$this->verifySignature($orderId, $statusCode, $grossAmount, $signatureKey)) {
            return [
                'success' => false,
                'message' => 'Invalid signature key',
                'code'    => 403,
            ];
        }

        $payment = Payment::where('payment_code', $orderId)
            ->with(['booking.user'])
            ->first();

        if (!$payment) {
            $payment = Payment::whereHas('booking', fn ($q) => $q->where('booking_code', $orderId))
                ->with(['booking.user'])
                ->first();
        }

        if (!$payment || !$payment->booking) {
            return [
                'success' => false,
                'message' => 'Payment or booking not found',
                'code'    => 404,
            ];
        }

        $booking = $payment->booking;

        if ($transactionStatus === 'capture') {
            if ($fraudStatus === 'challenge') {
                $payment->update([
                    'status'         => PaymentStatus::Pending,
                    'payment_method' => $paymentType,
                    'payload'        => json_encode($payload),
                ]);
            } elseif ($fraudStatus === 'accept') {
                $this->bookingService->processPayment($booking, $paymentType, $payload);
            }
        } elseif ($transactionStatus === 'settlement') {
            $this->bookingService->processPayment($booking, $paymentType, $payload);
        } elseif ($transactionStatus === 'pending') {
            $payment->update([
                'status'         => PaymentStatus::Pending,
                'payment_method' => $paymentType,
                'payload'        => json_encode($payload),
            ]);
        } elseif (in_array($transactionStatus, ['deny', 'cancel'])) {
            $payment->update([
                'status'         => PaymentStatus::Cancelled,
                'payment_method' => $paymentType,
                'payload'        => json_encode($payload),
            ]);
            if ($booking->status === BookingStatus::Pending) {
                try {
                    $this->bookingService->cancelBooking($booking);
                } catch (\Exception $e) {
                    Log::warning('Failed auto-cancel booking on Midtrans cancel: ' . $e->getMessage());
                }
            }
        } elseif ($transactionStatus === 'expire') {
            $payment->update([
                'status'         => PaymentStatus::Expired,
                'payment_method' => $paymentType,
                'payload'        => json_encode($payload),
            ]);
            $booking->reservedSlots()->delete();
            $booking->update(['status' => BookingStatus::Expired]);
        }

        return [
            'success' => true,
            'message' => 'Notification processed successfully',
            'code'    => 200,
        ];
    }
}
