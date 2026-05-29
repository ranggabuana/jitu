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
            $table->longText('template_surat_rekom')->nullable()->after('persyaratan');
            $table->longText('template_surat_izin')->nullable()->after('template_surat_rekom');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perijinan', function (Blueprint $table) {
            $table->dropColumn(['template_surat_rekom', 'template_surat_izin']);
        });
    }
};
