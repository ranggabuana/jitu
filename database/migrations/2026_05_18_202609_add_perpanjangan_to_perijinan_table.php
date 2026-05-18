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
        Schema::table('perijinan', function (Blueprint $blueprint) {
            $blueprint->enum('opsi_perpanjangan', ['setelah_habis', 'sebelum_habis', 'keduanya'])->nullable()->after('nama_perijinan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perijinan', function (Blueprint $blueprint) {
            $blueprint->dropColumn('opsi_perpanjangan');
        });
    }
};
