<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Court;
use App\Models\OperatingHour;
use App\Models\Sport;
use App\Models\User;
use App\Models\Venue;
use App\Services\BookingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
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

        $sport = Sport::create(['name' => 'Basket', 'slug' => 'basket']);
        $venue = Venue::create([
            'manager_id'  => $manager->id,
            'name'        => 'Review Venue',
            'slug'        => 'review-venue',
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
                'close_time'  => '22:00',
            ]);
        }

        $this->court = Court::create([
            'venue_id'       => $venue->id,
            'sport_id'       => $sport->id,
            'name'           => 'Court R',
            'price_per_hour' => 80000,
            'is_active'      => true,
        ]);

        $this->service = app(BookingService::class);
    }

    public function test_customer_can_submit_review_for_confirmed_booking(): void
    {
        $date    = now()->addDay()->toDateString();
        $booking = $this->service->createBooking($this->customer, $this->court, $date, ['09:00']);
        $this->service->processPayment($booking);

        // Mark as completed so review is allowed
        $booking->update(['status' => \App\Enums\BookingStatus::Completed]);

        $this->actingAs($this->customer)
            ->post(route('reviews.store', $booking), [
                'rating'  => 5,
                'comment' => 'Lapangan sangat bagus!',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('reviews', [
            'booking_id' => $booking->id,
            'user_id'    => $this->customer->id,
            'rating'     => 5,
        ]);
    }

    public function test_review_cannot_be_submitted_twice(): void
    {
        $date    = now()->addDay()->toDateString();
        $booking = $this->service->createBooking($this->customer, $this->court, $date, ['10:00']);
        $this->service->processPayment($booking);
        $booking->update(['status' => \App\Enums\BookingStatus::Completed]);

        $this->actingAs($this->customer)
            ->post(route('reviews.store', $booking), ['rating' => 4]);

        // Second submission
        $this->actingAs($this->customer)
            ->post(route('reviews.store', $booking), ['rating' => 3])
            ->assertSessionHas('error');
    }

    public function test_other_customer_cannot_review_booking(): void
    {
        $date    = now()->addDay()->toDateString();
        $booking = $this->service->createBooking($this->customer, $this->court, $date, ['11:00']);
        $this->service->processPayment($booking);
        $booking->update(['status' => \App\Enums\BookingStatus::Completed]);

        $other = User::factory()->create();

        $this->actingAs($other)
            ->post(route('reviews.store', $booking), ['rating' => 1])
            ->assertStatus(403);
    }
}
