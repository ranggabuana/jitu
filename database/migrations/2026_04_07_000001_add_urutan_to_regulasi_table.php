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
            $table->integer('urutan')->default(0)->after('user_id');
        });

        // Initialize urutan based on existing data (order by created_at)
        DB::statement('SET @row_number = 0');
        DB::statement('UPDATE regulasi SET urutan = (@row_number := @row_number + 1) ORDER BY created_at ASC');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('regulasi', function (Blueprint $table) {
            $table->dropColumn('urutan');
        });
    }
};
