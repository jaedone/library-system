<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $approvedStatusId = DB::table('account_statuses')
            ->where('status_key', 'approved')
            ->value('id');

        $studentRoleId = DB::table('roles')
            ->where('role_name', 'student')
            ->value('id');

        $employeeRoleId = DB::table('roles')
            ->where('role_name', 'employee')
            ->value('id');

        $libraryStaffRoleId = DB::table('roles')
            ->where('role_name', 'library_staff')
            ->value('id');

        $ccisId = DB::table('colleges')
            ->where('college_code', 'ccis')
            ->value('id');

        $bscsId = DB::table('programs')
            ->where('program_code', 'bscs')
            ->value('id');

        $facultyTypeId = DB::table('employee_types')
            ->where('employee_type_key', 'faculty')
            ->value('id');

        $staffTypeId = DB::table('employee_types')
            ->where('employee_type_key', 'staff')
            ->value('id');

        $computerScienceDepartmentId = DB::table('departments')
            ->where('department_code', 'computer_science')
            ->value('id');

        $libraryDepartmentId = DB::table('departments')
            ->where('department_code', 'library_services')
            ->value('id');

        $instructorTitleId = DB::table('academic_titles')
            ->where('title_key', 'instructor')
            ->value('id');

        $libraryJobTitleId = DB::table('job_titles')
            ->where('job_title_key', 'library_staff')
            ->value('id');

        $itDivisionId = DB::table('divisions')
            ->where('division_key', 'it')
            ->value('id');

        $excellentLevelId = DB::table('credit_score_levels')
            ->where('level_key', 'excellent')
            ->value('id');

        $studentId = DB::table('users')->insertGetId([
            'role_id' => $studentRoleId,
            'account_status_id' => $approvedStatusId,
            'email' => 'student@iskolarngbayan.pup.edu.ph',
            'password' => Hash::make('password'),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('user_profiles')->insert([
            'user_id' => $studentId,
            'first_name' => 'Juan',
            'middle_name' => 'Reyes',
            'last_name' => 'Dela Cruz',
            'contact_number' => '09123456789',
            'library_account_number' => 'LIB-STU-0001',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('student_profiles')->insert([
            'user_id' => $studentId,
            'student_number' => '2026-00001-MN-0',
            'college_id' => $ccisId,
            'program_id' => $bscsId,
            'year_level' => '3rd Year',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('credit_scores')->insert([
            'user_id' => $studentId,
            'credit_score_level_id' => $excellentLevelId,
            'current_score' => 100,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $facultyUserId = DB::table('users')->insertGetId([
            'role_id' => $employeeRoleId,
            'account_status_id' => $approvedStatusId,
            'email' => 'faculty@pup.edu.ph',
            'password' => Hash::make('password'),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('user_profiles')->insert([
            'user_id' => $facultyUserId,
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'contact_number' => '09999999999',
            'library_account_number' => 'LIB-FAC-0001',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $facultyEmployeeProfileId = DB::table('employee_profiles')->insertGetId([
            'user_id' => $facultyUserId,
            'employee_number' => 'FAC-0001',
            'employee_id_number' => 'PUP-FAC-0001',
            'employee_type_id' => $facultyTypeId,
            'department_id' => $computerScienceDepartmentId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('faculty_profiles')->insert([
            'employee_profile_id' => $facultyEmployeeProfileId,
            'academic_title_id' => $instructorTitleId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('credit_scores')->insert([
            'user_id' => $facultyUserId,
            'credit_score_level_id' => $excellentLevelId,
            'current_score' => 100,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $libraryStaffUserId = DB::table('users')->insertGetId([
            'role_id' => $libraryStaffRoleId,
            'account_status_id' => $approvedStatusId,
            'email' => 'staff@pup.edu.ph',
            'password' => Hash::make('password'),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('user_profiles')->insert([
            'user_id' => $libraryStaffUserId,
            'first_name' => 'Library',
            'last_name' => 'Staff',
            'contact_number' => '09111111111',
            'library_account_number' => 'LIB-STF-0001',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $staffEmployeeProfileId = DB::table('employee_profiles')->insertGetId([
            'user_id' => $libraryStaffUserId,
            'employee_number' => 'EMP-0001',
            'employee_id_number' => 'PUP-EMP-0001',
            'employee_type_id' => $staffTypeId,
            'department_id' => $libraryDepartmentId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('staff_profiles')->insert([
            'employee_profile_id' => $staffEmployeeProfileId,
            'job_title_id' => $libraryJobTitleId,
            'division_id' => $itDivisionId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
