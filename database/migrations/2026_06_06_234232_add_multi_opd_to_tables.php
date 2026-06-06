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
            $table->boolean('is_multi_opd')->default(false)->after('nama_perijinan');
        });

        Schema::table('data_perijinan', function (Blueprint $table) {
            $table->json('rekom_data_multi')->nullable()->after('rekom_data');
            $table->json('file_rekom_multi')->nullable()->after('file_rekom');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perijinan', function (Blueprint $table) {
            $table->dropColumn('is_multi_opd');
        });

        Schema::table('data_perijinan', function (Blueprint $table) {
            $table->dropColumn(['rekom_data_multi', 'file_rekom_multi']);
        });
    }
};
