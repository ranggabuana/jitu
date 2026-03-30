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
        Schema::create('perijinan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perijinan');
            $table->text('deskripsi')->nullable();
            $table->string('kode_perijinan')->unique()->nullable();
            $table->integer('lama_hari')->default(0)->comment('Lama proses dalam hari');
            $table->decimal('biaya', 15, 2)->default(0)->comment('Biaya perijinan');
            $table->enum('status', ['aktif', 'tidak_aktif'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perijinan');
    }
};
