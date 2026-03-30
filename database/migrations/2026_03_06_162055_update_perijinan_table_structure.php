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
        Schema::table('perijinan', function (Blueprint $table) {
            // Drop existing columns
            $table->dropColumn(['deskripsi', 'kode_perijinan', 'lama_hari', 'biaya', 'status']);
            
            // Add new columns
            $table->text('dasar_hukum')->nullable()->after('nama_perijinan');
            $table->text('persyaratan')->nullable()->after('dasar_hukum');
            $table->text('prosedur')->nullable()->after('persyaratan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perijinan', function (Blueprint $table) {
            // Drop new columns
            $table->dropColumn(['dasar_hukum', 'persyaratan', 'prosedur']);
            
            // Restore old columns
            $table->text('deskripsi')->nullable()->after('nama_perijinan');
            $table->string('kode_perijinan')->unique()->nullable()->after('deskripsi');
            $table->integer('lama_hari')->default(0)->after('kode_perijinan');
            $table->decimal('biaya', 15, 2)->default(0)->after('lama_hari');
            $table->enum('status', ['aktif', 'tidak_aktif'])->default('aktif')->after('biaya');
        });
    }
};
