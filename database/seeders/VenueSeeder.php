<?php

namespace Database\Seeders;

use App\Models\Court;
use App\Models\OperatingHour;
use App\Models\Sport;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VenueSeeder extends Seeder
{
    public function run(): void
    {
        $manager = User::where('role', 'manager')->first();
        $futsal = Sport::where('name', 'Futsal')->first();
        $badminton = Sport::where('name', 'Badminton')->first();

        // Venue 1
        $venue1 = Venue::firstOrCreate([
            'slug' => 'gelora-bung-karno-arena'
        ], [
            'manager_id' => $manager->id,
            'name' => 'Gelora Bung Karno Arena',
            'description' => 'Fasilitas olahraga terlengkap di pusat kota.',
            'address' => 'Jl. Pintu Satu Senayan',
            'city' => 'Jakarta Pusat',
            'phone' => '08111222333',
        ]);

        for ($i = 0; $i <= 6; $i++) {
            OperatingHour::firstOrCreate([
                'venue_id' => $venue1->id,
                'day_of_week' => $i,
            ], [
                'open_time' => '08:00',
                'close_time' => '23:00',
            ]);
        }

        Court::firstOrCreate([
            'venue_id' => $venue1->id,
            'name' => 'Lapangan Futsal A (Sintetis)',
        ], [
            'sport_id' => $futsal->id,
            'price_per_hour' => 150000,
            'facilities' => ['Bola', 'Rompi', 'Papan Skor'],
        ]);

        Court::firstOrCreate([
            'venue_id' => $venue1->id,
            'name' => 'Lapangan Futsal B (Vinyl)',
        ], [
            'sport_id' => $futsal->id,
            'price_per_hour' => 175000,
            'facilities' => ['Bola', 'Rompi', 'AC'],
        ]);

        // Venue 2
        $venue2 = Venue::firstOrCreate([
            'slug' => 'tangkas-sports-centre'
        ], [
            'manager_id' => $manager->id,
            'name' => 'Tangkas Sports Centre',
            'description' => 'GOR Bulutangkis standar nasional.',
            'address' => 'Jl. Tanjung Duren Raya',
            'city' => 'Jakarta Barat',
            'phone' => '08222333444',
        ]);

        for ($i = 0; $i <= 6; $i++) {
            OperatingHour::firstOrCreate([
                'venue_id' => $venue2->id,
                'day_of_week' => $i,
            ], [
                'open_time' => '06:00',
                'close_time' => '22:00',
            ]);
        }

        for ($i = 1; $i <= 3; $i++) {
            Court::firstOrCreate([
                'venue_id' => $venue2->id,
                'name' => "Lapangan Badminton $i",
            ], [
                'sport_id' => $badminton->id,
                'price_per_hour' => 60000,
                'facilities' => ['Karpet Badminton', 'Net'],
            ]);
        }
    }
}
