<?php

namespace Tests\Feature;

use App\Models\{Booking, Court, OperatingHour, ReservedSlot, Sport, User, Venue};
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SingleVenueSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_contains_one_building_and_three_distinct_sports(): void
    {
        $this->travelTo(now()->setTime(10, 30));
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(1, Venue::count());
        $this->assertSame(3, Court::count());
        $this->assertSame(3, Sport::count());
        $this->assertSame(3, Court::distinct()->count('sport_id'));
        $this->assertSame(7, OperatingHour::count());
        $this->assertSame(4, User::count());
        $this->assertSame(0, Booking::count());
        $this->assertSame(0, ReservedSlot::count());
        $this->assertDatabaseCount('payments', 0);
        $this->assertDatabaseCount('reviews', 0);

        $this->seed(DatabaseSeeder::class);
        $this->assertSame(1, Venue::count());
        $this->assertSame(3, Court::count());
        $this->assertSame(0, Booking::count());
    }
}
