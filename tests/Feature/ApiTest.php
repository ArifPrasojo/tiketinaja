<?php

namespace Tests\Feature;

use App\Models\Court;
use App\Models\OperatingHour;
use App\Models\Sport;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private Court $court;
    private Venue $venue;

    protected function setUp(): void
    {
        parent::setUp();

        $manager        = User::factory()->manager()->create();
        $this->customer = User::factory()->create();

        $sport = Sport::create(['name' => 'Mini Soccer', 'slug' => 'mini-soccer']);
        $this->venue = Venue::create([
            'manager_id'  => $manager->id,
            'name'        => 'API Venue',
            'slug'        => 'api-venue',
            'address'     => 'Addr',
            'city'        => 'Jakarta',
            'phone'       => '081',
            'description' => 'Desc',
            'is_active'   => true,
        ]);

        for ($i = 0; $i <= 6; $i++) {
            OperatingHour::create([
                'venue_id'    => $this->venue->id,
                'day_of_week' => $i,
                'open_time'   => '08:00',
                'close_time'  => '22:00',
            ]);
        }

        $this->court = Court::create([
            'venue_id'       => $this->venue->id,
            'sport_id'       => $sport->id,
            'name'           => 'API Court',
            'price_per_hour' => 90000,
            'is_active'      => true,
        ]);
    }

    // ── Auth ────────────────────────────────────────────────────────────────

    public function test_api_login_returns_token(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email'    => $this->customer->email,
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'role']]);
    }

    public function test_api_register_creates_customer(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name'                  => 'New User',
            'email'                 => 'newuser@test.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertCreated()
            ->assertJsonStructure(['token', 'user']);

        $this->assertDatabaseHas('users', ['email' => 'newuser@test.com', 'role' => 'customer']);
    }

    public function test_api_invalid_credentials_returns_422(): void
    {
        $this->postJson('/api/auth/login', [
            'email'    => $this->customer->email,
            'password' => 'wrongpassword',
        ])->assertStatus(422);
    }

    // ── Venues ───────────────────────────────────────────────────────────────

    public function test_api_venues_list_is_publicly_accessible(): void
    {
        $this->getJson('/api/venues')
            ->assertOk()
            ->assertJsonStructure(['data', 'current_page', 'total']);
    }

    public function test_api_venue_show_includes_courts_and_hours(): void
    {
        $this->getJson("/api/venues/{$this->venue->id}")
            ->assertOk()
            ->assertJsonPath('name', 'API Venue')
            ->assertJsonStructure(['courts', 'operating_hours']);
    }

    public function test_api_availability_returns_slots(): void
    {
        $date = now()->addDay()->toDateString();

        $this->getJson("/api/courts/{$this->court->id}/availability?date={$date}")
            ->assertOk()
            ->assertJsonStructure(['court', 'date', 'slots']);
    }

    // ── Bookings ─────────────────────────────────────────────────────────────

    public function test_api_create_booking_requires_auth(): void
    {
        $this->postJson('/api/bookings', [
            'court_id'   => $this->court->id,
            'date'       => now()->addDay()->toDateString(),
            'time_slots' => ['09:00'],
        ])->assertUnauthorized();
    }

    public function test_api_authenticated_user_can_create_booking(): void
    {
        $token = $this->customer->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/bookings', [
            'court_id'   => $this->court->id,
            'date'       => now()->addDay()->toDateString(),
            'time_slots' => ['09:00'],
        ]);

        $response->assertCreated()
            ->assertJsonPath('booking.total_amount', 90000);
    }

    public function test_api_booking_list_is_scoped_to_user(): void
    {
        $token = $this->customer->createToken('test')->plainTextToken;

        // Create booking for customer
        $this->withToken($token)->postJson('/api/bookings', [
            'court_id'   => $this->court->id,
            'date'       => now()->addDay()->toDateString(),
            'time_slots' => ['10:00'],
        ]);

        // List bookings — should see exactly 1
        $this->withToken($token)
            ->getJson('/api/bookings')
            ->assertOk()
            ->assertJsonPath('total', 1);
    }

    public function test_api_me_returns_authenticated_user(): void
    {
        $token = $this->customer->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/auth/me')
            ->assertOk()
            ->assertJsonPath('email', $this->customer->email);
    }

    public function test_api_logout_revokes_token(): void
    {
        $token = $this->customer->createToken('test')->plainTextToken;

        $this->withToken($token)->postJson('/api/auth/logout')->assertOk();

        // Verify token was deleted from database
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
