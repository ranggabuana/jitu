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
        Schema::table('master_dokumen_pemohons', function (Blueprint $table) {
            $table->integer('max_size')->default(2048)->after('jenis')->comment('Ukuran maksimal dalam KB');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_dokumen_pemohons', function (Blueprint $table) {
            $table->dropColumn('max_size');
        });
    }
};