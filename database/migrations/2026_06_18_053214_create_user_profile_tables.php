<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->foreignId('role_id')
                ->constrained('roles')
                ->restrictOnDelete();

            $table->foreignId('account_status_id')
                ->constrained('account_statuses')
                ->restrictOnDelete();

            $table->string('email', 150)->unique();
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->index('role_id');
            $table->index('account_status_id');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100);
            $table->string('contact_number', 30)->nullable();
            $table->string('profile_photo_path')->nullable();
            $table->string('library_account_number', 80)->unique();
            $table->timestamps();
        });

        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('student_number', 50)->unique();

            $table->foreignId('college_id')
                ->constrained('colleges')
                ->restrictOnDelete();

            $table->foreignId('program_id')
                ->constrained('programs')
                ->restrictOnDelete();

            $table->string('year_level', 50);
            $table->timestamps();
        });

        Schema::create('employee_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('employee_number', 50)->unique();
            $table->string('employee_id_number', 80)->unique();

            $table->foreignId('employee_type_id')
                ->constrained('employee_types')
                ->restrictOnDelete();

            $table->foreignId('department_id')
                ->constrained('departments')
                ->restrictOnDelete();

            $table->timestamps();
        });

        Schema::create('faculty_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_profile_id')
                ->unique()
                ->constrained('employee_profiles')
                ->cascadeOnDelete();

            $table->foreignId('academic_title_id')
                ->constrained('academic_titles')
                ->restrictOnDelete();

            $table->timestamps();
        });

        Schema::create('staff_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_profile_id')
                ->unique()
                ->constrained('employee_profiles')
                ->cascadeOnDelete();

            $table->foreignId('job_title_id')
                ->constrained('job_titles')
                ->restrictOnDelete();

            $table->foreignId('division_id')
                ->constrained('divisions')
                ->restrictOnDelete();

            $table->timestamps();
        });

        Schema::create('alumni_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('alumni_id_number', 50)->unique();
            $table->string('graduated_program', 180);
            $table->unsignedSmallInteger('graduation_year');
            $table->timestamps();
        });

        Schema::create('visitor_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('institution', 180);
            $table->string('research_topic', 255);
            $table->date('intended_visit_date')->nullable();
            $table->text('purpose_of_visit')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('user_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('document_type_id')
                ->constrained('document_types')
                ->restrictOnDelete();

            $table->foreignId('status_id')
                ->constrained('request_statuses')
                ->restrictOnDelete();

            $table->string('file_path');
            $table->timestamp('uploaded_at')->nullable();

            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('verified_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_documents');
        Schema::dropIfExists('visitor_profiles');
        Schema::dropIfExists('alumni_profiles');
        Schema::dropIfExists('staff_profiles');
        Schema::dropIfExists('faculty_profiles');
        Schema::dropIfExists('employee_profiles');
        Schema::dropIfExists('student_profiles');
        Schema::dropIfExists('user_profiles');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};