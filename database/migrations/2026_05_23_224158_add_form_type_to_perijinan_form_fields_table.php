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
        Schema::table('perijinan_form_fields', function (Blueprint $table) {
            $table->enum('form_type', ['global', 'rekom', 'izin'])->default('global')->after('perijinan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perijinan_form_fields', function (Blueprint $table) {
            $table->dropColumn('form_type');
        });
    }
};
