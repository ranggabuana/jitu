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
        Schema::table('data_skm', function (Blueprint $table) {
            $table->string('opsi_1')->nullable()->default('Kurang Baik')->after('status');
            $table->string('opsi_2')->nullable()->default('Cukup Baik')->after('opsi_1');
            $table->string('opsi_3')->nullable()->default('Baik')->after('opsi_2');
            $table->string('opsi_4')->nullable()->default('Sangat Baik')->after('opsi_3');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_skm', function (Blueprint $table) {
            $table->dropColumn(['opsi_1', 'opsi_2', 'opsi_3', 'opsi_4']);
        });
    }
};
