<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeders extends Seeder
{
    public function run(): void
    {
        $now = now();

        $libraryStaffRoleId = DB::table('roles')->where('role_name', 'library_staff')->value('id');
        $approvedStatusId   = DB::table('account_statuses')->where('status_key', 'approved')->value('id');
        $staffTypeId        = DB::table('employee_types')->where('employee_type_key', 'staff')->value('id');
        $libraryDeptId      = DB::table('departments')->where('department_code', 'library_services')->value('id');
        $libraryJobTitleId  = DB::table('job_titles')->where('job_title_key', 'library_staff')->value('id');
        $itDivisionId       = DB::table('divisions')->where('division_key', 'it')->value('id');
        $excellentLevelId   = DB::table('credit_score_levels')->where('level_key', 'excellent')->value('id');

        if (!DB::table('users')->where('email', 'admin@pup.edu.ph')->exists()) {
            $adminUserId = DB::table('users')->insertGetId([
                'role_id'           => $libraryStaffRoleId,
                'account_status_id' => $approvedStatusId,
                'email'             => 'admin@pup.edu.ph',
                'password'          => Hash::make('password'),
                'created_at'        => $now,
                'updated_at'        => $now,
            ]);

            DB::table('user_profiles')->insert([
                'user_id'                => $adminUserId,
                'first_name'             => 'Admin',
                'last_name'              => 'Librarian',
                'contact_number'         => '09500000000',
                'library_account_number' => 'LIB-ADM-0001',
                'created_at'             => $now,
                'updated_at'             => $now,
            ]);

            $adminEmpId = DB::table('employee_profiles')->insertGetId([
                'user_id'            => $adminUserId,
                'employee_number'    => 'EMP-ADMIN-01',
                'employee_id_number' => 'PUP-ADM-0001',
                'employee_type_id'   => $staffTypeId,
                'department_id'      => $libraryDeptId,
                'created_at'         => $now,
                'updated_at'         => $now,
            ]);

            DB::table('staff_profiles')->insert([
                'employee_profile_id' => $adminEmpId,
                'job_title_id'        => $libraryJobTitleId,
                'division_id'         => $itDivisionId,
                'created_at'          => $now,
                'updated_at'          => $now,
            ]);

            DB::table('credit_scores')->insert([
                'user_id'               => $adminUserId,
                'credit_score_level_id' => $excellentLevelId,
                'current_score'         => 100,
                'created_at'            => $now,
                'updated_at'            => $now,
            ]);
        }

        $alumniRoleId = DB::table('roles')->where('role_name', 'alumni')->value('id');

        if (!DB::table('users')->where('email', 'alumni1@pup.edu.ph')->exists()) {
            $alumniUserId = DB::table('users')->insertGetId([
                'role_id'           => $alumniRoleId,
                'account_status_id' => $approvedStatusId,
                'email'             => 'alumni1@pup.edu.ph',
                'password'          => Hash::make('password'),
                'created_at'        => $now,
                'updated_at'        => $now,
            ]);

            DB::table('user_profiles')->insert([
                'user_id'                => $alumniUserId,
                'first_name'             => 'Ana',
                'middle_name'            => 'Cruz',
                'last_name'              => 'Reyes',
                'contact_number'         => '09177654321',
                'library_account_number' => 'LIB-ALM-0001',
                'created_at'             => $now,
                'updated_at'             => $now,
            ]);

            DB::table('alumni_profiles')->insert([
                'user_id'           => $alumniUserId,
                'alumni_id_number'  => 'ALM-2022-001',
                'graduated_program' => 'Bachelor of Science in Computer Science',
                'graduation_year'   => 2022,
                'created_at'        => $now,
                'updated_at'        => $now,
            ]);

            DB::table('credit_scores')->insert([
                'user_id'               => $alumniUserId,
                'credit_score_level_id' => $excellentLevelId,
                'current_score'         => 95,
                'created_at'            => $now,
                'updated_at'            => $now,
            ]);
        }

        $visitorRoleId = DB::table('roles')->where('role_name', 'visitor')->value('id');

        if (!DB::table('users')->where('email', 'visitor@email.com')->exists()) {
            $visitorUserId = DB::table('users')->insertGetId([
                'role_id'           => $visitorRoleId,
                'account_status_id' => $approvedStatusId,
                'email'             => 'visitor@email.com',
                'password'          => Hash::make('password'),
                'created_at'        => $now,
                'updated_at'        => $now,
            ]);

            DB::table('user_profiles')->insert([
                'user_id'                => $visitorUserId,
                'first_name'             => 'Carlos',
                'last_name'              => 'Bautista',
                'contact_number'         => '09286543210',
                'library_account_number' => 'LIB-VIS-0001',
                'created_at'             => $now,
                'updated_at'             => $now,
            ]);

            DB::table('visitor_profiles')->insert([
                'user_id'             => $visitorUserId,
                'institution'         => 'De La Salle University',
                'research_topic'      => 'Open Source Software in Philippine Higher Education',
                'intended_visit_date' => $now->copy()->addDays(7)->toDateString(),
                'purpose_of_visit'    => 'Conduct library research and review theses for comparative study.',
                'expires_at'          => $now->copy()->addMonths(3),
                'created_at'          => $now,
                'updated_at'          => $now,
            ]);

            DB::table('credit_scores')->insert([
                'user_id'               => $visitorUserId,
                'credit_score_level_id' => $excellentLevelId,
                'current_score'         => 100,
                'created_at'            => $now,
                'updated_at'            => $now,
            ]);
        }

        $pendingStatusId = DB::table('account_statuses')->where('status_key', 'pending')->value('id');
        $studentRoleId   = DB::table('roles')->where('role_name', 'student')->value('id');
        $ccisId          = DB::table('colleges')->where('college_code', 'ccis')->value('id');
        $bsitId          = DB::table('programs')->where('program_code', 'bsit')->value('id');

        if (!DB::table('users')->where('email', 'student2@iskolarngbayan.pup.edu.ph')->exists()) {
            $student2Id = DB::table('users')->insertGetId([
                'role_id'           => $studentRoleId,
                'account_status_id' => $pendingStatusId,
                'email'             => 'student2@iskolarngbayan.pup.edu.ph',
                'password'          => Hash::make('password'),
                'created_at'        => $now,
                'updated_at'        => $now,
            ]);

            DB::table('user_profiles')->insert([
                'user_id'                => $student2Id,
                'first_name'             => 'Pedro',
                'middle_name'            => 'Garcia',
                'last_name'              => 'Mendoza',
                'contact_number'         => '09201234567',
                'library_account_number' => 'LIB-STU-0002',
                'created_at'             => $now,
                'updated_at'             => $now,
            ]);

            DB::table('student_profiles')->insert([
                'user_id'       => $student2Id,
                'student_number' => '2026-00002-MN-0',
                'college_id'    => $ccisId,
                'program_id'    => $bsitId,
                'year_level'    => '2nd Year',
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);

            DB::table('credit_scores')->insert([
                'user_id'               => $student2Id,
                'credit_score_level_id' => $excellentLevelId,
                'current_score'         => 100,
                'created_at'            => $now,
                'updated_at'            => $now,
            ]);

            $corDocTypeId = DB::table('document_types')->where('document_type_key', 'certificate_of_registration')->value('id');
            $pendingReqId = DB::table('request_statuses')->where('status_key', 'pending')->value('id');

            DB::table('user_documents')->insert([
                'user_id'          => $student2Id,
                'document_type_id' => $corDocTypeId,
                'status_id'        => $pendingReqId,
                'file_path'        => 'sample-documents/student2-cor.pdf',
                'uploaded_at'      => $now,
                'verified_by'      => null,
                'verified_at'      => null,
                'remarks'          => 'Awaiting staff verification.',
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);
        }

        $announcements = [
            [
                'announcement_key' => 'library_hours_update',
                'title'            => 'Updated Library Service Hours',
                'description'      => 'Effective immediately, the PUP Main Library will be open Monday to Friday from 8:00 AM to 8:00 PM, and Saturday from 8:00 AM to 5:00 PM. Sunday remains closed. Please plan your visits accordingly.',
                'icon'             => 'bi-clock',
                'is_active'        => true,
                'is_archived'      => false,
                'sort_order'       => 1,
                'published_at'     => $now->copy()->subDays(5),
                'image_path'       => null,
            ],
            [
                'announcement_key' => 'new_arrivals_june_2026',
                'title'            => 'New Book Arrivals — June 2026',
                'description'      => 'The library has received new acquisitions for June 2026, including additional titles in Computer Science, Engineering, and Law. Visit the New Arrivals section on the second floor or browse the catalog online.',
                'icon'             => 'bi-book',
                'is_active'        => true,
                'is_archived'      => false,
                'sort_order'       => 2,
                'published_at'     => $now->copy()->subDays(3),
                'image_path'       => null,
            ],
            [
                'announcement_key' => 'online_renewal_available',
                'title'            => 'Online Book Renewal Now Available',
                'description'      => 'Students and employees may now request book renewals through the library portal. Renewals must be requested at least one day before the due date. Only one renewal per borrowing transaction is allowed.',
                'icon'             => 'bi-arrow-repeat',
                'is_active'        => true,
                'is_archived'      => false,
                'sort_order'       => 3,
                'published_at'     => $now->copy()->subDays(10),
                'image_path'       => null,
            ],
            [
                'announcement_key' => 'thesis_submission_period',
                'title'            => 'Thesis and Dissertation Submission Period',
                'description'      => 'Graduating students are reminded to submit two (2) bound copies of their approved thesis or dissertation to the library on or before June 30, 2026. Digital copies in PDF format must also be submitted via the library portal.',
                'icon'             => 'bi-mortarboard',
                'is_active'        => true,
                'is_archived'      => false,
                'sort_order'       => 4,
                'published_at'     => $now->copy()->subDays(7),
                'image_path'       => null,
            ],
            [
                'announcement_key' => 'facility_reservation_system',
                'title'            => 'Discussion Room Reservation Now Online',
                'description'      => 'You may now reserve Discussion Rooms and Computer Stations through the library portal. Reservations must be made at least 24 hours in advance. Walk-in reservations remain available subject to availability.',
                'icon'             => 'bi-calendar-check',
                'is_active'        => true,
                'is_archived'      => false,
                'sort_order'       => 5,
                'published_at'     => $now->copy()->subDays(14),
                'image_path'       => null,
            ],
            [
                'announcement_key' => 'holiday_schedule_2026',
                'title'            => 'Holiday Library Schedule — June 2026',
                'description'      => 'The library will be closed on the following national holidays: June 12 (Independence Day). Regular operations resume on the next working day.',
                'icon'             => 'bi-calendar-x',
                'is_active'        => false,
                'is_archived'      => true,
                'sort_order'       => 6,
                'published_at'     => $now->copy()->subDays(20),
                'image_path'       => null,
            ],
        ];

        foreach ($announcements as $announcement) {
            if (!DB::table('announcements')->where('announcement_key', $announcement['announcement_key'])->exists()) {
                DB::table('announcements')->insert(array_merge($announcement, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }
        }

        $studentUserId      = DB::table('users')->where('email', 'student@iskolarngbayan.pup.edu.ph')->value('id');
        $libraryStaffUserId = DB::table('users')->where('email', 'staff@pup.edu.ph')->value('id');
        $approvedReqId      = DB::table('request_statuses')->where('status_key', 'approved')->value('id');
        $pendingReqStatusId = DB::table('request_statuses')->where('status_key', 'pending')->value('id');

        $borrowTxId = DB::table('borrow_transactions')
            ->where('user_id', $studentUserId)
            ->value('id');

        if ($borrowTxId && !DB::table('book_return_requests')->where('borrow_transaction_id', $borrowTxId)->exists()) {
            DB::table('book_return_requests')->insert([
                'borrow_transaction_id' => $borrowTxId,
                'user_id'               => $studentUserId,
                'status_id'             => $approvedReqId,
                'returned_at'           => $now->copy()->subDay(),
                'material_condition'    => 'Good',
                'proof_path'            => 'sample-return-proofs/student-return-001.jpg',
                'remarks'               => 'Returned in good condition. No visible damage.',
                'processed_by'          => $libraryStaffUserId,
                'processed_at'          => $now->copy()->subHours(2),
                'created_at'            => $now,
                'updated_at'            => $now,
            ]);
        }

        $algorithmsCopyId = DB::table('resources')
            ->where('title', 'Introduction to Algorithms')
            ->join('resource_copies', 'resources.id', '=', 'resource_copies.resource_id')
            ->value('resource_copies.id');

        $borrowedStatusId = DB::table('borrow_statuses')->where('status_key', 'borrowed')->value('id');

        // Create a second borrow transaction for the return-pending demo
        $secondBorrowId = DB::table('borrow_transactions')
            ->where('user_id', $studentUserId)
            ->where('status_id', $borrowedStatusId)
            ->whereNull('returned_at')
            ->skip(1)
            ->value('id');

        if (!$secondBorrowId) {
            $cleanCodeId     = DB::table('resources')->where('title', 'Clean Code')->value('id');
            $cleanCodeCopyId = DB::table('resource_copies')->where('resource_id', $cleanCodeId)->value('id');

            if ($cleanCodeCopyId) {
                $secondBorrowId = DB::table('borrow_transactions')->insertGetId([
                    'user_id'        => $studentUserId,
                    'copy_id'        => $cleanCodeCopyId,
                    'reservation_id' => null,
                    'processed_by'   => $libraryStaffUserId,
                    'borrowed_at'    => $now->copy()->subDays(8),
                    'due_at'         => $now->copy()->addDay(),
                    'returned_at'    => null,
                    'status_id'      => $borrowedStatusId,
                    'remarks'        => 'Second active borrowing for return-request demo.',
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ]);
            }
        }

        if ($secondBorrowId && !DB::table('book_return_requests')
            ->where('borrow_transaction_id', $secondBorrowId)
            ->where('status_id', $pendingReqStatusId)
            ->exists()
        ) {
            DB::table('book_return_requests')->insert([
                'borrow_transaction_id' => $secondBorrowId,
                'user_id'               => $studentUserId,
                'status_id'             => $pendingReqStatusId,
                'returned_at'           => $now,
                'material_condition'    => 'Good',
                'proof_path'            => 'sample-return-proofs/student-return-002.jpg',
                'remarks'               => 'Online return request. Awaiting staff confirmation.',
                'processed_by'          => null,
                'processed_at'          => null,
                'created_at'            => $now,
                'updated_at'            => $now,
            ]);
        }

        if (!DB::table('user_service_restrictions')
            ->where('user_id', $studentUserId)
            ->where('service_key', 'borrow_book')
            ->exists()
        ) {
            
            DB::table('user_service_restrictions')->insert([
                'user_id'           => $studentUserId,
                'service_key'       => 'borrow_book',
                'reason'            => 'Account had an unpaid overdue penalty. Restriction was lifted after full payment.',
                'restricted_until'  => $now->copy()->subDays(18),
                'restricted_by'     => $libraryStaffUserId,
                'is_active'         => false,
                'created_at'        => $now->copy()->subDays(22),
                'updated_at'        => $now->copy()->subDays(18),
            ]);
        }

        $notificationRows = [
            [
                'user_id'          => $studentUserId,
                'notification_key' => 'notif-student-reservation-approved',
                'type'             => 'reservation',
                'title'            => 'Book Reservation Approved',
                'message'          => 'Your reservation for "Clean Code" has been approved. Please claim your book within 3 days.',
                'action_url'       => '/my-reservations',
                'data'             => json_encode(['resource' => 'Clean Code', 'branch' => 'Main Library']),
                'is_read'          => false,
                'read_at'          => null,
                'created_at'       => $now->copy()->subDay(),
                'updated_at'       => $now->copy()->subDay(),
            ],
            [
                'user_id'          => $studentUserId,
                'notification_key' => 'notif-student-due-reminder',
                'type'             => 'due_reminder',
                'title'            => 'Book Due Tomorrow',
                'message'          => '"Introduction to Algorithms" is due tomorrow. Return or request a renewal to avoid penalties.',
                'action_url'       => '/my-borrowings',
                'data'             => json_encode(['resource' => 'Introduction to Algorithms', 'due_in_days' => 1]),
                'is_read'          => false,
                'read_at'          => null,
                'created_at'       => $now->copy()->subHours(6),
                'updated_at'       => $now->copy()->subHours(6),
            ],
            [
                'user_id'          => $studentUserId,
                'notification_key' => 'notif-student-penalty-paid',
                'type'             => 'penalty',
                'title'            => 'Penalty Marked as Paid',
                'message'          => 'Your overdue penalty of ₱10.00 has been marked as paid. Your borrowing privileges have been restored.',
                'action_url'       => '/my-penalties',
                'data'             => json_encode(['amount' => 10.00, 'penalty_type' => 'Overdue Book']),
                'is_read'          => true,
                'read_at'          => $now->copy()->subDays(18),
                'created_at'       => $now->copy()->subDays(18),
                'updated_at'       => $now->copy()->subDays(18),
            ],
            [
                'user_id'          => $studentUserId,
                'notification_key' => 'notif-student-renewal-pending',
                'type'             => 'renewal',
                'title'            => 'Renewal Request Received',
                'message'          => 'Your renewal request is pending review by library staff. You will be notified once processed.',
                'action_url'       => '/my-renewals',
                'data'             => json_encode(['resource' => 'Introduction to Algorithms']),
                'is_read'          => true,
                'read_at'          => $now->copy()->subHours(12),
                'created_at'       => $now->copy()->subHours(14),
                'updated_at'       => $now->copy()->subHours(12),
            ],
            [
                'user_id'          => $libraryStaffUserId,
                'notification_key' => 'notif-staff-new-reservation',
                'type'             => 'admin_action',
                'title'            => 'New Book Reservation Request',
                'message'          => 'A new reservation request has been submitted by Juan Dela Cruz for "Introduction to Algorithms". Please review and process.',
                'action_url'       => '/admin/reservations',
                'data'             => json_encode(['user' => 'Juan Dela Cruz', 'resource' => 'Introduction to Algorithms']),
                'is_read'          => false,
                'read_at'          => null,
                'created_at'       => $now->copy()->subHours(1),
                'updated_at'       => $now->copy()->subHours(1),
            ],
            [
                'user_id'          => $libraryStaffUserId,
                'notification_key' => 'notif-staff-return-request',
                'type'             => 'admin_action',
                'title'            => 'Pending Book Return Request',
                'message'          => 'Juan Dela Cruz submitted an online return request for "Clean Code". Please verify and update the copy status.',
                'action_url'       => '/admin/returns',
                'data'             => json_encode(['user' => 'Juan Dela Cruz', 'resource' => 'Clean Code']),
                'is_read'          => false,
                'read_at'          => null,
                'created_at'       => $now->copy()->subMinutes(30),
                'updated_at'       => $now->copy()->subMinutes(30),
            ],
            [
                'user_id'          => $libraryStaffUserId,
                'notification_key' => 'notif-staff-account-pending',
                'type'             => 'admin_action',
                'title'            => 'New Account Pending Verification',
                'message'          => 'Pedro Mendoza has registered as a student and is awaiting account verification. Review the submitted documents.',
                'action_url'       => '/admin/users/pending',
                'data'             => json_encode(['user' => 'Pedro Mendoza', 'role' => 'Student']),
                'is_read'          => false,
                'read_at'          => null,
                'created_at'       => $now->copy()->subHours(3),
                'updated_at'       => $now->copy()->subHours(3),
            ],
        ];

        foreach ($notificationRows as $notif) {
            if (!DB::table('user_notifications')->where('notification_key', $notif['notification_key'])->exists()) {
                DB::table('user_notifications')->insert($notif);
            }
        }

        $reservationId = DB::table('book_reservations')->where('user_id', $studentUserId)->value('id');
        $facilityResId = DB::table('facility_reservations')->where('user_id', $studentUserId)->value('id');
        $referralReqId = DB::table('referral_requests')->where('user_id', $studentUserId)->value('id');

        $serviceLogs = [];

        if ($reservationId) {
            $serviceLogs[] = [
                'service_key'   => 'book_reservation',
                'request_table' => 'book_reservations',
                'request_id'    => $reservationId,
                'user_id'       => $studentUserId,
                'admin_id'      => null,
                'action'        => 'submitted',
                'remarks'       => 'Student submitted a book reservation request.',
                'metadata'      => json_encode(['resource' => 'Clean Code']),
                'created_at'    => $now->copy()->subDays(2),
                'updated_at'    => $now->copy()->subDays(2),
            ];
            $serviceLogs[] = [
                'service_key'   => 'book_reservation',
                'request_table' => 'book_reservations',
                'request_id'    => $reservationId,
                'user_id'       => null,
                'admin_id'      => $libraryStaffUserId,
                'action'        => 'approved',
                'remarks'       => 'Library staff approved the reservation.',
                'metadata'      => json_encode(['resource' => 'Clean Code', 'copy_assigned' => true]),
                'created_at'    => $now->copy()->subDay(),
                'updated_at'    => $now->copy()->subDay(),
            ];
        }

        if ($facilityResId) {
            $serviceLogs[] = [
                'service_key'   => 'facility_reservation',
                'request_table' => 'facility_reservations',
                'request_id'    => $facilityResId,
                'user_id'       => $studentUserId,
                'admin_id'      => null,
                'action'        => 'submitted',
                'remarks'       => 'Student submitted a facility reservation for Discussion Room.',
                'metadata'      => json_encode(['facility' => 'Discussion Room', 'participants' => 5]),
                'created_at'    => $now->copy()->subDays(4),
                'updated_at'    => $now->copy()->subDays(4),
            ];
            $serviceLogs[] = [
                'service_key'   => 'facility_reservation',
                'request_table' => 'facility_reservations',
                'request_id'    => $facilityResId,
                'user_id'       => null,
                'admin_id'      => $libraryStaffUserId,
                'action'        => 'approved',
                'remarks'       => 'Approved facility reservation. Room confirmed for the requested time slot.',
                'metadata'      => json_encode(['facility' => 'Discussion Room', 'date' => $now->copy()->addDays(3)->toDateString()]),
                'created_at'    => $now->copy()->subDays(3),
                'updated_at'    => $now->copy()->subDays(3),
            ];
        }

        if ($referralReqId) {
            $serviceLogs[] = [
                'service_key'   => 'referral',
                'request_table' => 'referral_requests',
                'request_id'    => $referralReqId,
                'user_id'       => $studentUserId,
                'admin_id'      => null,
                'action'        => 'submitted',
                'remarks'       => 'Student submitted a referral letter request.',
                'metadata'      => json_encode(['destination' => 'National Library of the Philippines']),
                'created_at'    => $now,
                'updated_at'    => $now,
            ];
        }

        if ($borrowTxId) {
            $serviceLogs[] = [
                'service_key'   => 'borrowing',
                'request_table' => 'borrow_transactions',
                'request_id'    => $borrowTxId,
                'user_id'       => null,
                'admin_id'      => $libraryStaffUserId,
                'action'        => 'issued',
                'remarks'       => 'Library staff issued the book to the student.',
                'metadata'      => json_encode(['copy_condition' => 'Good']),
                'created_at'    => $now->copy()->subDays(5),
                'updated_at'    => $now->copy()->subDays(5),
            ];
        }

        foreach ($serviceLogs as $log) {
            DB::table('service_request_logs')->insert($log);
        }

        $facultyUserId = DB::table('users')->where('email', 'faculty@pup.edu.ph')->value('id');
        $computerNetworksId = DB::table('resources')->where('title', 'Computer Networks')->value('id');
        $computerNetworksCopyId = DB::table('resource_copies')->where('resource_id', $computerNetworksId)->value('id');

        if ($facultyUserId && $computerNetworksCopyId) {
            $returnedStatusId = DB::table('borrow_statuses')->where('status_key', 'returned')->value('id');

            if (!DB::table('borrow_transactions')->where('user_id', $facultyUserId)->exists()) {
                DB::table('borrow_transactions')->insert([
                    'user_id'        => $facultyUserId,
                    'copy_id'        => $computerNetworksCopyId,
                    'reservation_id' => null,
                    'processed_by'   => $libraryStaffUserId,
                    'borrowed_at'    => $now->copy()->subDays(14),
                    'due_at'         => $now->copy()->subDays(7),
                    'returned_at'    => $now->copy()->subDays(8),
                    'status_id'      => $returnedStatusId,
                    'remarks'        => 'Faculty returned the book one day before due date.',
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ]);

                // Credit score reward for on-time return
                $onTimeReturnRuleId = DB::table('credit_score_rules')->where('rule_key', 'on_time_return')->value('id');
                $facultyBorrowId    = DB::table('borrow_transactions')->where('user_id', $facultyUserId)->value('id');

                DB::table('credit_score_logs')->insert([
                    'user_id'       => $facultyUserId,
                    'rule_id'       => $onTimeReturnRuleId,
                    'change_points' => 2,
                    'reason'        => 'Returned "Computer Networks" on time.',
                    'source_type'   => 'borrow_transactions',
                    'source_id'     => $facultyBorrowId,
                    'created_at'    => $now->copy()->subDays(8),
                ]);
            }
        }

        $this->command->info('DemoDataSeeder complete.');
        $this->command->info('');
        $this->command->info('Demo Credentials:');
        $this->command->info('  admin@pup.edu.ph                  / password  (Library Admin)');
        $this->command->info('  staff@pup.edu.ph                  / password  (Library Staff)');
        $this->command->info('  student@iskolarngbayan.pup.edu.ph / password  (Student - active)');
        $this->command->info('  student2@iskolarngbayan.pup.edu.ph/ password  (Student - pending verification)');
        $this->command->info('  faculty@pup.edu.ph                / password  (Faculty)');
        $this->command->info('  alumni1@pup.edu.ph                 / password  (Alumni)');
        $this->command->info('  visitor@email.com                 / password  (Visitor)');
    }
}