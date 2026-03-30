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
            // Change columns to LONGTEXT for rich HTML content
            $table->longText('dasar_hukum')->change();
            $table->longText('persyaratan')->change();
            $table->longText('prosedur')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perijinan', function (Blueprint $table) {
            // Revert columns back to TEXT
            $table->text('dasar_hukum')->change();
            $table->text('persyaratan')->change();
            $table->text('prosedur')->change();
        });
    }
};
