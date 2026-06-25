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
        if (! Schema::hasColumn('personal_tasks', 'progress_before_completed')) {
            Schema::table('personal_tasks', function (Blueprint $table) {
                $table->unsignedTinyInteger('progress_before_completed')->nullable()->after('progress');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('personal_tasks', 'progress_before_completed')) {
            Schema::table('personal_tasks', function (Blueprint $table) {
                $table->dropColumn('progress_before_completed');
            });
        }
    }
};
