<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('facilities')->insert([
            [
                'facility_key' => 'discussion_room',
                'facility_name' => 'Discussion Room',
                'description' => 'Room for group discussion and consultation.',
                'capacity' => 8,
                'location' => 'Main Library',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'facility_key' => 'table_desk_reservation',
                'facility_name' => 'Table / Desk Reservation',
                'description' => 'Reserved study table or desk.',
                'capacity' => 1,
                'location' => 'Reading Area',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'facility_key' => 'computer_station',
                'facility_name' => 'Computer Station',
                'description' => 'Computer unit for research and academic work.',
                'capacity' => 1,
                'location' => 'Digital Library Area',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'facility_key' => 'reading_area',
                'facility_name' => 'Reading Area',
                'description' => 'General library reading area.',
                'capacity' => 50,
                'location' => 'Main Library',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}