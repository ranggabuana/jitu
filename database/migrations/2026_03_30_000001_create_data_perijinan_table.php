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
        Schema::create('data_perijinan', function (Blueprint $table) {
            $table->id();
            $table->string('no_registrasi')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('perijinan_id')->constrained('perijinan')->onDelete('cascade');
            $table->enum('status', [
                'draft',           // Masih draft
                'submitted',       // Sudah diajukan, menunggu validasi pertama
                'in_progress',     // Sedang dalam proses validasi
                'perbaikan',       // Perlu perbaikan
                'approved',        // Sudah disetujui semua tahap
                'rejected'         // Ditolak
            ])->default('submitted');
            $table->json('form_data')->nullable()->comment('Data dari form fields');
            $table->json('data_pemohon')->nullable()->comment('Snapshot data pemohon saat mengajukan');
            $table->text('catatan_perbaikan')->nullable();
            $table->text('catatan_reject')->nullable();
            $table->integer('current_step')->default(1)->comment('Tahap validasi saat ini');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['perijinan_id', 'status']);
            $table->index('no_registrasi');
            $table->index(['status', 'current_step']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_perijinan');
    }
};
