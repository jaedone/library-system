<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InitialTransactionSeeder extends Seeder
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
            throw new \Exception('No student user found. Make sure DemoUserSeeder runs before InitialTransactionSeeder.');
        }

        if (!$libraryStaffUserId) {
            throw new \Exception('No library staff user found. Make sure DemoUserSeeder runs before InitialTransactionSeeder.');
        }

        $pendingRequestId = DB::table('request_statuses')
            ->where('status_key', 'pending')
            ->value('id');

        $approvedRequestId = DB::table('request_statuses')
            ->where('status_key', 'approved')
            ->value('id');

        $borrowedStatusId = DB::table('borrow_statuses')
            ->where('status_key', 'borrowed')
            ->value('id');

        $paidPenaltyStatusId = DB::table('penalty_statuses')
            ->where('status_key', 'paid')
            ->value('id');

        $overduePenaltyTypeId = DB::table('penalty_types')
            ->where('penalty_type_key', 'overdue_book')
            ->value('id');

        $onTimeReturnRuleId = DB::table('credit_score_rules')
            ->where('rule_key', 'on_time_return')
            ->value('id');

        $cleanCodeId = DB::table('resources')
            ->where('title', 'Clean Code')
            ->value('id');

        $cleanCodeCopyId = DB::table('resource_copies')
            ->where('resource_id', $cleanCodeId)
            ->value('id');

        $algorithmsId = DB::table('resources')
            ->where('title', 'Introduction to Algorithms')
            ->value('id');

        $algorithmsCopyId = DB::table('resource_copies')
            ->where('resource_id', $algorithmsId)
            ->value('id');

        if (!$approvedRequestId || !$pendingRequestId || !$borrowedStatusId) {
            throw new \Exception('Missing request or borrow status lookup data.');
        }

        if (!$paidPenaltyStatusId || !$overduePenaltyTypeId || !$onTimeReturnRuleId) {
            throw new \Exception('Missing penalty or credit score lookup data.');
        }

        if (!$cleanCodeId || !$cleanCodeCopyId || !$algorithmsId || !$algorithmsCopyId) {
            throw new \Exception('Missing sample resources or resource copies. Make sure SampleResourceSeeder runs before InitialTransactionSeeder.');
        }

        DB::table('book_reservations')->insert([
            'user_id' => $studentUserId,
            'resource_id' => $cleanCodeId,
            'copy_id' => $cleanCodeCopyId,
            'status_id' => $approvedRequestId,
            'reserved_at' => $now->copy()->subDays(2),
            'expires_at' => $now->copy()->addDay(),
            'approved_by' => $libraryStaffUserId,
            'processed_at' => $now->copy()->subDay(),
            'claimed_at' => null,
            'remarks' => 'Sample approved reservation for testing.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $borrowTransactionId = DB::table('borrow_transactions')->insertGetId([
            'user_id' => $studentUserId,
            'copy_id' => $algorithmsCopyId,
            'reservation_id' => null,
            'processed_by' => $libraryStaffUserId,
            'borrowed_at' => $now->copy()->subDays(5),
            'due_at' => $now->copy()->addDays(2),
            'returned_at' => null,
            'status_id' => $borrowedStatusId,
            'remarks' => 'Sample active borrowing transaction.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('renewal_requests')->insert([
            'borrow_transaction_id' => $borrowTransactionId,
            'user_id' => $studentUserId,
            'status_id' => $pendingRequestId,
            'requested_at' => $now,
            'approved_by' => null,
            'processed_at' => null,
            'new_due_at' => null,
            'remarks' => 'Sample pending renewal request.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('penalties')->insert([
            'user_id' => $studentUserId,
            'borrow_transaction_id' => $borrowTransactionId,
            'penalty_type_id' => $overduePenaltyTypeId,
            'penalty_status_id' => $paidPenaltyStatusId,
            'amount' => 10.00,
            'description' => 'Sample paid overdue penalty.',
            'issued_at' => $now->copy()->subDays(20),
            'paid_at' => $now->copy()->subDays(18),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('credit_score_logs')->insert([
            'user_id' => $studentUserId,
            'rule_id' => $onTimeReturnRuleId,
            'change_points' => 2,
            'reason' => 'Sample reward for on-time return.',
            'source_type' => 'borrow_transactions',
            'source_id' => $borrowTransactionId,
            'created_at' => $now,
        ]);

        DB::table('user_documents')->insert([
            [
                'user_id' => $studentUserId,
                'document_type_id' => DB::table('document_types')->where('document_type_key', 'certificate_of_registration')->value('id'),
                'status_id' => $approvedRequestId,
                'file_path' => 'sample-documents/student-cor.pdf',
                'uploaded_at' => $now,
                'verified_by' => $libraryStaffUserId,
                'verified_at' => $now,
                'remarks' => 'Sample verified student document.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => $studentUserId,
                'document_type_id' => DB::table('document_types')->where('document_type_key', 'pup_id')->value('id'),
                'status_id' => $approvedRequestId,
                'file_path' => 'sample-documents/student-id.pdf',
                'uploaded_at' => $now,
                'verified_by' => $libraryStaffUserId,
                'verified_at' => $now,
                'remarks' => 'Sample verified PUP ID.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}