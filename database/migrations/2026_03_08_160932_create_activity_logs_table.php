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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('log_name')->nullable(); // Category/group for logs
            $table->text('description'); // What happened
            $table->string('subject_type')->nullable(); // Model class (e.g., App\Models\Perijinan)
            $table->unsignedBigInteger('subject_id')->nullable(); // Model ID
            $table->string('event')->nullable(); // Event type (created, updated, deleted, login, etc.)
            $table->json('properties')->nullable(); // Additional data (old values, new values, etc.)
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'log_name']);
            $table->index(['subject_type', 'subject_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
