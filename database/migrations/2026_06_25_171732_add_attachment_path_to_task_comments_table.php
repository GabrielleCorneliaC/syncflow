<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('task_comments', function (Blueprint $table) {
            // Menambahkan kolom attachment_path yang boleh kosong (nullable)
            $table->string('attachment_path')->nullable()->after('comment');
        });
    }

    public function down()
    {
        Schema::table('task_comments', function (Blueprint $table) {
            // Menghapus kolom jika di-rollback
            $table->dropColumn('attachment_path');
        });
    }
};
