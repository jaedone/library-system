<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facilities', function (Blueprint $table) {
    $table->id();

    $table->string('facility_key', 100)->unique();
    $table->string('facility_name', 150);
    $table->text('description')->nullable();

    $table->integer('capacity')->nullable();
    $table->string('location', 255)->nullable();

    $table->string('availability_days', 150)->nullable();
    $table->string('availability_hours', 150)->nullable();

    $table->json('equipment')->nullable();
    $table->json('usage_for')->nullable();

    $table->string('image_path', 1000)->nullable();

    $table->boolean('is_active')->default(true);
});

        Schema::create('referral_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('destination_library', 180);
            $table->text('purpose');
            $table->text('material_needed');
            $table->foreignId('status_id')->constrained('request_statuses')->restrictOnDelete();
            $table->dateTime('requested_at');
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('processed_at')->nullable();
            $table->string('approved_letter_path')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('facility_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('facility_id')->constrained('facilities')->restrictOnDelete();
            $table->foreignId('status_id')->constrained('request_statuses')->restrictOnDelete();
            $table->date('reservation_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->text('purpose');
            $table->integer('participants_count')->default(1);
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('processed_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['facility_id', 'reservation_date']);
        });

        Schema::create('online_resources', function (Blueprint $table) {
            $table->id();
            $table->string('title', 180);
            $table->text('description')->nullable();
            $table->string('url', 1000);
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('material_type_id')->nullable()->constrained('material_types')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('online_resource_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('online_resource_id')->constrained('online_resources')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();

            $table->unique(['online_resource_id', 'role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('online_resource_roles');
        Schema::dropIfExists('online_resources');
        Schema::dropIfExists('facility_reservations');
        Schema::dropIfExists('referral_requests');
        Schema::dropIfExists('facilities');
    }
};
