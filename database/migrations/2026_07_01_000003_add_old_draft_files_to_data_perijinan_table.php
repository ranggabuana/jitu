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
            $table->string('file_rekom_pembetulan_old')->nullable()->after('file_rekom_multi_tte_pembetulan_old');
            $table->string('file_izin_pembetulan_old')->nullable()->after('file_rekom_pembetulan_old');
            $table->text('file_rekom_multi_pembetulan_old')->nullable()->after('file_izin_pembetulan_old');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_perijinan', function (Blueprint $table) {
            $table->dropColumn([
                'file_rekom_pembetulan_old',
                'file_izin_pembetulan_old',
                'file_rekom_multi_pembetulan_old',
            ]);
        });
    }
};
