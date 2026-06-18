<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InitialServiceSystemSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $studentUserId = DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->where('roles.role_name', 'student')
            ->value('users.id');

        $libraryStaffUserId = DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->where('roles.role_name', 'library_staff')
            ->value('users.id');

        if (!$studentUserId) {
            throw new \Exception('No student user found. Make sure DemoUserSeeder runs before InitialServiceSystemSeeder.');
        }

        if (!$libraryStaffUserId) {
            throw new \Exception('No library staff user found. Make sure DemoUserSeeder runs before InitialServiceSystemSeeder.');
        }

        $studentRoleId = DB::table('roles')
            ->where('role_name', 'student')
            ->value('id');

        $employeeRoleId = DB::table('roles')
            ->where('role_name', 'employee')
            ->value('id');

        $libraryStaffRoleId = DB::table('roles')
            ->where('role_name', 'library_staff')
            ->value('id');

        $pendingRequestId = DB::table('request_statuses')
            ->where('status_key', 'pending')
            ->value('id');

        $approvedRequestId = DB::table('request_statuses')
            ->where('status_key', 'approved')
            ->value('id');

        $discussionRoomId = DB::table('facilities')
            ->where('facility_key', 'discussion_room')
            ->value('id');

        $generalCategoryId = DB::table('categories')
            ->where('category_key', 'general_references')
            ->value('id');

        $ebookMaterialTypeId = DB::table('material_types')
            ->where('material_type_key', 'ebook')
            ->value('id');

        if (!$studentRoleId || !$employeeRoleId || !$libraryStaffRoleId) {
            throw new \Exception('Missing role lookup data.');
        }

        if (!$pendingRequestId || !$approvedRequestId) {
            throw new \Exception('Missing request status lookup data.');
        }

        if (!$discussionRoomId) {
            throw new \Exception('Missing discussion room facility. Make sure the facilities seeder runs before InitialServiceSystemSeeder.');
        }

        if (!$generalCategoryId || !$ebookMaterialTypeId) {
            throw new \Exception('Missing category or material type lookup data.');
        }

        DB::table('referral_requests')->insert([
            'user_id' => $studentUserId,
            'destination_library' => 'National Library of the Philippines',
            'purpose' => 'Academic research for undergraduate thesis.',
            'material_needed' => 'Computer science and software engineering references.',
            'status_id' => $pendingRequestId,
            'requested_at' => $now,
            'processed_by' => null,
            'processed_at' => null,
            'approved_letter_path' => null,
            'remarks' => 'Sample referral request.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('facility_reservations')->insert([
            'user_id' => $studentUserId,
            'facility_id' => $discussionRoomId,
            'status_id' => $approvedRequestId,
            'reservation_date' => $now->copy()->addDays(3)->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'purpose' => 'Group study and thesis consultation preparation.',
            'participants_count' => 5,
            'processed_by' => $libraryStaffUserId,
            'processed_at' => $now,
            'remarks' => 'Sample approved facility reservation.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $onlineResourceIds = [];

        $onlineResourceIds[] = DB::table('online_resources')->insertGetId([
            'title' => 'PUP University Library and Learning Resources Center',
            'description' => 'Official PUP library portal for services, guidelines, and information.',
            'url' => 'https://www.pup.edu.ph/library/',
            'category_id' => $generalCategoryId,
            'material_type_id' => $ebookMaterialTypeId,
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $onlineResourceIds[] = DB::table('online_resources')->insertGetId([
            'title' => 'PUP Library Contact Information',
            'description' => 'Official contact information for library offices and sections.',
            'url' => 'https://www.pup.edu.ph/library/contactinfo',
            'category_id' => $generalCategoryId,
            'material_type_id' => $ebookMaterialTypeId,
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $onlineResourceIds[] = DB::table('online_resources')->insertGetId([
            'title' => 'Open Library',
            'description' => 'Open online library catalog useful for book discovery and reference.',
            'url' => 'https://openlibrary.org/',
            'category_id' => $generalCategoryId,
            'material_type_id' => $ebookMaterialTypeId,
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        foreach ($onlineResourceIds as $onlineResourceId) {
            foreach ([$studentRoleId, $employeeRoleId, $libraryStaffRoleId] as $roleId) {
                DB::table('online_resource_roles')->insert([
                    'online_resource_id' => $onlineResourceId,
                    'role_id' => $roleId,
                ]);
            }
        }

        DB::table('notifications')->insert([
            [
                'user_id' => $studentUserId,
                'title' => 'Reservation Approved',
                'message' => 'Your sample reservation has been approved. Please claim it before the expiry date.',
                'notification_type' => 'reservation_approved',
                'is_read' => false,
                'read_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => $studentUserId,
                'title' => 'Borrowed Book Due Soon',
                'message' => 'Your borrowed book is due soon. Please return or request renewal before the due date.',
                'notification_type' => 'return_due_soon',
                'is_read' => false,
                'read_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        DB::table('audit_logs')->insert([
            'user_id' => $libraryStaffUserId,
            'action' => 'seed_initial_data',
            'table_name' => 'database',
            'record_id' => null,
            'old_values' => null,
            'new_values' => json_encode(['message' => 'Initial library system data seeded.']),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Laravel Seeder',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('system_settings')->updateOrInsert(
            ['setting_key' => 'library_service_hours_weekdays'],
            [
                'setting_value' => '8:00 AM - 8:00 PM',
                'display_setting_name' => 'Library Service Hours on Weekdays',
                'description' => 'Default displayed library service hours for weekdays.',
                'updated_by' => $libraryStaffUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        DB::table('system_settings')->updateOrInsert(
            ['setting_key' => 'library_service_hours_saturday'],
            [
                'setting_value' => '8:00 AM - 5:00 PM',
                'display_setting_name' => 'Library Service Hours on Saturday',
                'description' => 'Default displayed library service hours for Saturdays.',
                'updated_by' => $libraryStaffUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }
}