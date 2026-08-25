<?php

namespace Tests\Unit;

use App\Models\Promo;
use Carbon\Carbon;
use Tests\TestCase;

class PromoModelTest extends TestCase
{
    private function makePromo(array $overrides = []): Promo
    {
        // Build a non-persisted Promo with raw attribute values
        $attrs = array_merge([
            'code'               => 'TEST',
            'description'        => 'Test promo',
            'type'               => 'fixed',
            'value'              => 10000,
            'max_uses'           => null,
            'used_count'         => 0,
            'min_booking_amount' => 0,
            'max_discount'       => null,
            'starts_at'          => null,
            'expires_at'         => null,
            'is_active'          => true,
        ], $overrides);

        $promo = new Promo();

        // Set raw attributes without touching DB casts
        foreach ($attrs as $key => $val) {
            $promo->$key = $val;
        }

        return $promo;
    }

    public function test_active_promo_is_valid(): void
    {
        $promo = $this->makePromo();
        $this->assertTrue($promo->isValid());
    }

    public function test_inactive_promo_is_not_valid(): void
    {
        $promo = $this->makePromo(['is_active' => false]);
        $this->assertFalse($promo->isValid());
    }

    public function test_expired_promo_is_not_valid(): void
    {
        $promo = $this->makePromo(['expires_at' => Carbon::yesterday()]);
        $this->assertFalse($promo->isValid());
    }

    public function test_not_started_promo_is_not_valid(): void
    {
        $promo = $this->makePromo(['starts_at' => Carbon::tomorrow()]);
        $this->assertFalse($promo->isValid());
    }

    public function test_maxed_out_promo_is_not_valid(): void
    {
        $promo = $this->makePromo(['max_uses' => 5, 'used_count' => 5]);
        $this->assertFalse($promo->isValid());
    }

    public function test_fixed_discount_calculation(): void
    {
        $promo = $this->makePromo(['type' => 'fixed', 'value' => 25000]);
        $this->assertEquals(25000, $promo->calculateDiscount(100000));
    }

    public function test_fixed_discount_cannot_exceed_amount(): void
    {
        $promo = $this->makePromo(['type' => 'fixed', 'value' => 50000]);
        $this->assertEquals(30000, $promo->calculateDiscount(30000));
    }

    public function test_percentage_discount_calculation(): void
    {
        $promo = $this->makePromo(['type' => 'percentage', 'value' => 20]);
        $this->assertEquals(20000, $promo->calculateDiscount(100000));
    }

    public function test_percentage_discount_is_capped(): void
    {
        $promo = $this->makePromo(['type' => 'percentage', 'value' => 20, 'max_discount' => 15000]);
        $this->assertEquals(15000, $promo->calculateDiscount(100000));
    }

    public function test_discount_zero_when_below_min_amount(): void
    {
        $promo = $this->makePromo(['type' => 'fixed', 'value' => 10000, 'min_booking_amount' => 100000]);
        $this->assertEquals(0, $promo->calculateDiscount(50000));
    }
}
