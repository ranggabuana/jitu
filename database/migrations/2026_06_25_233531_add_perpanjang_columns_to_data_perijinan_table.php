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
        Schema::table('data_perijinan', function (Blueprint $table) {
            $table->unsignedBigInteger('perpanjang_dari_id')->nullable()->after('perijinan_id');
            $table->unsignedBigInteger('root_perpanjang_id')->nullable()->after('perpanjang_dari_id');
            
            $table->foreign('perpanjang_dari_id')->references('id')->on('data_perijinan')->onDelete('set null');
            $table->foreign('root_perpanjang_id')->references('id')->on('data_perijinan')->onDelete('set null');
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE data_perijinan MODIFY COLUMN status ENUM('draft', 'submitted', 'in_progress', 'perbaikan', 'approved', 'rejected', 'diperpanjang') NOT NULL DEFAULT 'submitted'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_perijinan', function (Blueprint $table) {
            $table->dropForeign(['root_perpanjang_id']);
            $table->dropForeign(['perpanjang_dari_id']);
            $table->dropColumn(['root_perpanjang_id', 'perpanjang_dari_id']);
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE data_perijinan MODIFY COLUMN status ENUM('draft', 'submitted', 'in_progress', 'perbaikan', 'approved', 'rejected') NOT NULL DEFAULT 'submitted'");
        }
    }
};
