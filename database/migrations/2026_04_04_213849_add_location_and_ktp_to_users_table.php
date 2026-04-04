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
            $table->unsignedBigInteger('provinsi_id')->nullable()->after('no_hp');
            $table->unsignedBigInteger('kabupaten_id')->nullable()->after('provinsi_id');
            $table->unsignedBigInteger('kecamatan_id')->nullable()->after('kabupaten_id');
            $table->unsignedBigInteger('kelurahan_id')->nullable()->after('kecamatan_id');
            $table->text('alamat_lengkap')->nullable()->after('kelurahan_id');
            $table->string('foto_ktp')->nullable()->after('alamat_lengkap');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['provinsi_id', 'kabupaten_id', 'kecamatan_id', 'kelurahan_id', 'alamat_lengkap', 'foto_ktp']);
        });
    }
};
