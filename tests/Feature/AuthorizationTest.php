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
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private User $manager1;
    private User $manager2;
    private User $customer;
    private User $admin;
    private Venue $venue1;
    private Venue $venue2;
    private Court $court1;
    private Court $court2;
    private BookingService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin    = User::factory()->admin()->create();
        $this->manager1 = User::factory()->manager()->create();
        $this->manager2 = User::factory()->manager()->create();
        $this->customer = User::factory()->create();

        $sport = Sport::create(['name' => 'Futsal', 'slug' => 'futsal']);

        $this->venue1 = Venue::create([
            'manager_id' => $this->manager1->id, 'name' => 'Venue A', 'slug' => 'venue-a',
            'address' => 'Addr', 'city' => 'Kota', 'phone' => '0811', 'description' => 'D',
        ]);
        $this->venue2 = Venue::create([
            'manager_id' => $this->manager2->id, 'name' => 'Venue B', 'slug' => 'venue-b',
            'address' => 'Addr', 'city' => 'Kota', 'phone' => '0812', 'description' => 'D',
        ]);

        foreach ([$this->venue1, $this->venue2] as $v) {
            for ($i = 0; $i <= 6; $i++) {
                OperatingHour::create(['venue_id' => $v->id, 'day_of_week' => $i, 'open_time' => '08:00', 'close_time' => '22:00']);
            }
        }

        $this->court1 = Court::create(['venue_id' => $this->venue1->id, 'sport_id' => $sport->id, 'name' => 'C1', 'price_per_hour' => 100000, 'is_active' => true]);
        $this->court2 = Court::create(['venue_id' => $this->venue2->id, 'sport_id' => $sport->id, 'name' => 'C2', 'price_per_hour' => 100000, 'is_active' => true]);

        $this->service = app(BookingService::class);
    }

    // ── Booking authorization ───────────────────────────────────────────────

    public function test_manager_cannot_confirm_booking_from_another_venue(): void
    {
        $date    = now()->addDay()->toDateString();
        $booking = $this->service->createBooking($this->customer, $this->court2, $date, ['09:00']);

        // manager1 tries to confirm a booking from venue2 (owned by manager2)
        $this->actingAs($this->manager1)
            ->post(route('manage.bookings.confirm', $booking))
            ->assertForbidden();
    }

    public function test_manager_cannot_view_booking_from_another_venue(): void
    {
        $date    = now()->addDay()->toDateString();
        $booking = $this->service->createBooking($this->customer, $this->court2, $date, ['10:00']);

        $this->actingAs($this->manager1)
            ->get(route('manage.bookings.show', $booking))
            ->assertForbidden();
    }

    public function test_manager_can_confirm_own_venue_booking(): void
    {
        $date    = now()->addDay()->toDateString();
        $booking = $this->service->createBooking($this->customer, $this->court1, $date, ['11:00']);

        $this->actingAs($this->manager1)
            ->post(route('manage.bookings.confirm', $booking))
            ->assertRedirect();

        $this->assertEquals(BookingStatus::Confirmed, $booking->fresh()->status);
    }

    // ── Admin user management ───────────────────────────────────────────────

    public function test_admin_cannot_change_own_role(): void
    {
        $this->actingAs($this->admin)
            ->put(route('admin.users.update', $this->admin), ['role' => 'customer'])
            ->assertRedirect();

        // Role must remain admin
        $this->assertEquals('admin', $this->admin->fresh()->role);
    }

    public function test_admin_cannot_delete_other_admin(): void
    {
        $anotherAdmin = User::factory()->admin()->create();

        $this->actingAs($this->admin)
            ->delete(route('admin.users.destroy', $anotherAdmin))
            ->assertRedirect();

        // Other admin still exists
        $this->assertDatabaseHas('users', ['id' => $anotherAdmin->id]);
    }

    public function test_admin_can_search_users(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.users.index', ['search' => $this->customer->name]))
            ->assertOk()
            ->assertSee($this->customer->name);
    }

    public function test_admin_can_filter_users_by_role(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.users.index', ['role' => 'manager']))
            ->assertOk()
            ->assertSee($this->manager1->name)
            ->assertDontSee($this->customer->name);
    }

    // ── Booking pay expiry guard ────────────────────────────────────────────

    public function test_cannot_pay_expired_booking_via_policy(): void
    {
        $date    = now()->addDay()->toDateString();
        $booking = $this->service->createBooking($this->customer, $this->court1, $date, ['12:00']);

        // Force expire the booking
        $booking->update(['expires_at' => now()->subMinute()]);
        $booking->refresh();

        $this->actingAs($this->customer)
            ->post(route('bookings.pay', $booking))
            ->assertForbidden();
    }

    // ── Promo manager scope ─────────────────────────────────────────────────

    public function test_manager_cannot_create_global_promo(): void
    {
        $this->actingAs($this->manager1)
            ->post(route('manage.promos.store'), [
                'code'        => 'GLOBAL99',
                'description' => 'Global promo by manager',
                'type'        => 'fixed',
                'value'       => 10000,
                'is_active'   => true,
                // No venue_id — should fail for manager
            ])
            ->assertSessionHasErrors('venue_id');
    }

    public function test_manager_cannot_create_promo_for_another_venue(): void
    {
        $this->actingAs($this->manager1)
            ->post(route('manage.promos.store'), [
                'venue_id'    => $this->venue2->id, // venue owned by manager2
                'code'        => 'WRONG99',
                'description' => 'Promo for wrong venue',
                'type'        => 'fixed',
                'value'       => 10000,
                'is_active'   => true,
            ])
            ->assertForbidden();
    }

    public function test_manager_can_create_promo_for_own_venue(): void
    {
        $this->actingAs($this->manager1)
            ->post(route('manage.promos.store'), [
                'venue_id'    => $this->venue1->id,
                'code'        => 'MYPROMO',
                'description' => 'Promo for my venue',
                'type'        => 'fixed',
                'value'       => 15000,
                'is_active'   => true,
            ])
            ->assertRedirect(route('manage.promos.index'));

        $this->assertDatabaseHas('promos', ['code' => 'MYPROMO', 'venue_id' => $this->venue1->id]);
    }

    // ── Review only for completed bookings ─────────────────────────────────

    public function test_review_rejected_for_confirmed_booking(): void
    {
        $date    = now()->addDay()->toDateString();
        $booking = $this->service->createBooking($this->customer, $this->court1, $date, ['13:00']);
        $this->service->processPayment($booking);

        $this->actingAs($this->customer)
            ->post(route('reviews.store', $booking), ['rating' => 5])
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('reviews', ['booking_id' => $booking->id]);
    }

    public function test_review_allowed_for_completed_booking(): void
    {
        $date    = now()->addDay()->toDateString();
        $booking = $this->service->createBooking($this->customer, $this->court1, $date, ['14:00']);
        $this->service->processPayment($booking);
        $booking->update(['status' => BookingStatus::Completed]);

        $this->actingAs($this->customer)
            ->post(route('reviews.store', $booking), ['rating' => 4, 'comment' => 'Good!'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('reviews', ['booking_id' => $booking->id, 'rating' => 4]);
    }
}
