<?php

namespace Tests\Feature;

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

class PromoTest extends TestCase
{
    use RefreshDatabase;

    private User $manager;
    private User $customer;
    private Venue $venue;

    protected function setUp(): void
    {
        parent::setUp();

        $this->manager  = User::factory()->manager()->create();
        $this->customer = User::factory()->create();

        $sport = Sport::create(['name' => 'Badminton', 'slug' => 'badminton']);
        $this->venue = Venue::create([
            'manager_id'  => $this->manager->id,
            'name'        => 'Test Venue',
            'slug'        => 'test-venue',
            'address'     => 'Addr',
            'city'        => 'Kota',
            'phone'       => '0811',
            'description' => 'Desc',
        ]);

        for ($i = 0; $i <= 6; $i++) {
            OperatingHour::create([
                'venue_id'    => $this->venue->id,
                'day_of_week' => $i,
                'open_time'   => '07:00',
                'close_time'  => '22:00',
            ]);
        }
    }

    public function test_manager_can_create_promo(): void
    {
        $this->actingAs($this->manager)
            ->post(route('manage.promos.store'), [
                'code'        => 'MANAGER20',
                'description' => 'Diskon 20%',
                'type'        => 'percentage',
                'value'       => 20,
                'venue_id'    => $this->venue->id,
                'is_active'   => true,
            ])
            ->assertRedirect(route('manage.promos.index'));

        $this->assertDatabaseHas('promos', ['code' => 'MANAGER20']);
    }

    public function test_promo_validate_endpoint_returns_discount(): void
    {
        Promo::create([
            'code'        => 'WEB10',
            'description' => '10%',
            'type'        => 'percentage',
            'value'       => 10,
            'max_discount' => 20000,
            'is_active'   => true,
        ]);

        $this->actingAs($this->customer)
            ->postJson(route('promos.validate'), [
                'code'     => 'WEB10',
                'amount'   => 100000,
                'venue_id' => $this->venue->id,
            ])
            ->assertOk()
            ->assertJson([
                'valid'           => true,
                'discount_amount' => 10000,
                'final_amount'    => 90000,
            ]);
    }

    public function test_expired_promo_is_rejected(): void
    {
        Promo::create([
            'code'       => 'EXPIRED',
            'description'=> 'Sudah expired',
            'type'       => 'fixed',
            'value'      => 10000,
            'expires_at' => now()->subDay(),
            'is_active'  => true,
        ]);

        $this->actingAs($this->customer)
            ->postJson(route('promos.validate'), [
                'code'     => 'EXPIRED',
                'amount'   => 100000,
                'venue_id' => $this->venue->id,
            ])
            ->assertJson(['valid' => false]);
    }

    public function test_promo_max_uses_is_enforced(): void
    {
        $promo = Promo::create([
            'code'      => 'MAXONE',
            'description'=> 'Max 1 use',
            'type'      => 'fixed',
            'value'     => 5000,
            'max_uses'  => 1,
            'used_count'=> 1,
            'is_active' => true,
        ]);

        $this->actingAs($this->customer)
            ->postJson(route('promos.validate'), [
                'code'     => 'MAXONE',
                'amount'   => 100000,
                'venue_id' => $this->venue->id,
            ])
            ->assertJson(['valid' => false]);
    }
}
