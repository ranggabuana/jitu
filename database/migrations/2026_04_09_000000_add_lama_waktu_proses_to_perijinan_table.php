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
            $table->string('lama_waktu_proses')->nullable()->after('informasi_biaya');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perijinan', function (Blueprint $table) {
            $table->dropColumn('lama_waktu_proses');
        });
    }
};
