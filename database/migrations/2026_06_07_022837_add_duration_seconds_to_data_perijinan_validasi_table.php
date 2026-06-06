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
        Schema::table('data_perijinan_validasi', function (Blueprint $table) {
            $table->unsignedBigInteger('duration_seconds')->default(0)->after('validated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_perijinan_validasi', function (Blueprint $table) {
            $table->dropColumn('duration_seconds');
        });
    }
};
