<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
    LookupSeeder::class,
    DemoUserSeeder::class,
    SampleResourceSeeder::class,
    FacilitySeeder::class,
    InitialTransactionSeeder::class,
    InitialServiceSystemSeeder::class,
]);
    }
}
