<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_perijinan', function (Blueprint $table) {
            $table->text('alasan_pembetulan')->nullable()->after('is_pembetulan');
        });
    }

    public function down(): void
    {
        Schema::table('data_perijinan', function (Blueprint $table) {
            $table->dropColumn('alasan_pembetulan');
        });
    }
};
