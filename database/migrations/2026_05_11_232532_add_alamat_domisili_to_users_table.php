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
            $table->renameColumn('alamat_lengkap', 'alamat_ktp');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_alamat_sama')->default(true)->after('alamat_ktp');
            $table->text('alamat_domisili')->nullable()->after('is_alamat_sama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_alamat_sama', 'alamat_domisili']);
            $table->renameColumn('alamat_ktp', 'alamat_lengkap');
        });
    }
};
