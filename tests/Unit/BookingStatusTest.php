<?php

namespace Tests\Unit;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Court;
use Carbon\Carbon;
use Tests\TestCase;

class BookingStatusTest extends TestCase
{
    public function test_booking_status_labels_are_in_indonesian(): void
    {
        $this->assertEquals('Menunggu Pembayaran', BookingStatus::Pending->label());
        $this->assertEquals('Terkonfirmasi', BookingStatus::Confirmed->label());
        $this->assertEquals('Dibatalkan', BookingStatus::Cancelled->label());
        $this->assertEquals('Kedaluwarsa', BookingStatus::Expired->label());
        $this->assertEquals('Selesai', BookingStatus::Completed->label());
    }

    public function test_payment_status_labels_are_in_indonesian(): void
    {
        $this->assertEquals('Menunggu Pembayaran', PaymentStatus::Pending->label());
        $this->assertEquals('Dibayar', PaymentStatus::Paid->label());
        $this->assertEquals('Gagal', PaymentStatus::Failed->label());
        $this->assertEquals('Kedaluwarsa', PaymentStatus::Expired->label());
        $this->assertEquals('Dibatalkan', PaymentStatus::Cancelled->label());
    }

    public function test_booking_status_values(): void
    {
        $this->assertEquals('pending',   BookingStatus::Pending->value);
        $this->assertEquals('confirmed', BookingStatus::Confirmed->value);
        $this->assertEquals('cancelled', BookingStatus::Cancelled->value);
        $this->assertEquals('expired',   BookingStatus::Expired->value);
        $this->assertEquals('completed', BookingStatus::Completed->value);
    }

    public function test_can_create_from_string_value(): void
    {
        $this->assertEquals(BookingStatus::Confirmed, BookingStatus::from('confirmed'));
        $this->assertEquals(PaymentStatus::Paid, PaymentStatus::from('paid'));
    }

    public function test_badge_class_returns_correct_tailwind_classes(): void
    {
        $this->assertStringContainsString('yellow', BookingStatus::Pending->badgeClass());
        $this->assertStringContainsString('blue',   BookingStatus::Confirmed->badgeClass());
        $this->assertStringContainsString('red',    BookingStatus::Cancelled->badgeClass());
        $this->assertStringContainsString('gray',   BookingStatus::Expired->badgeClass());
        $this->assertStringContainsString('green',  BookingStatus::Completed->badgeClass());
    }

    public function test_bar_class_returns_string(): void
    {
        foreach (BookingStatus::cases() as $case) {
            $this->assertNotEmpty($case->barClass());
        }
    }

    public function test_label_for_confirmed_no_checkin_returns_standard_label(): void
    {
        $booking = new Booking();
        $booking->status      = BookingStatus::Confirmed;
        $booking->checked_in  = false;
        $booking->setRelation('items', collect());

        $this->assertEquals('Terkonfirmasi', BookingStatus::Confirmed->labelFor($booking));
    }

    public function test_label_for_confirmed_checked_in_past_returns_checkin_label(): void
    {
        $booking = new Booking();
        $booking->status      = BookingStatus::Confirmed;
        $booking->checked_in  = true;

        // Create a fake item with end_time in the past
        $item = new BookingItem();
        $item->booking_date = Carbon::yesterday()->toDateString();
        $item->end_time     = '09:00';
        $booking->setRelation('items', collect([$item]));

        $label = BookingStatus::Confirmed->labelFor($booking);
        $this->assertEquals('Check-in ✓', $label);
    }

    public function test_label_for_confirmed_checked_in_ongoing_returns_playing_label(): void
    {
        $booking = new Booking();
        $booking->status     = BookingStatus::Confirmed;
        $booking->checked_in = true;

        // Item ends in 30 minutes from now
        $item = new BookingItem();
        $item->booking_date = now()->toDateString();
        $item->end_time     = now()->addMinutes(30)->format('H:i');
        $booking->setRelation('items', collect([$item]));

        $label = BookingStatus::Confirmed->labelFor($booking);
        $this->assertEquals('Sedang Bermain', $label);
    }
}
