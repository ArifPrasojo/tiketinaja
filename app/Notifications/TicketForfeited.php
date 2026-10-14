<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketForfeited extends Notification implements ShouldQueue
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
            ->subject('Tiket Hangus – ' . $this->booking->booking_code)
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Tiket booking **' . $this->booking->booking_code . '** Anda telah dinyatakan **hangus**.')
            ->line('Penyebab: Anda tidak melakukan check-in selama masa aktif sewa lapangan di **' . ($this->booking->venue?->name ?? 'venue') . '**.')
            ->line('Sesuai ketentuan, tiket yang tidak di-check-in hingga waktu sesi bermain berakhir tidak dapat digunakan kembali atau dikembalikan.')
            ->action('Lihat Detail Tiket', url('/bookings/' . $this->booking->id))
            ->line('Terima kasih telah menggunakan TiketinAja.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'ticket_forfeited',
            'booking_id'   => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
            'message'      => 'Tiket ' . $this->booking->booking_code . ' hangus karena tidak melakukan check-in selama masa sewa.',
            'url'          => '/bookings/' . $this->booking->id,
        ];
    }
}
