<?php

namespace App\Enums;

enum BookingStatus: string
{
    case Pending   = 'pending';
    case Confirmed = 'confirmed';
    case Cancelled = 'cancelled';
    case Expired   = 'expired';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Pending   => 'Menunggu Pembayaran',
            self::Confirmed => 'Terkonfirmasi',
            self::Cancelled => 'Dibatalkan',
            self::Expired   => 'Kedaluwarsa',
            self::Completed => 'Selesai',
        };
    }

    /**
     * Label kontekstual: booking confirmed yang sedang aktif (sudah check-in & belum habis)
     * tampil berbeda dari confirmed biasa.
     */
    public function labelFor(\App\Models\Booking $booking): string
    {
        if ($this === self::Confirmed) {
            if ($booking->checked_in && $this->isOngoing($booking)) {
                return 'Sedang Bermain';
            }
            if ($booking->checked_in) {
                return 'Check-in ✓';
            }
        }
        return $this->label();
    }

    /** Tailwind badge classes */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending   => 'bg-yellow-100 text-yellow-800',
            self::Confirmed => 'bg-blue-100 text-blue-800',
            self::Cancelled => 'bg-red-100 text-red-800',
            self::Expired   => 'bg-gray-100 text-gray-600',
            self::Completed => 'bg-green-100 text-green-800',
        };
    }

    /** Tailwind badge class dengan konteks booking */
    public function badgeClassFor(\App\Models\Booking $booking): string
    {
        if ($this === self::Confirmed && $booking->checked_in && $this->isOngoing($booking)) {
            return 'bg-indigo-100 text-indigo-800';
        }
        return $this->badgeClass();
    }

    /** Left-bar color untuk booking list */
    public function barClass(): string
    {
        return match ($this) {
            self::Pending   => 'bg-yellow-400',
            self::Confirmed => 'bg-blue-400',
            self::Cancelled => 'bg-red-300',
            self::Expired   => 'bg-gray-300',
            self::Completed => 'bg-green-400',
        };
    }

    /** Apakah booking sedang aktif dimainkan sekarang */
    private function isOngoing(\App\Models\Booking $booking): bool
    {
        $now      = now();
        $lastItem = $booking->items->last();
        if (!$lastItem) return false;

        $endTime = \Carbon\Carbon::parse($lastItem->booking_date)
            ->setTimeFromTimeString($lastItem->end_time);

        return $now->lt($endTime);
    }
}
