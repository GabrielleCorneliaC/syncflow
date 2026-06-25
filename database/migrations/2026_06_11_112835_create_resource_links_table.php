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
    Schema::create('resource_links', function (Blueprint $table) {
        $table->id();
        $table->foreignId('workspace_id')->constrained('workspaces')->cascadeOnDelete();
        $table->string('title');
        $table->string('url'); //  simpen link website/drive
        $table->text('description')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_links');
    }
};
