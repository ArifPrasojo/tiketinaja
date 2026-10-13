<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\BookingService;
use App\Services\MidtransService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MidtransController extends Controller
{
    use AuthorizesRequests;

    public function token(Booking $booking, MidtransService $midtransService): JsonResponse
    {
        $this->authorize('pay', $booking);

        if ($booking->expires_at && $booking->expires_at->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Waktu pembayaran sudah habis.',
            ], 422);
        }

        $token = $midtransService->getOrCreateSnapToken($booking);

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat token pembayaran Midtrans. Pastikan konfigurasi Midtrans sudah benar.',
            ], 500);
        }

        return response()->json([
            'success'    => true,
            'snap_token' => $token,
            'client_key' => config('midtrans.client_key'),
        ]);
    }

    public function finish(Request $request, Booking $booking, BookingService $bookingService): JsonResponse
    {
        $this->authorize('pay', $booking);

        $payload = $request->all();
        $status = $payload['transaction_status'] ?? $payload['status'] ?? '';

        if (in_array($status, ['settlement', 'capture', 'success'])) {
            $paymentType = $payload['payment_type'] ?? 'midtrans';
            $bookingService->processPayment($booking, $paymentType, $payload);

            return response()->json([
                'success'      => true,
                'message'      => 'Pembayaran berhasil dikonfirmasi!',
                'redirect_url' => route('bookings.show', $booking),
            ]);
        }

        return response()->json([
            'success'      => false,
            'message'      => 'Pembayaran sedang diproses atau belum selesai.',
            'redirect_url' => route('bookings.show', $booking),
        ]);
    }

    public function notification(Request $request, MidtransService $midtransService): JsonResponse
    {
        $result = $midtransService->handleNotification($request->all());

        return response()->json([
            'status'  => $result['success'] ? 'OK' : 'ERROR',
            'message' => $result['message'],
        ], $result['code'] ?? 200);
    }
}
