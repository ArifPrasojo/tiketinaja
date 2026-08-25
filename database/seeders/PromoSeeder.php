<?php

namespace Database\Seeders;

use App\Models\Promo;
use App\Models\Venue;
use Illuminate\Database\Seeder;

class PromoSeeder extends Seeder
{
    public function run(): void
    {
        // Platform-wide promos (upsert agar aman dijalankan berkali-kali)
        Promo::firstOrCreate(['code' => 'TIKETIN10'], [
            'venue_id'     => null,
            'description'  => 'Diskon 10% untuk semua booking (maks Rp 50.000)',
            'type'         => 'percentage',
            'value'        => 10,
            'max_discount' => 50000,
            'max_uses'     => 100,
            'is_active'    => true,
        ]);

        Promo::firstOrCreate(['code' => 'NEWUSER25'], [
            'venue_id'           => null,
            'description'        => 'Diskon Rp 25.000 untuk pengguna baru (min. booking Rp 100.000)',
            'type'               => 'fixed',
            'value'              => 25000,
            'min_booking_amount' => 100000,
            'max_uses'           => 50,
            'is_active'          => true,
        ]);

        // Venue-specific promo
        $firstVenue = Venue::first();
        if ($firstVenue) {
            Promo::firstOrCreate(['code' => 'GBK20'], [
                'venue_id'     => $firstVenue->id,
                'description'  => 'Diskon 20% khusus ' . $firstVenue->name,
                'type'         => 'percentage',
                'value'        => 20,
                'max_discount' => 100000,
                'max_uses'     => 30,
                'expires_at'   => now()->addMonth(),
                'is_active'    => true,
            ]);
        }
    }
}
