<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Models\Court;
use App\Models\OperatingHour;
use App\Models\Sport;
use App\Models\User;
use App\Models\Venue;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SessionCompletionTest extends TestCase
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

        $sport = Sport::create(['name' => 'Tenis', 'slug' => 'tenis']);
        $venue = Venue::create([
            'manager_id'  => $manager->id,
            'name'        => 'Complete Venue',
            'slug'        => 'complete-venue',
            'address'     => 'Addr',
            'city'        => 'Kota',
            'phone'       => '0812',
            'description' => 'Desc',
        ]);

        for ($i = 0; $i <= 6; $i++) {
            OperatingHour::create([
                'venue_id'    => $venue->id,
                'day_of_week' => $i,
                'open_time'   => '07:00',
                'close_time'  => '23:00',
            ]);
        }

        $this->court = Court::create([
            'venue_id'       => $venue->id,
            'sport_id'       => $sport->id,
            'name'           => 'Court T',
            'price_per_hour' => 75000,
            'is_active'      => true,
        ]);

        $this->service = app(BookingService::class);
    }

    /** ── Helpers ─────────────────────────────────── */

    private function makeCheckedInBooking(string $playDate, string $startSlot): \App\Models\Booking
    {
        $booking = $this->service->createBooking($this->customer, $this->court, $playDate, [$startSlot]);
        $this->service->processPayment($booking);
        $booking->refresh();

        // Set time to just inside the check-in window
        $playTime = Carbon::parse($playDate . ' ' . $startSlot);
        Carbon::setTestNow($playTime->copy()->subMinutes(5));

        $this->actingAs($this->customer);
        $this->service->checkIn($booking);

        Carbon::setTestNow(); // reset

        return $booking->fresh();
    }

    /** ── Tests ───────────────────────────────────── */

    public function test_checked_in_booking_is_completed_after_end_time(): void
    {
        $date    = now()->addDay()->toDateString();
        $booking = $this->makeCheckedInBooking($date, '08:00');

        // Advance time to after end_time (09:01)
        Carbon::setTestNow(Carbon::parse($date . ' 09:01'));

        $count = $this->service->completeFinishedBookings();

        $this->assertEquals(1, $count);
        $this->assertEquals(BookingStatus::Completed, $booking->fresh()->status);

        Carbon::setTestNow();
    }

    public function test_ongoing_booking_is_not_completed_early(): void
    {
        $date    = now()->addDay()->toDateString();
        $booking = $this->makeCheckedInBooking($date, '10:00');

        // Only 30 minutes in — still playing
        Carbon::setTestNow(Carbon::parse($date . ' 10:30'));

        $count = $this->service->completeFinishedBookings();

        $this->assertEquals(0, $count);
        $this->assertEquals(BookingStatus::Confirmed, $booking->fresh()->status);

        Carbon::setTestNow();
    }

    public function test_confirmed_without_checkin_is_completed_after_end_time(): void
    {
        $date    = now()->addDay()->toDateString();
        $booking = $this->service->createBooking($this->customer, $this->court, $date, ['11:00']);
        $this->service->processPayment($booking);

        // No check-in, advance past end time
        Carbon::setTestNow(Carbon::parse($date . ' 12:05'));

        $count = $this->service->completeFinishedBookings();

        $this->assertEquals(1, $count);
        $this->assertEquals(BookingStatus::Completed, $booking->fresh()->status);

        Carbon::setTestNow();
    }

    public function test_session_completed_notification_is_sent(): void
    {
        \Notification::fake();

        $date    = now()->addDay()->toDateString();
        $booking = $this->makeCheckedInBooking($date, '14:00');

        Carbon::setTestNow(Carbon::parse($date . ' 15:01'));
        $this->service->completeFinishedBookings();

        \Notification::assertSentTo(
            $this->customer,
            \App\Notifications\SessionCompleted::class,
            fn($n) => $n->booking->id === $booking->id
        );

        Carbon::setTestNow();
    }

    public function test_complete_command_runs_successfully(): void
    {
        $date    = now()->addDay()->toDateString();
        $booking = $this->makeCheckedInBooking($date, '16:00');

        Carbon::setTestNow(Carbon::parse($date . ' 17:01'));

        $this->artisan('bookings:complete')->assertSuccessful();

        $this->assertEquals(BookingStatus::Completed, $booking->fresh()->status);

        Carbon::setTestNow();
    }
}
