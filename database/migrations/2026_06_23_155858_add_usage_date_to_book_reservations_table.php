<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('book_reservations', function (Blueprint $table) {
            $table->date('usage_date')->nullable()->after('reserved_at');
        });
    }

    public function down(): void
    {
        Schema::table('book_reservations', function (Blueprint $table) {
            $table->dropColumn('usage_date');
        });
    }
};