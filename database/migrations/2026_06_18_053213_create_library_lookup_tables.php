<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('role_name', 80)->unique();
            $table->string('display_role_name', 100);
            $table->text('description')->nullable();
        });

        Schema::create('account_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('status_key', 80)->unique();
            $table->string('status_name', 100);
            $table->text('description')->nullable();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('permission_name', 100)->unique();
            $table->string('display_permission_name', 150);
            $table->string('module', 100);
            $table->text('description')->nullable();
        });

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('role_id')
                ->constrained('roles')
                ->cascadeOnDelete();

            $table->foreignId('permission_id')
                ->constrained('permissions')
                ->cascadeOnDelete();

            $table->unique(['role_id', 'permission_id']);
        });

        Schema::create('colleges', function (Blueprint $table) {
            $table->id();
            $table->string('college_code', 50)->unique();
            $table->string('college_name', 180);
            $table->text('description')->nullable();
        });

        Schema::create('programs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('college_id')
                ->constrained('colleges')
                ->cascadeOnDelete();

            $table->string('program_code', 80)->unique();
            $table->string('program_name', 180);
            $table->text('description')->nullable();
        });

        Schema::create('departments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('college_id')
                ->nullable()
                ->constrained('colleges')
                ->nullOnDelete();

            $table->string('department_code', 80)->unique();
            $table->string('department_name', 180);
            $table->text('description')->nullable();
        });

        Schema::create('library_branches', function (Blueprint $table) {
            $table->id();
            $table->string('branch_code', 80)->unique();
            $table->string('branch_name', 180);
            $table->string('location', 255)->nullable();
            $table->text('description')->nullable();
        });

        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('document_type_key', 80)->unique();
            $table->string('document_type_name', 120);
            $table->text('description')->nullable();
        });

        Schema::create('request_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('status_key', 80)->unique();
            $table->string('status_name', 100);
            $table->text('description')->nullable();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_key', 100)->unique();
            $table->string('category_name', 150);
            $table->text('description')->nullable();
        });

        Schema::create('material_types', function (Blueprint $table) {
            $table->id();
            $table->string('material_type_key', 100)->unique();
            $table->string('material_type_name', 150);
            $table->text('description')->nullable();
        });

        Schema::create('copy_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('status_key', 80)->unique();
            $table->string('status_name', 100);
            $table->text('description')->nullable();
        });

        Schema::create('borrow_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('status_key', 80)->unique();
            $table->string('status_name', 100);
            $table->text('description')->nullable();
        });

        Schema::create('penalty_types', function (Blueprint $table) {
            $table->id();
            $table->string('penalty_type_key', 100)->unique();
            $table->string('penalty_type_name', 120);
            $table->decimal('default_amount', 10, 2)->default(0);
            $table->text('description')->nullable();
        });

        Schema::create('penalty_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('status_key', 80)->unique();
            $table->string('status_name', 100);
            $table->text('description')->nullable();
        });

        Schema::create('credit_score_levels', function (Blueprint $table) {
            $table->id();
            $table->string('level_key', 80)->unique();
            $table->string('level_name', 100);
            $table->integer('min_score');
            $table->integer('max_score');
            $table->text('description')->nullable();
        });

        Schema::create('credit_score_rules', function (Blueprint $table) {
            $table->id();
            $table->string('rule_key', 100)->unique();
            $table->string('rule_name', 150);
            $table->string('rule_type', 50);
            $table->integer('points');
            $table->text('description')->nullable();
        });

        Schema::create('employee_types', function (Blueprint $table) {
            $table->id();
            $table->string('employee_type_key', 50)->unique();
            $table->string('employee_type_name', 100);
            $table->text('description')->nullable();
        });

        Schema::create('academic_titles', function (Blueprint $table) {
            $table->id();
            $table->string('title_key', 80)->unique();
            $table->string('title_name', 120);
            $table->string('display_title', 120);
        });

        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->string('division_key', 80)->unique();
            $table->string('division_name', 180);
        });

        Schema::create('job_titles', function (Blueprint $table) {
            $table->id();
            $table->string('job_title_key', 80)->unique();
            $table->string('job_title_name', 180);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_titles');
        Schema::dropIfExists('divisions');
        Schema::dropIfExists('academic_titles');
        Schema::dropIfExists('employee_types');
        Schema::dropIfExists('credit_score_rules');
        Schema::dropIfExists('credit_score_levels');
        Schema::dropIfExists('penalty_statuses');
        Schema::dropIfExists('penalty_types');
        Schema::dropIfExists('borrow_statuses');
        Schema::dropIfExists('copy_statuses');
        Schema::dropIfExists('material_types');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('request_statuses');
        Schema::dropIfExists('document_types');
        Schema::dropIfExists('library_branches');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('programs');
        Schema::dropIfExists('colleges');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('account_statuses');
        Schema::dropIfExists('roles');
    }
};