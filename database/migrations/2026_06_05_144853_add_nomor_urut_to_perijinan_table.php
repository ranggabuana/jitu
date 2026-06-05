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
        Schema::table('perijinan', function (Blueprint $blueprint) {
            $blueprint->integer('next_nomor_rekom')->default(1)->after('template_surat_rekom');
            $blueprint->integer('next_nomor_izin')->default(1)->after('template_surat_izin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perijinan', function (Blueprint $blueprint) {
            $blueprint->dropColumn(['next_nomor_rekom', 'next_nomor_izin']);
        });
    }
};
