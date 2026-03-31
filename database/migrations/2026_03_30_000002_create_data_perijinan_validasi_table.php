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
        Schema::create('data_perijinan_validasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('data_perijinan_id')->constrained('data_perijinan')->onDelete('cascade');
            $table->foreignId('validation_flow_id')->constrained('perijinan_validation_flows')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null')->comment('Validator yang ditugaskan');
            $table->enum('status', [
                'pending',     // Menunggu validasi
                'approved',    // Disetujui
                'rejected',    // Ditolak
                'revision'     // Perlu perbaikan
            ])->default('pending');
            $table->text('catatan')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->integer('order')->comment('Urutan validasi');
            $table->timestamps();
            
            $table->index(['data_perijinan_id', 'order']);
            $table->index(['user_id', 'status']);
            
            // Unique constraint dengan nama custom yang pendek
            $table->unique(['data_perijinan_id', 'validation_flow_id'], 'unique_data_validasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_perijinan_validasi');
    }
};
