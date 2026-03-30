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
        Schema::create('perijinan_validation_flows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perijinan_id')->constrained('perijinan')->onDelete('cascade');
            $table->string('role'); // Role yang melakukan validasi (fo, bo, operator_opd, kepala_opd, verifikator, kadin)
            $table->integer('order')->default(0); // Urutan validasi
            $table->boolean('is_active')->default(true); // Status aktif/tidak aktif
            $table->text('description')->nullable(); // Deskripsi tugas validasi
            $table->integer('sla_hours')->nullable(); // SLA dalam jam untuk validasi
            $table->timestamps();

            $table->index(['perijinan_id', 'order']);
            $table->index(['perijinan_id', 'role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perijinan_validation_flows');
    }
};
