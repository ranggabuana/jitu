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
            $table->unsignedBigInteger('pembetulan_dari_id')->nullable()->after('perpanjang_dari_id');
            $table->foreign('pembetulan_dari_id')->references('id')->on('data_perijinan')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_perijinan', function (Blueprint $table) {
            $table->dropForeign(['pembetulan_dari_id']);
            $table->dropColumn('pembetulan_dari_id');
        });
    }
};
