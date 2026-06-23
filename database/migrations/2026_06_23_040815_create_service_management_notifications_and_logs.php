<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('user_notifications')) {
            Schema::create('user_notifications', function (Blueprint $table) {
                $table->id();

                $table->foreignId('user_id')
                    ->constrained('users')
                    ->cascadeOnDelete();

                $table->string('notification_key', 120)->unique();
                $table->string('type', 80)->default('general');

                $table->string('title', 180);
                $table->text('message');

                $table->string('action_url', 1000)->nullable();
                $table->json('data')->nullable();

                $table->boolean('is_read')->default(false);
                $table->dateTime('read_at')->nullable();

                $table->timestamps();

                $table->index(['user_id', 'is_read']);
                $table->index(['type']);
            });
        }

        if (!Schema::hasTable('service_request_logs')) {
            Schema::create('service_request_logs', function (Blueprint $table) {
                $table->id();

                $table->string('service_key', 80);
                $table->string('request_table', 120);
                $table->unsignedBigInteger('request_id');

                $table->foreignId('user_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->foreignId('admin_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->string('action', 80);
                $table->text('remarks')->nullable();
                $table->json('metadata')->nullable();

                $table->timestamps();

                $table->index(['service_key', 'request_id']);
                $table->index(['request_table', 'request_id']);
                $table->index(['user_id']);
                $table->index(['admin_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('service_request_logs');
        Schema::dropIfExists('user_notifications');
    }
};