<?php

namespace Database\Seeders;

use App\Models\{Court, OperatingHour, Promo, Sport, User, Venue};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SingleVenueSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $users = [];
            foreach ([
                ['admin', 'Admin Demo', 'admin'],
                ['manager', 'Pengelola GOR Demo', 'manager'],
                ['customer', 'Dimas Pemain Demo', 'customer'],
                ['customer2', 'Nadia Pemain Demo', 'customer'],
            ] as [$login, $name, $role]) {
                $users[$login] = User::updateOrCreate(['email' => $login . '@tiketinaja.test'], [
                    'name' => $name,
                    'role' => $role,
                    'password' => Hash::make('password'),
                    'phone' => '08120000000' . count($users),
                ]);
                $users[$login]->forceFill(['email_verified_at' => now()])->save();
            }

            $venue = Venue::updateOrCreate(['slug' => 'gor-tiketinaja-demo'], [
                'manager_id' => $users['manager']->id,
                'name' => 'GOR TiketinAja (Demo)',
                'description' => 'Gedung olahraga dummy untuk pengujian pemesanan Futsal, Badminton, dan Basket.',
                'address' => 'Jl. Olahraga Demo No. 1',
                'city' => 'Bandung',
                'phone' => '081200001000',
                'is_active' => true,
            ]);

            for ($day = 0; $day < 7; $day++) {
                OperatingHour::updateOrCreate(['venue_id' => $venue->id, 'day_of_week' => $day], [
                    'open_time' => '07:00', 'close_time' => '22:00', 'is_closed' => false,
                ]);
            }

            foreach ([
                ['Futsal', 'futsal', 'sports_soccer', 150000, ['Rumput sintetis', 'Gawang', 'Ruang ganti']],
                ['Badminton', 'badminton', 'sports_tennis', 60000, ['Lantai vinyl', 'Net', 'Ruang ganti']],
                ['Basket', 'basket', 'sports_basketball', 120000, ['Ring basket', 'Papan skor', 'Ruang ganti']],
            ] as [$name, $slug, $icon, $price, $facilities]) {
                $sport = Sport::updateOrCreate(['slug' => $slug], ['name' => $name, 'icon' => $icon]);
                Court::updateOrCreate(['venue_id' => $venue->id, 'name' => 'Lapangan ' . $name], [
                    'sport_id' => $sport->id,
                    'description' => 'Lapangan ' . $name . ' indoor, data dummy untuk pengujian.',
                    'price_per_hour' => $price,
                    'facilities' => $facilities,
                    'is_active' => true,
                ]);
            }

            foreach ([
                ['DEMO10', 'percentage', 10, 0, 30000],
                ['GORBARU25', 'fixed', 25000, 100000, null],
            ] as [$code, $type, $value, $minimum, $maximum]) {
                Promo::updateOrCreate(['code' => $code], [
                    'venue_id' => $venue->id,
                    'description' => $type === 'percentage' ? 'Promo demo diskon 10%, maksimal Rp 30.000.' : 'Promo demo potongan Rp 25.000, minimum Rp 100.000.',
                    'type' => $type, 'value' => $value,
                    'min_booking_amount' => $minimum, 'max_discount' => $maximum,
                    'max_uses' => 100, 'used_count' => 0,
                    'starts_at' => now()->subDay(), 'expires_at' => now()->addMonth(), 'is_active' => true,
                ]);
            }

        });
    }
}
