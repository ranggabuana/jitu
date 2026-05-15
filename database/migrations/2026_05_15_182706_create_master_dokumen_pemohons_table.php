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
        Schema::create('master_dokumen_pemohons', function (Blueprint $table) {
            $table->id();
            $table->string('nama_dokumen');
            $table->string('tipe_data_file')->comment('Contoh: pdf,jpg,png');
            $table->enum('jenis', ['umum', 'spesifik'])->default('umum');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_dokumen_pemohons');
    }
};