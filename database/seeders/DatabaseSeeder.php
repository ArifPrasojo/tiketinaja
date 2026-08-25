<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SportSeeder::class,
            DemoSeeder::class,
            PromoSeeder::class,   // ensures platform-wide promos exist
        ]);
    }
}
