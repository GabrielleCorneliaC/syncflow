<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('personal_schedules', 'timezone')) {
            Schema::table('personal_schedules', function (Blueprint $table) {
                $table->string('timezone')->default('Asia/Jakarta')->after('end_time');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('personal_schedules', 'timezone')) {
            Schema::table('personal_schedules', function (Blueprint $table) {
                $table->dropColumn('timezone');
            });
        }
    }
};
