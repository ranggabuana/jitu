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
        if (!Schema::hasColumn('data_perijinan', 'masa_aktif')) {
            Schema::table('data_perijinan', function (Blueprint $table) {
                $table->date('masa_aktif')->nullable()->after('no_izin_kode');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('data_perijinan', 'masa_aktif')) {
            Schema::table('data_perijinan', function (Blueprint $table) {
                $table->dropColumn('masa_aktif');
            });
        }
    }
};
