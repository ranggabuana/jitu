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
        Schema::create('user_dokumens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('master_dokumen_id')->constrained('master_dokumen_pemohons')->cascadeOnDelete();
            $table->string('file_path');
            $table->timestamps();
            
            // Seorang pemohon biasanya hanya butuh 1 file untuk 1 master dokumen
            $table->unique(['user_id', 'master_dokumen_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_dokumens');
    }
};