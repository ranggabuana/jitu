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
        Schema::table('hasil_skm', function (Blueprint $table) {
            $table->string('jenis_kelamin')->nullable()->after('nip');
            $table->string('pendidikan')->nullable()->after('jenis_kelamin');
            $table->string('pekerjaan')->nullable()->after('pendidikan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil_skm', function (Blueprint $table) {
            $table->dropColumn(['jenis_kelamin', 'pendidikan', 'pekerjaan']);
        });
    }
};
