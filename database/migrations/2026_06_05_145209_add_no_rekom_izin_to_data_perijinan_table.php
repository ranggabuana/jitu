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
        Schema::table('data_perijinan', function (Blueprint $blueprint) {
            $blueprint->integer('no_rekom')->nullable()->after('rekom_data');
            $blueprint->integer('no_izin')->nullable()->after('izin_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_perijinan', function (Blueprint $blueprint) {
            $blueprint->dropColumn(['no_rekom', 'no_izin']);
        });
    }
};
