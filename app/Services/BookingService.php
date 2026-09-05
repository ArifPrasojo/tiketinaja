<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\{User, Court, Booking, BookingItem, ReservedSlot, Payment, Promo, ActivityLog};
use App\Notifications\BookingConfirmed;
use App\Notifications\BookingCancelled;
use App\Notifications\BookingExpired;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BookingService
{
    public function getAvailableSlots(Court $court, string $date): array
    {
        $carbon = Carbon::parse($date);
        $operatingHour = $court->venue->operatingHours()
            ->where('day_of_week', $carbon->dayOfWeek)
            ->first();

        if (!$operatingHour || $operatingHour->is_closed) {
            return [];
        }

        $reserved = ReservedSlot::where('court_id', $court->id)
            ->whereDate('booking_date', $carbon->toDateString())
            ->pluck('start_time')
            ->map(fn($time) => substr($time, 0, 5))
            ->all();

        $slots = [];
        $open  = Carbon::parse($operatingHour->open_time);
        $close = Carbon::parse($operatingHour->close_time);
        $now   = now();
        
        // Aturan anker: menit harus :00, minimal mulai jam 07:00
        $anchor = $open->copy();
        if ($anchor->minute > 0) {
            $anchor->addHour()->startOfHour(); // Snap up jika ada menit lebih
        }
        
        // Batas minimal jam 07:00
        if ($anchor->hour < 7) {
            $anchor->hour(7)->startOfHour();
        }

        for ($time = $anchor->copy(); $time < $close; $time->addHour()) {
            // Pastikan end time (1 jam setelah start time) tidak melewati batas tutup
            $endTimeCalc = $time->copy()->addHour();
            if ($endTimeCalc > $close) {
                break;
            }

            $startTime = $time->format('H:i');
            $endTime   = $endTimeCalc->format('H:i');
            $taken     = in_array($startTime, $reserved);

            $slots[] = [
                'start_time' => $startTime,
                'end_time'   => $endTime,
                'available'  => !$taken && !($carbon->isToday() && $time->lte($now)),
            ];
        }

        return $slots;
    }

    public function createBooking(
        User $user,
        Court $court,
        string $date,
        array $timeSlots,
        ?string $promoCode = null
    ): Booking {
        if (!$court->is_active) {
            throw ValidationException::withMessages(['court' => 'Lapangan tidak tersedia']);
        }

        if (!$court->venue->is_active) {
            throw ValidationException::withMessages(['venue' => 'Venue tidak tersedia']);
        }

        $carbon = Carbon::parse($date);
        if ($carbon->isPast() && !$carbon->isToday()) {
            throw ValidationException::withMessages(['date' => 'Tanggal tidak valid']);
        }

        $availableSlots = collect($this->getAvailableSlots($court, $date))
            ->filter(fn($slot) => $slot['available'])
            ->pluck('start_time')
            ->all();

        foreach ($timeSlots as $slot) {
            if (!in_array($slot, $availableSlots)) {
                throw ValidationException::withMessages(['slots' => "Slot {$slot} tidak tersedia"]);
            }
        }

        // Resolve promo if provided
        $promo          = null;
        $discountAmount = 0;
        $totalAmount    = count($timeSlots) * $court->price_per_hour;

        if ($promoCode) {
            $promo = Promo::where('code', strtoupper($promoCode))->first();

            if (!$promo || !$promo->isValid()) {
                throw ValidationException::withMessages(['promo_code' => 'Kode promo tidak valid atau sudah kedaluwarsa']);
            }

            // Check if promo is venue-specific and matches
            if ($promo->venue_id && $promo->venue_id !== $court->venue_id) {
                throw ValidationException::withMessages(['promo_code' => 'Kode promo tidak berlaku untuk venue ini']);
            }

            // Check per-user usage (one use per promo per user)
            $alreadyUsed = $promo->usages()->where('user_id', $user->id)->exists();
            if ($alreadyUsed) {
                throw ValidationException::withMessages(['promo_code' => 'Kode promo sudah pernah digunakan']);
            }

            $discountAmount = $promo->calculateDiscount($totalAmount);
        }

        try {
            return DB::transaction(function () use (
                $user, $court, $carbon, $timeSlots, $promo, $discountAmount, $totalAmount
            ) {
                // Generate QR token
                $qrToken = Str::uuid()->toString();

                $booking = Booking::create([
                    'booking_code'    => 'BK-' . strtoupper(Str::random(8)),
                    'user_id'         => $user->id,
                    'venue_id'        => $court->venue_id,
                    'promo_id'        => $promo?->id,
                    'status'          => BookingStatus::Pending,
                    'total_amount'    => $totalAmount,
                    'discount_amount' => $discountAmount,
                    'expires_at'      => now()->addMinutes(15),
                    'qr_code'         => $qrToken,
                ]);

                foreach ($timeSlots as $slot) {
                    $endTime = Carbon::parse($slot)->addHour()->format('H:i');

                    BookingItem::create([
                        'booking_id'   => $booking->id,
                        'court_id'     => $court->id,
                        'booking_date' => $carbon->toDateString(),
                        'start_time'   => $slot,
                        'end_time'     => $endTime,
                        'price'        => $court->price_per_hour,
                    ]);

                    ReservedSlot::create([
                        'booking_id'   => $booking->id,
                        'court_id'     => $court->id,
                        'booking_date' => $carbon->toDateString(),
                        'start_time'   => $slot,
                    ]);
                }

                Payment::create([
                    'booking_id'   => $booking->id,
                    'payment_code' => 'PAY-' . strtoupper(Str::random(8)),
                    'amount'       => $booking->total_amount - $discountAmount,
                    'status'       => PaymentStatus::Pending,
                ]);

                // Record promo usage
                if ($promo) {
                    $promo->usages()->create([
                        'user_id'         => $user->id,
                        'booking_id'      => $booking->id,
                        'discount_amount' => $discountAmount,
                    ]);
                    $promo->increment('used_count');
                }

                // Activity log
                $this->logActivity($user, 'booking_created', $booking, [
                    'booking_code' => $booking->booking_code,
                    'total_amount' => $totalAmount,
                    'discount'     => $discountAmount,
                    'venue'        => $court->venue->name,
                ]);

                return $booking->load(['items', 'reservedSlots', 'payment', 'user', 'venue']);
            });
        } catch (\Illuminate\Database\QueryException $e) {
            throw ValidationException::withMessages(['slots' => 'Slot sudah terisi, silakan pilih jam lain']);
        }
    }

    public function processPayment(Booking $booking): Booking
    {
        $booking->payment()->update([
            'status'  => PaymentStatus::Paid,
            'paid_at' => now(),
        ]);

        $booking->update(['status' => BookingStatus::Confirmed]);
        $booking->refresh();

        // Activity log
        $this->logActivity($booking->user, 'payment_confirmed', $booking, [
            'booking_code'   => $booking->booking_code,
            'payment_amount' => $booking->payment->amount,
        ]);

        // Send notification
        try {
            $booking->user->notify(new BookingConfirmed($booking));
        } catch (\Exception $e) {
            Log::warning('Failed to send BookingConfirmed notification: ' . $e->getMessage());
        }

        return $booking->load(['payment', 'items']);
    }

    public function cancelBooking(Booking $booking): Booking
    {
        $earliestItem = $booking->items()
            ->orderBy('booking_date')
            ->orderBy('start_time')
            ->first();

        $cancellable = in_array($booking->status->value, ['pending', 'confirmed']);

        if ($booking->status === BookingStatus::Confirmed && $earliestItem) {
            $earliestDateTime = Carbon::parse($earliestItem->booking_date)
                ->setTimeFromTimeString($earliestItem->start_time);
            $cancellable = $earliestDateTime->gt(now()->addDay());
        }

        if (!$cancellable) {
            throw ValidationException::withMessages(['booking' => 'Booking tidak dapat dibatalkan']);
        }

        $booking->reservedSlots()->delete();
        $booking->update(['status' => BookingStatus::Cancelled]);
        $booking->payment()->update(['status' => PaymentStatus::Cancelled]);

        // Activity log — ambil user dari semua guard yang mungkin aktif
        $actingUser = auth()->user() ?? auth('sanctum')->user() ?? $booking->user;
        $this->logActivity($actingUser, 'booking_cancelled', $booking, [
            'booking_code' => $booking->booking_code,
        ]);

        // Send notification
        try {
            $booking->user->notify(new BookingCancelled($booking));
        } catch (\Exception $e) {
            Log::warning('Failed to send BookingCancelled notification: ' . $e->getMessage());
        }

        return $booking;
    }

    public function expirePendingBookings(): int
    {
        $bookings = Booking::where('status', BookingStatus::Pending)
            ->where('expires_at', '<', now())
            ->get();

        foreach ($bookings as $booking) {
            $booking->reservedSlots()->delete();
            $booking->update(['status' => BookingStatus::Expired]);
            $booking->payment()->update(['status' => PaymentStatus::Expired]);

            $this->logActivity(null, 'booking_expired', $booking, [
                'booking_code' => $booking->booking_code,
            ]);

            // Send notification
            try {
                $booking->user->notify(new BookingExpired($booking));
            } catch (\Exception $e) {
                Log::warning('Failed to send BookingExpired notification: ' . $e->getMessage());
            }
        }

        return $bookings->count();
    }

    /**
     * Mark confirmed bookings as completed.
     *
     * Priority logic:
     * 1. Booking yang sudah check-in → selesai saat end_time lewat (akurat per menit)
     * 2. Booking confirmed tapi TIDAK check-in → selesai setelah end_time lewat
     *
     * Called every minute by scheduler.
     */
    public function completeFinishedBookings(): int
    {
        $now   = now();
        $today = $now->toDateString();   // Y-m-d
        $time  = $now->format('H:i:s');  // H:i:s
        $count = 0;

        // Confirmed bookings whose ALL slots have ended
        $bookings = Booking::where('status', BookingStatus::Confirmed)
            // Must have at least one item (sanity check)
            ->has('items')
            // No item is still in the future or currently active
            ->whereDoesntHave('items', function ($q) use ($today, $time) {
                $q->where(function ($inner) use ($today, $time) {
                    // Item is on a future date
                    $inner->whereRaw('DATE(booking_date) > ?', [$today])
                          // OR: same date but end_time hasn't passed yet
                          ->orWhere(function ($same) use ($today, $time) {
                              $same->whereRaw('DATE(booking_date) = ?', [$today])
                                   ->where('end_time', '>', $time);
                          });
                });
            })
            ->with(['items.court', 'venue', 'user'])
            ->get();

        foreach ($bookings as $booking) {
            $booking->update(['status' => BookingStatus::Completed]);
            $count++;

            $this->logActivity(null, 'booking_completed', $booking, [
                'booking_code' => $booking->booking_code,
                'checked_in'   => $booking->checked_in,
            ]);

            try {
                $booking->user->notify(new \App\Notifications\SessionCompleted($booking));
            } catch (\Exception $e) {
                Log::warning('Failed to send SessionCompleted notification: ' . $e->getMessage());
            }
        }

        return $count;
    }

    /**
     * Check-in rule:
     * - Booking harus berstatus Confirmed
     * - Belum pernah check-in
     * - Hanya boleh dilakukan paling cepat 10 menit sebelum jam main
     *   (contoh: jam main 09:00, check-in terbuka mulai 08:50)
     * - Notifikasi berbeda: jika sudah ≥ jam main → "Waktunya main!"
     *                       jika masih < jam main → "Check-in berhasil, siap-siap"
     */
    public function checkIn(Booking $booking): array
    {
        if ($booking->status !== BookingStatus::Confirmed) {
            throw ValidationException::withMessages([
                'booking' => 'Hanya booking terkonfirmasi yang bisa check-in.',
            ]);
        }

        if ($booking->checked_in) {
            throw ValidationException::withMessages([
                'booking' => 'Booking ini sudah check-in sebelumnya.',
            ]);
        }

        // Ambil slot pertama (jam main paling awal)
        $firstItem = $booking->items()
            ->orderBy('booking_date')
            ->orderBy('start_time')
            ->first();

        if (!$firstItem) {
            throw ValidationException::withMessages([
                'booking' => 'Data booking tidak lengkap.',
            ]);
        }

        // Waktu mulai main dalam Carbon
        $playTime = Carbon::parse($firstItem->booking_date)
            ->setTimeFromTimeString($firstItem->start_time);

        $now = now();

        // Batas check-in = 10 menit sebelum jam main
        $checkInOpensAt = $playTime->copy()->subMinutes(10);

        if ($now->lt($checkInOpensAt)) {
            $menit = (int) $now->diffInMinutes($checkInOpensAt, true);
            throw ValidationException::withMessages([
                'booking' => "Check-in belum dibuka. Silakan kembali {$menit} menit lagi "
                    . "(check-in dibuka pukul {$checkInOpensAt->format('H:i')}).",
            ]);
        }

        // Tidak bisa check-in jika jam main sudah lewat lebih dari durasi booking
        $lastItem    = $booking->items()->orderBy('booking_date')->orderByDesc('end_time')->first();
        $endTime     = Carbon::parse($lastItem->booking_date)->setTimeFromTimeString($lastItem->end_time);

        if ($now->gt($endTime)) {
            throw ValidationException::withMessages([
                'booking' => 'Waktu bermain sudah selesai. Check-in tidak dapat dilakukan.',
            ]);
        }

        // Lakukan check-in
        $booking->update([
            'checked_in'    => true,
            'checked_in_at' => $now,
        ]);

        $this->logActivity(auth()->user(), 'booking_checked_in', $booking, [
            'booking_code'    => $booking->booking_code,
            'checked_in_at'   => $now->toDateTimeString(),
            'play_time'       => $playTime->toDateTimeString(),
            'is_play_time'    => $now->gte($playTime),
        ]);

        // Kirim notifikasi yang sesuai
        $isPlayTime = $now->gte($playTime);

        try {
            $booking->load(['items.court', 'venue', 'user']);
            $booking->user->notify(new \App\Notifications\CheckInSuccess($booking, $isPlayTime));
        } catch (\Exception $e) {
            Log::warning('Failed to send CheckInSuccess notification: ' . $e->getMessage());
        }

        return [
            'booking'     => $booking,
            'is_play_time' => $isPlayTime,
            'play_time'    => $playTime->format('H:i'),
            'minutes_to_play' => $isPlayTime ? 0 : (int) $now->diffInMinutes($playTime, true),
        ];
    }

    public function validatePromo(string $code, int $amount, int $venueId, ?int $userId = null): array
    {
        $promo = Promo::where('code', strtoupper($code))->first();

        if (!$promo || !$promo->isValid()) {
            return ['valid' => false, 'message' => 'Kode promo tidak valid atau sudah kedaluwarsa'];
        }

        if ($promo->venue_id && $promo->venue_id !== $venueId) {
            return ['valid' => false, 'message' => 'Kode promo tidak berlaku untuk venue ini'];
        }

        // Cek per-user usage jika user diketahui
        if ($userId) {
            $alreadyUsed = $promo->usages()->where('user_id', $userId)->exists();
            if ($alreadyUsed) {
                return ['valid' => false, 'message' => 'Kode promo sudah pernah Anda gunakan'];
            }
        }

        if ($amount < $promo->min_booking_amount) {
            return [
                'valid'   => false,
                'message' => 'Minimal pembelian Rp ' . number_format($promo->min_booking_amount, 0, ',', '.'),
            ];
        }

        $discount = $promo->calculateDiscount($amount);

        return [
            'valid'           => true,
            'promo_id'        => $promo->id,
            'discount_amount' => $discount,
            'final_amount'    => $amount - $discount,
            'description'     => $promo->description,
        ];
    }

    private function logActivity(?User $user, string $action, Booking $booking, array $properties = []): void
    {
        try {
            ActivityLog::create([
                'user_id'      => $user?->id,
                'action'       => $action,
                'subject_type' => Booking::class,
                'subject_id'   => $booking->id,
                'properties'   => $properties,
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to write activity log: ' . $e->getMessage());
        }
    }
}
