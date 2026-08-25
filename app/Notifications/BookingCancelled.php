<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCancelled extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Booking $booking)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Booking Dibatalkan – ' . $this->booking->booking_code)
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Booking Anda dengan kode **' . $this->booking->booking_code . '** telah dibatalkan.')
            ->line('**Venue:** ' . $this->booking->venue?->name)
            ->action('Cari Venue Lain', url('/venues'))
            ->line('Jika Anda tidak merasa membatalkan, segera hubungi kami.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'booking_cancelled',
            'booking_id'   => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
            'message'      => 'Booking ' . $this->booking->booking_code . ' telah dibatalkan.',
            'url'          => '/bookings/' . $this->booking->id,
        ];
    }
}
