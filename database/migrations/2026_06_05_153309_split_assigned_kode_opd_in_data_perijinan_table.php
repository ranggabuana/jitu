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
        Schema::table('data_perijinan', function (Blueprint $table) {
            $table->string('no_rekom_kode', 50)->nullable()->after('no_rekom');
            $table->string('no_izin_kode', 50)->nullable()->after('no_izin');
            $table->dropColumn('assigned_kode_opd');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_perijinan', function (Blueprint $table) {
            $table->string('assigned_kode_opd', 50)->nullable()->after('no_izin');
            $table->dropColumn(['no_rekom_kode', 'no_izin_kode']);
        });
    }
};
