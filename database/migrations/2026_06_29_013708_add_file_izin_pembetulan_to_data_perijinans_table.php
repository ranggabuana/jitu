<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_perijinan', function (Blueprint $table) {
            $table->string('file_izin_pembetulan')->nullable()->after('file_izin_tte');
        });
    }

    public function down(): void
    {
        Schema::table('data_perijinan', function (Blueprint $table) {
            $table->dropColumn('file_izin_pembetulan');
        });
    }
};
