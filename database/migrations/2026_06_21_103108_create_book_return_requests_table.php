<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_return_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('borrow_transaction_id')
                ->constrained('borrow_transactions')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('status_id')
                ->constrained('request_statuses')
                ->restrictOnDelete();

            $table->dateTime('returned_at');
            $table->string('material_condition', 80);
            $table->string('proof_path', 1000);
            $table->text('remarks')->nullable();

            $table->foreignId('processed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->dateTime('processed_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'status_id']);
            $table->index('borrow_transaction_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_return_requests');
    }
};