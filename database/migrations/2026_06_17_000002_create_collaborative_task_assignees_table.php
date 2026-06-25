<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collaborative_task_assignees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collaborative_task_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('google_calendar_event_id')->nullable();
            $table->string('google_calendar_html_link')->nullable();
            $table->timestamps();

            $table->unique(['collaborative_task_id', 'user_id'], 'collab_task_user_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collaborative_task_assignees');
    }
};
