<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LookupSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'role_name' => 'student',
                'display_role_name' => 'Student',
                'description' => 'Currently studying in PUP',
            ],
            [
                'role_name' => 'employee',
                'display_role_name' => 'Employee',
                'description' => 'PUP faculty or staff employee',
            ],
            [
                'role_name' => 'alumni',
                'display_role_name' => 'Alumni',
                'description' => 'Former PUP student',
            ],
            [
                'role_name' => 'visitor',
                'display_role_name' => 'Visitor',
                'description' => 'External researcher or library visitor',
            ],
            [
                'role_name' => 'library_staff',
                'display_role_name' => 'Library Staff',
                'description' => 'Library personnel with staff/admin access',
            ],
        ]);

        DB::table('account_statuses')->insert([
            ['status_key' => 'pending', 'status_name' => 'Pending', 'description' => 'Account is waiting for verification'],
            ['status_key' => 'approved', 'status_name' => 'Approved', 'description' => 'Account is approved and active'],
            ['status_key' => 'rejected', 'status_name' => 'Rejected', 'description' => 'Account registration was rejected'],
            ['status_key' => 'suspended', 'status_name' => 'Suspended', 'description' => 'Account is temporarily disabled'],
            ['status_key' => 'expired', 'status_name' => 'Expired', 'description' => 'Account access has expired'],
        ]);

        $permissions = [
            ['search_catalog', 'Search Catalog', 'Catalog', 'Can search catalog'],
            ['view_resource_availability', 'View Resource Availability', 'Catalog', 'Can view resource availability'],
            ['reserve_book', 'Reserve Book', 'Reservations', 'Can reserve books'],
            ['borrow_book', 'Borrow Book', 'Borrowing', 'Can borrow books'],
            ['renew_book', 'Renew Book', 'Borrowing', 'Can renew books'],
            ['view_own_history', 'View Own History', 'Account', 'Can view own borrowing history'],
            ['view_due_dates_penalties', 'View Due Dates and Penalties', 'Account', 'Can view due dates and penalties'],
            ['request_referral_letter', 'Request Referral Letter', 'Referral', 'Can request referral letters'],
            ['reserve_facility', 'Reserve Facility', 'Facilities', 'Can reserve facilities'],
            ['access_online_resources', 'Access Online Resources', 'Online Resources', 'Can access online resources'],
            ['verify_registrations', 'Verify Registrations', 'Staff', 'Can verify registrations'],
            ['approve_reservations', 'Approve Reservations', 'Reservations', 'Can approve reservations'],
            ['process_borrowing', 'Process Borrowing', 'Borrowing', 'Can process borrowing'],
            ['process_returns', 'Process Returns', 'Returns', 'Can process returns'],
            ['manage_penalties', 'Manage Penalties', 'Penalties', 'Can manage penalties'],
            ['manage_resources', 'Manage Resources', 'Resources', 'Can manage resources'],
            ['generate_reports', 'Generate Reports', 'Reports', 'Can generate reports'],
            ['manage_users', 'Manage Users', 'Users', 'Can manage users'],
            ['manage_settings', 'Manage Settings', 'Settings', 'Can manage settings'],
        ];

        foreach ($permissions as [$key, $display, $module, $description]) {
            DB::table('permissions')->insert([
                'permission_name' => $key,
                'display_permission_name' => $display,
                'module' => $module,
                'description' => $description,
            ]);
        }

        $borrowerPermissions = [
            'search_catalog',
            'view_resource_availability',
            'reserve_book',
            'borrow_book',
            'renew_book',
            'view_own_history',
            'view_due_dates_penalties',
            'request_referral_letter',
            'reserve_facility',
            'access_online_resources',
        ];

        foreach (['student', 'employee'] as $roleName) {
            $roleId = DB::table('roles')->where('role_name', $roleName)->value('id');

            foreach ($borrowerPermissions as $permissionName) {
                DB::table('role_permissions')->insert([
                    'role_id' => $roleId,
                    'permission_id' => DB::table('permissions')->where('permission_name', $permissionName)->value('id'),
                ]);
            }
        }

        foreach (['alumni', 'visitor'] as $roleName) {
            $roleId = DB::table('roles')->where('role_name', $roleName)->value('id');

            foreach (['search_catalog', 'view_resource_availability'] as $permissionName) {
                DB::table('role_permissions')->insert([
                    'role_id' => $roleId,
                    'permission_id' => DB::table('permissions')->where('permission_name', $permissionName)->value('id'),
                ]);
            }
        }

        $libraryStaffRoleId = DB::table('roles')->where('role_name', 'library_staff')->value('id');

        foreach (DB::table('permissions')->pluck('id') as $permissionId) {
            DB::table('role_permissions')->insert([
                'role_id' => $libraryStaffRoleId,
                'permission_id' => $permissionId,
            ]);
        }

        DB::table('colleges')->insert([
            ['college_code' => 'ccis', 'college_name' => 'College of Computer and Information Sciences', 'description' => 'Computer and information science programs'],
            ['college_code' => 'ce', 'college_name' => 'College of Engineering', 'description' => 'Engineering programs'],
            ['college_code' => 'coed', 'college_name' => 'College of Education', 'description' => 'Education programs'],
            ['college_code' => 'cba', 'college_name' => 'College of Business Administration', 'description' => 'Business programs'],
            ['college_code' => 'coc', 'college_name' => 'College of Communication', 'description' => 'Communication programs'],
            ['college_code' => 'cl', 'college_name' => 'College of Law', 'description' => 'Law programs'],
            ['college_code' => 'cthtm', 'college_name' => 'College of Tourism, Hospitality and Transportation Management', 'description' => 'Tourism and hospitality programs'],
        ]);

        DB::table('programs')->insert([
            ['college_id' => DB::table('colleges')->where('college_code', 'ccis')->value('id'), 'program_code' => 'bscs', 'program_name' => 'Bachelor of Science in Computer Science', 'description' => 'Computer Science program'],
            ['college_id' => DB::table('colleges')->where('college_code', 'ccis')->value('id'), 'program_code' => 'bsit', 'program_name' => 'Bachelor of Science in Information Technology', 'description' => 'Information Technology program'],
            ['college_id' => DB::table('colleges')->where('college_code', 'ce')->value('id'), 'program_code' => 'bsce', 'program_name' => 'Bachelor of Science in Civil Engineering', 'description' => 'Civil Engineering program'],
            ['college_id' => DB::table('colleges')->where('college_code', 'ce')->value('id'), 'program_code' => 'bscpe', 'program_name' => 'Bachelor of Science in Computer Engineering', 'description' => 'Computer Engineering program'],
            ['college_id' => DB::table('colleges')->where('college_code', 'coed')->value('id'), 'program_code' => 'bse', 'program_name' => 'Bachelor of Secondary Education', 'description' => 'Secondary Education program'],
            ['college_id' => DB::table('colleges')->where('college_code', 'cba')->value('id'), 'program_code' => 'bsba', 'program_name' => 'Bachelor of Science in Business Administration', 'description' => 'Business Administration program'],
            ['college_id' => DB::table('colleges')->where('college_code', 'coc')->value('id'), 'program_code' => 'bacomm', 'program_name' => 'Bachelor of Arts in Communication', 'description' => 'Communication program'],
            ['college_id' => DB::table('colleges')->where('college_code', 'cthtm')->value('id'), 'program_code' => 'bstm', 'program_name' => 'Bachelor of Science in Tourism Management', 'description' => 'Tourism Management program'],
        ]);

        DB::table('departments')->insert([
            [
                'college_id' => DB::table('colleges')->where('college_code', 'ccis')->value('id'),
                'department_code' => 'computer_science',
                'department_name' => 'Department of Computer Science',
                'description' => 'Academic department under CCIS',
            ],
            [
                'college_id' => DB::table('colleges')->where('college_code', 'ccis')->value('id'),
                'department_code' => 'information_technology',
                'department_name' => 'Department of Information Technology',
                'description' => 'Academic department under CCIS',
            ],
            [
                'college_id' => null,
                'department_code' => 'library_services',
                'department_name' => 'Library Services',
                'description' => 'Library operations',
            ],
            [
                'college_id' => null,
                'department_code' => 'registrar',
                'department_name' => 'Registrar',
                'description' => 'Registrar office',
            ],
            [
                'college_id' => null,
                'department_code' => 'finance',
                'department_name' => 'Finance Office',
                'description' => 'Finance office',
            ],
            [
                'college_id' => null,
                'department_code' => 'it_office',
                'department_name' => 'Information Technology Office',
                'description' => 'IT office',
            ],
        ]);

        DB::table('library_branches')->insert([
            ['branch_code' => 'main_library', 'branch_name' => 'Main Library', 'location' => 'PUP Main Campus', 'description' => 'Main university library'],
            ['branch_code' => 'graduate_library', 'branch_name' => 'Graduate School Library', 'location' => 'Graduate School Area', 'description' => 'Graduate materials library'],
            ['branch_code' => 'law_library', 'branch_name' => 'College of Law Library', 'location' => 'College of Law', 'description' => 'Law materials library'],
            ['branch_code' => 'digital_library', 'branch_name' => 'Digital Library', 'location' => 'Online', 'description' => 'Digital resources'],
        ]);

        DB::table('document_types')->insert([
            ['document_type_key' => 'certificate_of_registration', 'document_type_name' => 'Certificate of Registration', 'description' => 'Student enrollment document'],
            ['document_type_key' => 'pup_id', 'document_type_name' => 'PUP ID', 'description' => 'Official PUP ID'],
            ['document_type_key' => 'employee_id', 'document_type_name' => 'Employee ID', 'description' => 'Official employee ID'],
            ['document_type_key' => 'alumni_id', 'document_type_name' => 'Alumni ID', 'description' => 'Official alumni ID'],
            ['document_type_key' => 'referral_letter', 'document_type_name' => 'Referral Letter', 'description' => 'Referral letter from institution'],
            ['document_type_key' => 'valid_government_id', 'document_type_name' => 'Valid Government ID', 'description' => 'Government ID'],
            ['document_type_key' => 'photo_2x2', 'document_type_name' => '2x2 Photo', 'description' => 'User profile photo'],
        ]);

        DB::table('request_statuses')->insert([
            ['status_key' => 'pending', 'status_name' => 'Pending', 'description' => 'Waiting for action'],
            ['status_key' => 'approved', 'status_name' => 'Approved', 'description' => 'Approved request'],
            ['status_key' => 'rejected', 'status_name' => 'Rejected', 'description' => 'Rejected request'],
            ['status_key' => 'claimed', 'status_name' => 'Claimed', 'description' => 'Claimed reservation'],
            ['status_key' => 'cancelled', 'status_name' => 'Cancelled', 'description' => 'Cancelled request'],
            ['status_key' => 'expired', 'status_name' => 'Expired', 'description' => 'Expired request'],
        ]);

        DB::table('employee_types')->insert([
            [
                'employee_type_key' => 'faculty',
                'employee_type_name' => 'Faculty',
                'description' => 'Teaching or academic employee',
            ],
            [
                'employee_type_key' => 'staff',
                'employee_type_name' => 'Staff',
                'description' => 'Administrative or non-teaching employee',
            ],
        ]);

        DB::table('academic_titles')->insert([
            ['title_key' => 'adjunct', 'title_name' => 'Adjunct', 'display_title' => 'Adjunct'],
            ['title_key' => 'lecturer', 'title_name' => 'Lecturer', 'display_title' => 'Lecturer'],
            ['title_key' => 'instructor', 'title_name' => 'Instructor', 'display_title' => 'Instructor'],
            ['title_key' => 'assistant_professor', 'title_name' => 'Assistant Professor', 'display_title' => 'Assistant Professor'],
            ['title_key' => 'associate_professor', 'title_name' => 'Associate Professor', 'display_title' => 'Associate Professor'],
            ['title_key' => 'professor', 'title_name' => 'Professor', 'display_title' => 'Professor'],
        ]);

        DB::table('divisions')->insert([
            ['division_key' => 'enrollment_management', 'division_name' => 'Enrollment Management'],
            ['division_key' => 'facilities', 'division_name' => 'Facilities'],
            ['division_key' => 'finance', 'division_name' => 'Finance'],
            ['division_key' => 'it', 'division_name' => 'Information Technology'],
            ['division_key' => 'human_resources', 'division_name' => 'Human Resources'],
        ]);

        DB::table('job_titles')->insert([
            ['job_title_key' => 'it_specialist', 'job_title_name' => 'IT Specialist'],
            ['job_title_key' => 'hr_generalist', 'job_title_name' => 'HR Generalist'],
            ['job_title_key' => 'financial_analyst', 'job_title_name' => 'Financial Analyst'],
            ['job_title_key' => 'records_officer', 'job_title_name' => 'Records Officer'],
            ['job_title_key' => 'library_staff', 'job_title_name' => 'Library Staff'],
        ]);

        DB::table('categories')->insert([
            ['category_key' => 'computer_science', 'category_name' => 'Computer Science', 'description' => 'Computing and software materials'],
            ['category_key' => 'engineering', 'category_name' => 'Engineering', 'description' => 'Engineering materials'],
            ['category_key' => 'education', 'category_name' => 'Education', 'description' => 'Education materials'],
            ['category_key' => 'business', 'category_name' => 'Business', 'description' => 'Business materials'],
            ['category_key' => 'communication', 'category_name' => 'Communication', 'description' => 'Communication materials'],
            ['category_key' => 'law', 'category_name' => 'Law', 'description' => 'Law materials'],
            ['category_key' => 'tourism_hospitality', 'category_name' => 'Tourism and Hospitality', 'description' => 'Tourism and hospitality materials'],
            ['category_key' => 'literature', 'category_name' => 'Literature', 'description' => 'Literature materials'],
            ['category_key' => 'science_technology', 'category_name' => 'Science and Technology', 'description' => 'Science and technology materials'],
            ['category_key' => 'general_references', 'category_name' => 'General References', 'description' => 'General reference materials'],
        ]);

        DB::table('material_types')->insert([
            ['material_type_key' => 'book', 'material_type_name' => 'Book', 'description' => 'Physical book'],
            ['material_type_key' => 'thesis', 'material_type_name' => 'Thesis', 'description' => 'Thesis material'],
            ['material_type_key' => 'dissertation', 'material_type_name' => 'Dissertation', 'description' => 'Dissertation material'],
            ['material_type_key' => 'journal', 'material_type_name' => 'Journal', 'description' => 'Academic journal'],
            ['material_type_key' => 'ebook', 'material_type_name' => 'E-Book', 'description' => 'Digital book'],
            ['material_type_key' => 'reference_material', 'material_type_name' => 'Reference Material', 'description' => 'Reference material'],
            ['material_type_key' => 'fiction_book', 'material_type_name' => 'Fiction Book', 'description' => 'Fiction book'],
            ['material_type_key' => 'non_print_material', 'material_type_name' => 'Non-print Material', 'description' => 'Media or non-print resource'],
        ]);

        DB::table('copy_statuses')->insert([
            ['status_key' => 'available', 'status_name' => 'Available', 'description' => 'Copy is available'],
            ['status_key' => 'borrowed', 'status_name' => 'Borrowed', 'description' => 'Copy is borrowed'],
            ['status_key' => 'reserved', 'status_name' => 'Reserved', 'description' => 'Copy is reserved'],
            ['status_key' => 'room_use_only', 'status_name' => 'Room Use Only', 'description' => 'Inside-library use only'],
            ['status_key' => 'digital_access_only', 'status_name' => 'Digital Access Only', 'description' => 'Online access only'],
            ['status_key' => 'lost', 'status_name' => 'Lost', 'description' => 'Copy is lost'],
            ['status_key' => 'damaged', 'status_name' => 'Damaged', 'description' => 'Copy is damaged'],
        ]);

        DB::table('borrow_statuses')->insert([
            ['status_key' => 'borrowed', 'status_name' => 'Borrowed', 'description' => 'Material is borrowed'],
            ['status_key' => 'returned', 'status_name' => 'Returned', 'description' => 'Material has been returned'],
            ['status_key' => 'overdue', 'status_name' => 'Overdue', 'description' => 'Material is overdue'],
            ['status_key' => 'lost', 'status_name' => 'Lost', 'description' => 'Material is lost'],
            ['status_key' => 'damaged', 'status_name' => 'Damaged', 'description' => 'Material is damaged'],
        ]);

        DB::table('penalty_types')->insert([
            ['penalty_type_key' => 'overdue_book', 'penalty_type_name' => 'Overdue Book', 'default_amount' => 5, 'description' => 'Penalty for overdue books'],
            ['penalty_type_key' => 'lost_book', 'penalty_type_name' => 'Lost Book', 'default_amount' => 500, 'description' => 'Penalty for lost books'],
            ['penalty_type_key' => 'damaged_book', 'penalty_type_name' => 'Damaged Book', 'default_amount' => 300, 'description' => 'Penalty for damaged books'],
            ['penalty_type_key' => 'unreturned_material', 'penalty_type_name' => 'Unreturned Material', 'default_amount' => 100, 'description' => 'Penalty for unreturned materials'],
        ]);

        DB::table('penalty_statuses')->insert([
            ['status_key' => 'pending', 'status_name' => 'Pending', 'description' => 'Pending review'],
            ['status_key' => 'unpaid', 'status_name' => 'Unpaid', 'description' => 'Penalty unpaid'],
            ['status_key' => 'paid', 'status_name' => 'Paid', 'description' => 'Penalty paid'],
            ['status_key' => 'waived', 'status_name' => 'Waived', 'description' => 'Penalty waived'],
            ['status_key' => 'cancelled', 'status_name' => 'Cancelled', 'description' => 'Penalty cancelled'],
        ]);

        DB::table('credit_score_levels')->insert([
            ['level_key' => 'excellent', 'level_name' => 'Excellent', 'min_score' => 90, 'max_score' => 100, 'description' => 'Excellent standing'],
            ['level_key' => 'good', 'level_name' => 'Good', 'min_score' => 75, 'max_score' => 89, 'description' => 'Good standing'],
            ['level_key' => 'warning', 'level_name' => 'Warning', 'min_score' => 50, 'max_score' => 74, 'description' => 'Warning standing'],
            ['level_key' => 'restricted', 'level_name' => 'Restricted', 'min_score' => 0, 'max_score' => 49, 'description' => 'Restricted standing'],
        ]);

        DB::table('credit_score_rules')->insert([
            ['rule_key' => 'on_time_return', 'rule_name' => 'On-time Return', 'rule_type' => 'reward', 'points' => 2, 'description' => 'Returned on time'],
            ['rule_key' => 'late_return', 'rule_name' => 'Late Return', 'rule_type' => 'deduction', 'points' => -5, 'description' => 'Late return'],
            ['rule_key' => 'lost_material', 'rule_name' => 'Lost Material', 'rule_type' => 'deduction', 'points' => -25, 'description' => 'Lost material'],
            ['rule_key' => 'damaged_material', 'rule_name' => 'Damaged Material', 'rule_type' => 'deduction', 'points' => -15, 'description' => 'Damaged material'],
            ['rule_key' => 'penalty_paid', 'rule_name' => 'Penalty Paid', 'rule_type' => 'reward', 'points' => 5, 'description' => 'Penalty settled'],
        ]);
    }
}