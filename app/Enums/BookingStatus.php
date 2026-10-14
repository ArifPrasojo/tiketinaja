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
     * tampil berbeda dari confirmed biasa, dan booking hangus jika tidak check-in.
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

        if ($this === self::Expired) {
            $isPaid = $booking->payment && (
                $booking->payment->status === PaymentStatus::Paid ||
                (is_string($booking->payment->status) && $booking->payment->status === 'paid')
            );

            if ($isPaid) {
                return 'Tiket Hangus';
            }
        }

        return $this->label();
    }

    /** Tailwind badge classes */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending   => 'bg-amber-50 text-amber-700 border border-amber-200',
            self::Confirmed => 'bg-blue-50 text-electric-blue border border-blue-200',
            self::Cancelled => 'bg-red-50 text-red-700 border border-red-200',
            self::Expired   => 'bg-slate-100 text-slate-600 border border-slate-200',
            self::Completed => 'bg-green-50 text-field-green border border-green-200',
        };
    }

    /** Tailwind badge class dengan konteks booking */
    public function badgeClassFor(\App\Models\Booking $booking): string
    {
        if ($this === self::Confirmed && $booking->checked_in && $this->isOngoing($booking)) {
            return 'bg-green-50 text-field-green border border-green-200';
        }

        if ($this === self::Expired) {
            $isPaid = $booking->payment && (
                $booking->payment->status === PaymentStatus::Paid ||
                (is_string($booking->payment->status) && $booking->payment->status === 'paid')
            );

            if ($isPaid) {
                return 'bg-red-50 text-red-700 border border-red-200';
            }
        }

        return $this->badgeClass();
    }

    /** Left-bar color untuk booking list */
    public function barClass(): string
    {
        return match ($this) {
            self::Pending   => 'bg-amber-400',
            self::Confirmed => 'bg-sky-400',
            self::Cancelled => 'bg-red-400',
            self::Expired   => 'bg-slate-500',
            self::Completed => 'bg-emerald-400',
        };
    }

    public function barClassFor(\App\Models\Booking $booking): string
    {
        if ($this === self::Expired) {
            $isPaid = $booking->payment && (
                $booking->payment->status === PaymentStatus::Paid ||
                (is_string($booking->payment->status) && $booking->payment->status === 'paid')
            );

            if ($isPaid) {
                return 'bg-red-500';
            }
        }

        return $this->barClass();
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
