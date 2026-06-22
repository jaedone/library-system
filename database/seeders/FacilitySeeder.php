<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('facilities')->insert([
            [
                'facility_key' => 'discussion_room',
                'facility_name' => 'Discussion Room',
                'description' => 'A private room for group discussions, thesis consultations, meetings, and academic collaboration.',
                'capacity' => 8,
                'location' => 'Main Library',
                'availability_days' => 'Monday to Saturday',
                'availability_hours' => '8:00 AM – 5:00 PM',
                'equipment' => json_encode([
                    'Conference Table',
                    'Chairs',
                    'Whiteboard',
                    'Power Outlets',
                ]),
                'usage_for' => json_encode([
                    'Group Meetings',
                    'Thesis Consultation',
                    'Research Discussion',
                    'Academic Collaboration',
                ]),
                'image_path' => 'images/facilities/discussion-room.jpg',
                'is_active' => true,
            ],
            [
                'facility_key' => 'table_desk_reservation',
                'facility_name' => 'Table / Desk Reservation',
                'description' => 'Reserved study tables and desks for students who need a quiet space for reading, research, and academic work.',
                'capacity' => 1,
                'location' => 'Reading Area',
                'availability_days' => 'Monday to Saturday',
                'availability_hours' => '8:00 AM – 6:00 PM',
                'equipment' => json_encode([
                    'Study Desk',
                    'Chair',
                    'Reading Light',
                    'Power Outlet',
                ]),
                'usage_for' => json_encode([
                    'Individual Study',
                    'Review Session',
                    'Research Writing',
                    'Reading',
                ]),
                'image_path' => 'images/facilities/table-desk.jpg',
                'is_active' => true,
            ],
            [
                'facility_key' => 'computer_station',
                'facility_name' => 'Computer Station',
                'description' => 'Computer units for online research, digital resource access, library database searching, and academic encoding.',
                'capacity' => 1,
                'location' => 'Digital Library Area',
                'availability_days' => 'Monday to Friday',
                'availability_hours' => '8:00 AM – 5:00 PM',
                'equipment' => json_encode([
                    'Computer Unit',
                    'Internet Access',
                    'Keyboard',
                    'Mouse',
                ]),
                'usage_for' => json_encode([
                    'Online Research',
                    'Digital Resource Access',
                    'Academic Encoding',
                    'Library Database Search',
                ]),
                'image_path' => 'images/facilities/computer-area.jpg',
                'is_active' => true,
            ],
            [
                'facility_key' => 'reading_area',
                'facility_name' => 'Reading Area',
                'description' => 'A shared reading space for quiet study, academic review, research, and personal learning.',
                'capacity' => 50,
                'location' => 'Main Library',
                'availability_days' => 'Monday to Saturday',
                'availability_hours' => '8:00 AM – 6:00 PM',
                'equipment' => json_encode([
                    'Reading Tables',
                    'Chairs',
                    'Open Study Space',
                    'Book Shelves',
                ]),
                'usage_for' => json_encode([
                    'Reading',
                    'Quiet Study',
                    'Research',
                    'Academic Review',
                ]),
                'image_path' => 'images/facilities/reading-area.jpg',
                'is_active' => true,
            ],
        ]);
    }
}