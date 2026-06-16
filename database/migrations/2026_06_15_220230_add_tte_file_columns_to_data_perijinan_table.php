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
            $table->string('file_rekom_tte')->nullable()->after('file_rekom');
            $table->string('file_izin_tte')->nullable()->after('file_izin');
            $table->json('file_rekom_multi_tte')->nullable()->after('file_rekom_multi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_perijinan', function (Blueprint $table) {
            $table->dropColumn(['file_rekom_tte', 'file_izin_tte', 'file_rekom_multi_tte']);
        });
    }
};
