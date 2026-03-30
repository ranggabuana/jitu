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
        Schema::create('hasil_skm', function (Blueprint $table) {
            $table->id();
            $table->foreignId('data_skm_id')->constrained('data_skm')->onDelete('cascade');
            $table->string('responden_nama')->nullable(); // Nama responden (opsional)
            $table->string('responden_email')->nullable(); // Email responden (opsional)
            $table->string('nip')->nullable(); // NIP responden (untuk internal)
            $table->enum('jawaban', ['1', '2', '3', '4']); // Jawaban skala 1-4
            $table->text('saran')->nullable(); // Saran/komentar tambahan
            $table->ipAddress('ip_address')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // User yang mengisi (opsional)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_skm');
    }
};
