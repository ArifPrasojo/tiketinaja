<?php

namespace Database\Seeders;

use App\Models\Sport;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SportSeeder extends Seeder
{
    public function run(): void
    {
        $sports = ['Futsal', 'Badminton', 'Basket', 'Tenis', 'Mini Soccer'];

        foreach ($sports as $sport) {
            Sport::firstOrCreate([
                'name' => $sport,
                'slug' => Str::slug($sport),
            ]);
        }
    }
}
