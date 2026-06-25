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
            $table->boolean('has_bo_form')->default(false)->after('is_multi_opd');
        });

        Schema::table('data_perijinan', function (Blueprint $table) {
            $table->json('bo_data')->nullable()->after('izin_data');
        });

        // Modify form_type enum in perijinan_form_fields
        if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE perijinan_form_fields MODIFY COLUMN form_type ENUM('global', 'rekom', 'izin', 'bo') NOT NULL DEFAULT 'global'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perijinan', function (Blueprint $table) {
            $table->dropColumn('has_bo_form');
        });

        Schema::table('data_perijinan', function (Blueprint $table) {
            $table->dropColumn('bo_data');
        });

        // Revert form_type enum
        if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE perijinan_form_fields MODIFY COLUMN form_type ENUM('global', 'rekom', 'izin') NOT NULL DEFAULT 'global'");
        }
    }
};
