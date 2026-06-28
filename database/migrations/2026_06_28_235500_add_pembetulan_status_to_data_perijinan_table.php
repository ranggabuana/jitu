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
        // Add 'diperbaiki' to status enum
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE data_perijinan MODIFY COLUMN status ENUM('draft', 'submitted', 'in_progress', 'perbaikan', 'approved', 'rejected', 'diperpanjang', 'diperbaiki') NOT NULL DEFAULT 'submitted'");
        }

        Schema::table('data_perijinan', function (Blueprint $table) {
            if (!Schema::hasColumn('data_perijinan', 'is_pembetulan')) {
                $table->boolean('is_pembetulan')->default(false)->after('pembetulan_dari_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_perijinan', function (Blueprint $table) {
            if (Schema::hasColumn('data_perijinan', 'is_pembetulan')) {
                $table->dropColumn('is_pembetulan');
            }
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE data_perijinan MODIFY COLUMN status ENUM('draft', 'submitted', 'in_progress', 'perbaikan', 'approved', 'rejected', 'diperpanjang') NOT NULL DEFAULT 'submitted'");
        }
    }
};
