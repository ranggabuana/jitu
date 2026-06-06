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
        Schema::create('data_perijinan_return_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('data_perijinan_id')->constrained('data_perijinan')->onDelete('cascade');
            $table->foreignId('from_user_id')->constrained('users');
            $table->string('from_role_label');
            $table->string('to_role_label');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_perijinan_return_logs');
    }
};
