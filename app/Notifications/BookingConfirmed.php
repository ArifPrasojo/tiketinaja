<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmed extends Notification implements ShouldQueue
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
        $firstItem = $this->booking->items->first();

        return (new MailMessage)
            ->subject('Booking Dikonfirmasi – ' . $this->booking->booking_code)
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Pembayaran Anda telah berhasil dan booking berikut telah dikonfirmasi:')
            ->line('**Kode Booking:** ' . $this->booking->booking_code)
            ->line('**Venue:** ' . $this->booking->venue?->name)
            ->line('**Lapangan:** ' . $firstItem?->court?->name)
            ->line('**Tanggal:** ' . ($firstItem ? \Carbon\Carbon::parse($firstItem->booking_date)->format('d M Y') : '-'))
            ->line('**Jam:** ' . $firstItem?->start_time . ' – ' . $firstItem?->end_time)
            ->line('**Total Dibayar:** Rp ' . number_format($this->booking->payment?->amount ?? 0, 0, ',', '.'))
            ->action('Lihat Detail Booking', url('/bookings/' . $this->booking->id))
            ->line('Tunjukkan QR code pada halaman detail booking saat check-in.')
            ->line('Terima kasih telah menggunakan TiketinAja!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'booking_confirmed',
            'booking_id'   => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
            'message'      => 'Booking ' . $this->booking->booking_code . ' telah dikonfirmasi.',
            'url'          => '/bookings/' . $this->booking->id,
        ];
    }
}
