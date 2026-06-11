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
    Schema::create('collaborative_tasks', function (Blueprint $table) {
        $table->id();
        $table->foreignId('workspace_id')->constrained('workspaces')->cascadeOnDelete();
        $table->string('title');
        $table->text('description')->nullable();
        $table->string('status')->default('pending'); // Nilai awal: pending, nanti bisa in_progress, completed, overdue dll
        $table->foreignId('assignee_id')->nullable()->constrained('users')->onDelete('set null'); 
        $table->timestamp('due_date')->nullable(); // Untuk pelacakan Cron Job Overdue nanti
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collaborative_tasks');
    }
};
