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
            $table->enum('role', ['admin', 'fo', 'bo', 'operator_opd', 'kepala_opd', 'verifikator', 'kadin'])->default('admin')->after('email');
            $table->unsignedBigInteger('opd_id')->nullable()->after('role');
            $table->foreign('opd_id')->references('id')->on('opd')->onDelete('set null');
            $table->string('nip')->nullable()->after('opd_id');
            $table->string('no_hp')->nullable()->after('nip');
            $table->enum('status', ['aktif', 'tidak_aktif'])->default('aktif')->after('no_hp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['opd_id']);
            $table->dropColumn(['role', 'opd_id', 'nip', 'no_hp', 'status']);
        });
    }
};
