<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('resource_id')->constrained('resources')->cascadeOnDelete();
            $table->foreignId('copy_id')->nullable()->constrained('resource_copies')->nullOnDelete();
            $table->foreignId('status_id')->constrained('request_statuses')->restrictOnDelete();
            $table->dateTime('reserved_at');
            $table->dateTime('expires_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('processed_at')->nullable();
            $table->dateTime('claimed_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status_id']);
            $table->index(['resource_id', 'status_id']);
        });

        Schema::create('borrow_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('copy_id')->constrained('resource_copies')->restrictOnDelete();
            $table->foreignId('reservation_id')->nullable()->constrained('book_reservations')->nullOnDelete();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('borrowed_at');
            $table->dateTime('due_at');
            $table->dateTime('returned_at')->nullable();
            $table->foreignId('status_id')->constrained('borrow_statuses')->restrictOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status_id']);
            $table->index('due_at');
        });

        Schema::create('renewal_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrow_transaction_id')->constrained('borrow_transactions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('status_id')->constrained('request_statuses')->restrictOnDelete();
            $table->dateTime('requested_at');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('processed_at')->nullable();
            $table->dateTime('new_due_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('penalties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('borrow_transaction_id')->nullable()->constrained('borrow_transactions')->nullOnDelete();
            $table->foreignId('penalty_type_id')->constrained('penalty_types')->restrictOnDelete();
            $table->foreignId('penalty_status_id')->constrained('penalty_statuses')->restrictOnDelete();
            $table->decimal('amount', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->dateTime('issued_at');
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'penalty_status_id']);
        });

        Schema::create('credit_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->foreignId('credit_score_level_id')->constrained('credit_score_levels')->restrictOnDelete();
            $table->integer('current_score')->default(100);
            $table->timestamps();
        });

        Schema::create('credit_score_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('rule_id')->nullable()->constrained('credit_score_rules')->nullOnDelete();
            $table->integer('change_points');
            $table->text('reason');
            $table->string('source_type', 100)->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->timestamps();

            $table->index(['source_type', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_score_logs');
        Schema::dropIfExists('credit_scores');
        Schema::dropIfExists('penalties');
        Schema::dropIfExists('renewal_requests');
        Schema::dropIfExists('borrow_transactions');
        Schema::dropIfExists('book_reservations');
    }
};
