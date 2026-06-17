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
        Schema::create('perijinan_opd_configs', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('perijinan_id')->constrained('perijinan')->onDelete('cascade');
            $blueprint->foreignId('opd_id')->constrained('opd')->onDelete('cascade');
            $blueprint->text('template_surat_rekom')->nullable();
            $blueprint->text('keterangan_rekom')->nullable();
            $blueprint->integer('next_nomor_rekom')->default(1);
            $blueprint->timestamps();

            $blueprint->unique(['perijinan_id', 'opd_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perijinan_opd_configs');
    }
};
