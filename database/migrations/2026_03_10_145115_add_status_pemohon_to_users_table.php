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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status_pemohon', ['perorangan', 'badan_usaha'])->default('perorangan')->after('role');
            $table->string('nama_perusahaan')->nullable()->after('status_pemohon');
            $table->string('npwp')->nullable()->after('nama_perusahaan');
            
            // Add unique index for nik + status_pemohon combination
            $table->index(['nip', 'status_pemohon']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
