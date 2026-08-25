<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CheckInSuccess extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Booking $booking,
        public readonly bool $isPlayTime  // true = sudah waktunya main, false = check-in lebih awal
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $firstItem = $this->booking->items->first();
        $venue     = $this->booking->venue?->name;
        $court     = $firstItem?->court?->name;
        $startTime = $firstItem?->start_time;

        if ($this->isPlayTime) {
            return (new MailMessage)
                ->subject('🏃 Waktunya Main! – ' . $this->booking->booking_code)
                ->greeting('Halo, ' . $notifiable->name . '!')
                ->line('Check-in berhasil! Lapangan Anda sudah siap sekarang.')
                ->line('**Venue:** ' . $venue)
                ->line('**Lapangan:** ' . $court)
                ->line('**Jam Mulai:** ' . $startTime)
                ->line('Segera masuki venue dan nikmati permainan Anda!')
                ->action('Lihat Detail Booking', url('/bookings/' . $this->booking->id));
        }

        return (new MailMessage)
            ->subject('✓ Check-in Berhasil – ' . $this->booking->booking_code)
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Check-in Anda telah tercatat. Lapangan akan tersedia pada jam **' . $startTime . '**.')
            ->line('**Venue:** ' . $venue)
            ->line('**Lapangan:** ' . $court)
            ->line('Mohon bersiap sebelum jam main dimulai.')
            ->action('Lihat Detail Booking', url('/bookings/' . $this->booking->id));
    }

    public function toArray(object $notifiable): array
    {
        $firstItem = $this->booking->items->first();
        $startTime = $firstItem?->start_time;
        $venue     = $this->booking->venue?->name;
        $court     = $firstItem?->court?->name;

        if ($this->isPlayTime) {
            return [
                'type'         => 'checkin_playtime',
                'booking_id'   => $this->booking->id,
                'booking_code' => $this->booking->booking_code,
                'message'      => "Waktunya main! Lapangan {$court} di {$venue} sudah siap untuk Anda. Segera masuk venue.",
                'url'          => '/bookings/' . $this->booking->id,
                'icon'         => '🏃',
            ];
        }

        return [
            'type'         => 'checkin_success',
            'booking_id'   => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
            'message'      => "Check-in berhasil! Lapangan {$court} di {$venue} akan siap pukul {$startTime}.",
            'url'          => '/bookings/' . $this->booking->id,
            'icon'         => '✓',
        ];
    }
}
