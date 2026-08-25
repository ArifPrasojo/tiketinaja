<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingExpired extends Notification implements ShouldQueue
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
            ->subject('Booking Kedaluwarsa – ' . $this->booking->booking_code)
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Booking **' . $this->booking->booking_code . '** telah kedaluwarsa karena pembayaran tidak diselesaikan dalam 15 menit.')
            ->line('**Venue:** ' . $this->booking->venue?->name)
            ->action('Booking Ulang', url('/venues/' . $this->booking->venue?->slug))
            ->line('Silakan lakukan booking ulang jika masih ingin menggunakan lapangan tersebut.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'booking_expired',
            'booking_id'   => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
            'message'      => 'Booking ' . $this->booking->booking_code . ' telah kedaluwarsa.',
            'url'          => '/venues/' . $this->booking->venue?->slug,
        ];
    }
}
