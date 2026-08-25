<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Court;
use App\Models\OperatingHour;
use App\Models\Promo;
use App\Models\Sport;
use App\Models\User;
use App\Models\Venue;
use App\Services\BookingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class BookingEngineTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private Court $court;
    private BookingService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $manager        = User::factory()->manager()->create();
        $this->customer = User::factory()->create();

        $sport = Sport::create(['name' => 'Futsal', 'slug' => 'futsal']);

        $venue = Venue::create([
            'manager_id'  => $manager->id,
            'name'        => 'Test Venue',
            'slug'        => 'test-venue',
            'address'     => 'Test Address',
            'city'        => 'Jakarta',
            'phone'       => '08123',
            'description' => 'Desc',
        ]);

        for ($i = 0; $i <= 6; $i++) {
            OperatingHour::create([
                'venue_id'    => $venue->id,
                'day_of_week' => $i,
                'open_time'   => '08:00',
                'close_time'  => '22:00',
            ]);
        }

        $this->court = Court::create([
            'venue_id'       => $venue->id,
            'sport_id'       => $sport->id,
            'name'           => 'Court 1',
            'price_per_hour' => 100000,
            'is_active'      => true,
        ]);

        $this->service = app(BookingService::class);
    }

    public function test_can_create_booking_successfully(): void
    {
        $date    = now()->addDay()->toDateString();
        $booking = $this->service->createBooking(
            $this->customer,
            $this->court,
            $date,
            ['09:00', '10:00']
        );

        $this->assertEquals(BookingStatus::Pending, $booking->status);
        $this->assertEquals(200000, $booking->total_amount);
        $this->assertEquals(0, $booking->discount_amount);
        $this->assertCount(2, $booking->items);
        $this->assertCount(2, $booking->reservedSlots);
        $this->assertEquals('09:00', substr($booking->reservedSlots[0]->start_time, 0, 5));
        $this->assertNotNull($booking->qr_code);
    }

    public function test_prevents_double_booking_same_slot(): void
    {
        $date = now()->addDay()->toDateString();

        $this->service->createBooking($this->customer, $this->court, $date, ['09:00']);

        $customer2 = User::factory()->create();

        $this->expectException(ValidationException::class);
        $this->service->createBooking($customer2, $this->court, $date, ['09:00']);
    }

    public function test_mock_payment_confirms_booking(): void
    {
        $date     = now()->addDay()->toDateString();
        $booking  = $this->service->createBooking($this->customer, $this->court, $date, ['10:00']);

        $confirmed = $this->service->processPayment($booking);

        $this->assertEquals(BookingStatus::Confirmed, $confirmed->status);
        $this->assertEquals(PaymentStatus::Paid, $confirmed->payment->status);
        $this->assertNotNull($confirmed->payment->paid_at);
    }

    public function test_pending_booking_expires_and_releases_slot(): void
    {
        $date    = now()->addDay()->toDateString();
        $booking = $this->service->createBooking($this->customer, $this->court, $date, ['11:00']);

        $booking->update(['expires_at' => now()->subMinute()]);

        $expiredCount = $this->service->expirePendingBookings();

        $this->assertEquals(1, $expiredCount);
        $this->assertEquals(BookingStatus::Expired, $booking->fresh()->status);
        $this->assertEmpty($booking->fresh()->reservedSlots);
    }

    public function test_customer_cannot_view_other_customer_booking(): void
    {
        $date    = now()->addDay()->toDateString();
        $booking = $this->service->createBooking($this->customer, $this->court, $date, ['12:00']);

        $otherCustomer = User::factory()->create();

        $this->actingAs($otherCustomer)
            ->get(route('bookings.show', $booking))
            ->assertStatus(403);
    }

    public function test_booking_with_promo_applies_discount(): void
    {
        $promo = Promo::create([
            'code'        => 'TEST50K',
            'description' => 'Diskon 50rb',
            'type'        => 'fixed',
            'value'       => 50000,
            'is_active'   => true,
        ]);

        $date    = now()->addDay()->toDateString();
        $booking = $this->service->createBooking(
            $this->customer,
            $this->court,
            $date,
            ['13:00'],
            'TEST50K'
        );

        $this->assertEquals(100000, $booking->total_amount);
        $this->assertEquals(50000, $booking->discount_amount);
        $this->assertEquals(50000, $booking->final_amount);
        $this->assertEquals(50000, $booking->payment->amount);

        // Promo usage_count incremented
        $this->assertEquals(1, $promo->fresh()->used_count);

        // Can't reuse same promo
        $this->expectException(ValidationException::class);
        $this->service->createBooking(
            $this->customer,
            $this->court,
            $date,
            ['14:00'],
            'TEST50K'
        );
    }

    public function test_percentage_promo_is_capped_at_max_discount(): void
    {
        Promo::create([
            'code'         => 'PCT20',
            'description'  => '20% max 10rb',
            'type'         => 'percentage',
            'value'        => 20,
            'max_discount' => 10000,
            'is_active'    => true,
        ]);

        $date    = now()->addDay()->toDateString();
        $booking = $this->service->createBooking(
            $this->customer,
            $this->court,
            $date,
            ['15:00'],
            'PCT20'
        );

        // 20% of 100000 = 20000, capped at 10000
        $this->assertEquals(10000, $booking->discount_amount);
    }

    public function test_checkin_marks_booking_as_checked_in(): void
    {
        $date    = now()->addDay()->toDateString();
        $booking = $this->service->createBooking($this->customer, $this->court, $date, ['08:00']);
        $this->service->processPayment($booking);
        $booking->refresh();

        $this->actingAs($this->customer);

        // Fake time to 10 minutes before play time so check-in window is open
        $playDateTime = \Carbon\Carbon::parse($date . ' 08:00');
        \Carbon\Carbon::setTestNow($playDateTime->copy()->subMinutes(5));

        $result = $this->service->checkIn($booking);

        $this->assertTrue($result['booking']->fresh()->checked_in);
        $this->assertNotNull($result['booking']->fresh()->checked_in_at);
        $this->assertFalse($result['is_play_time']); // 5 min before, not yet play time

        \Carbon\Carbon::setTestNow(); // reset
    }

    public function test_checkin_at_exact_play_time_is_marked_playtime(): void
    {
        $date    = now()->addDay()->toDateString();
        $booking = $this->service->createBooking($this->customer, $this->court, $date, ['09:00']);
        $this->service->processPayment($booking);
        $booking->refresh();

        $this->actingAs($this->customer);

        // Fake time to exactly play time
        \Carbon\Carbon::setTestNow(\Carbon\Carbon::parse($date . ' 09:00'));

        $result = $this->service->checkIn($booking);

        $this->assertTrue($result['is_play_time']);
        $this->assertEquals(0, $result['minutes_to_play']);

        \Carbon\Carbon::setTestNow();
    }

    public function test_checkin_too_early_throws_exception(): void
    {
        $date    = now()->addDay()->toDateString();
        $booking = $this->service->createBooking($this->customer, $this->court, $date, ['10:00']);
        $this->service->processPayment($booking);
        $booking->refresh();

        $this->actingAs($this->customer);

        // Fake time to 30 minutes before play — check-in window not open yet (opens at 09:50)
        \Carbon\Carbon::setTestNow(\Carbon\Carbon::parse($date . ' 09:20'));

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $this->service->checkIn($booking);

        \Carbon\Carbon::setTestNow();
    }

    public function test_checkin_after_booking_end_time_throws_exception(): void
    {
        $date    = now()->addDay()->toDateString();
        $booking = $this->service->createBooking($this->customer, $this->court, $date, ['11:00']);
        $this->service->processPayment($booking);
        $booking->refresh();

        $this->actingAs($this->customer);

        // Fake time to after end time (12:00+)
        \Carbon\Carbon::setTestNow(\Carbon\Carbon::parse($date . ' 12:30'));

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $this->service->checkIn($booking);

        \Carbon\Carbon::setTestNow();
    }

    public function test_cancel_confirmed_booking_within_24h_fails(): void
    {
        // Create booking normally (slot available tomorrow)
        $date    = now()->addDay()->toDateString();
        $booking = $this->service->createBooking($this->customer, $this->court, $date, ['08:00']);
        $this->service->processPayment($booking);
        $booking->refresh();

        // Override booking_date on items to simulate a slot that's < 24h away
        $booking->items()->update(['booking_date' => now()->addHours(6)->toDateString()]);
        $booking->reservedSlots()->update(['booking_date' => now()->addHours(6)->toDateString()]);

        $this->expectException(ValidationException::class);
        $this->service->cancelBooking($booking);
    }
}
