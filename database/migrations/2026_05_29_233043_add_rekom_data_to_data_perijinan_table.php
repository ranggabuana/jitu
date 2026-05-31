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
            $table->json('rekom_data')->nullable()->after('form_data');
            $table->json('izin_data')->nullable()->after('rekom_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_perijinan', function (Blueprint $table) {
            $table->dropColumn(['rekom_data', 'izin_data']);
        });
    }
};
