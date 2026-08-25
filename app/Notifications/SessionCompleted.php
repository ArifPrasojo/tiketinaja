<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SessionCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Booking $booking)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $firstItem = $this->booking->items->first();
        $lastItem  = $this->booking->items->last();
        $venue     = $this->booking->venue?->name;
        $court     = $firstItem?->court?->name;
        $duration  = $this->booking->items->count();

        return (new MailMessage)
            ->subject('Sesi Bermain Selesai – ' . $this->booking->booking_code)
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Sesi bermain Anda telah selesai. Terima kasih sudah menggunakan TiketinAja!')
            ->line('**Venue:** ' . $venue)
            ->line('**Lapangan:** ' . $court)
            ->line('**Durasi:** ' . $duration . ' jam (' . $firstItem?->start_time . ' – ' . $lastItem?->end_time . ')')
            ->line('**Total Dibayar:** Rp ' . number_format($this->booking->payment?->amount ?? 0, 0, ',', '.'))
            ->action('Beri Ulasan', url('/bookings/' . $this->booking->id))
            ->line('Bagikan pengalaman Anda dengan memberikan ulasan. Sampai jumpa lagi! 🏆');
    }

    public function toArray(object $notifiable): array
    {
        $firstItem = $this->booking->items->first();
        $lastItem  = $this->booking->items->last();
        $court     = $firstItem?->court?->name;
        $venue     = $this->booking->venue?->name;

        return [
            'type'         => 'session_completed',
            'booking_id'   => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
            'message'      => "Sesi bermain di {$court}, {$venue} telah selesai. Terima kasih! Beri ulasan Anda.",
            'url'          => '/bookings/' . $this->booking->id,
            'icon'         => '🏆',
        ];
    }
}
