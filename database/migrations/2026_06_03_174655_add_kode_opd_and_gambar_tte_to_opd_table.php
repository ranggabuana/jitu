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
        Schema::table('opd', function (Blueprint $table) {
            $table->string('kode_opd', 100)->nullable()->after('nama_opd');
            $table->string('gambar_tte')->nullable()->after('kode_opd');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('opd', function (Blueprint $table) {
            $table->dropColumn(['kode_opd', 'gambar_tte']);
        });
    }
};
