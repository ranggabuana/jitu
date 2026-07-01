<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('jenis_kelamin')->nullable()->after('email');
            $table->string('pendidikan')->nullable()->after('jenis_kelamin');
            $table->string('pekerjaan')->nullable()->after('pendidikan');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['jenis_kelamin', 'pendidikan', 'pekerjaan']);
        });
    }
};
