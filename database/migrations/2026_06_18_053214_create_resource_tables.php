<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publishers', function (Blueprint $table) {
            $table->id();
            $table->string('publisher_key', 120)->unique();
            $table->string('publisher_name', 180);
            $table->timestamps();
        });

        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('author_key', 150)->nullable()->unique();
            $table->string('author_name', 180);
            $table->timestamps();
        });

        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_type_id')->constrained('material_types')->restrictOnDelete();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->foreignId('publisher_id')->nullable()->constrained('publishers')->nullOnDelete();
            $table->string('title', 255);
            $table->string('isbn', 50)->nullable()->unique();
            $table->unsignedSmallInteger('publication_year')->nullable();
            $table->string('edition', 100)->nullable();
            $table->text('description')->nullable();
            $table->string('cover_image_path', 1000)->nullable();
            $table->boolean('is_reference_only')->default(false);
            $table->boolean('is_digital')->default(false);
            $table->string('digital_url', 1000)->nullable();
            $table->timestamps();

            $table->index('title');
            $table->index('isbn');
            $table->index('publication_year');
            $table->index(['category_id', 'material_type_id']);
        });

        Schema::create('resource_authors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_id')->constrained('resources')->cascadeOnDelete();
            $table->foreignId('author_id')->constrained('authors')->cascadeOnDelete();

            $table->unique(['resource_id', 'author_id']);
        });

        Schema::create('resource_copies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_id')->constrained('resources')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('library_branches')->restrictOnDelete();
            $table->foreignId('copy_status_id')->constrained('copy_statuses')->restrictOnDelete();
            $table->string('accession_number', 100)->unique();
            $table->string('barcode', 100)->unique();
            $table->string('shelf_location', 100)->nullable();
            $table->boolean('is_borrowable')->default(true);
            $table->string('copy_condition', 80)->default('Good');
            $table->timestamps();

            $table->index(['resource_id', 'copy_status_id']);
            $table->index('branch_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resource_copies');
        Schema::dropIfExists('resource_authors');
        Schema::dropIfExists('resources');
        Schema::dropIfExists('authors');
        Schema::dropIfExists('publishers');
    }
};
