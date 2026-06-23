<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['announcements', 'resources', 'facilities'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'is_archived')) {
                    $table->boolean('is_archived')->default(false);
                }
            });
        }

        Schema::table('announcements', function (Blueprint $table) {
            if (!Schema::hasColumn('announcements', 'image_path')) {
                $table->string('image_path')->nullable();
            }
        });
    }

    public function down(): void
    {
        foreach (['announcements', 'resources', 'facilities'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'is_archived')) {
                    $table->dropColumn('is_archived');
                }
            });
        }

        Schema::table('announcements', function (Blueprint $table) {
            if (Schema::hasColumn('announcements', 'image_path')) {
                $table->dropColumn('image_path');
            }
        });
    }
};