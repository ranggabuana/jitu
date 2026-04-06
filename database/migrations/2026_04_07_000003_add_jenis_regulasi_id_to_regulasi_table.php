<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('regulasi', function (Blueprint $table) {
            $table->foreignId('jenis_regulasi_id')->nullable()->after('id')->constrained('jenis_regulasi')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('regulasi', function (Blueprint $table) {
            $table->dropForeign(['jenis_regulasi_id']);
            $table->dropColumn('jenis_regulasi_id');
        });
    }
};
