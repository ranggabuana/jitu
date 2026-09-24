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
        // 1. Synchronize existing perijinan sharing the same kode_perijinan
        $groups = DB::table('perijinan')
            ->select('kode_perijinan', DB::raw('MAX(next_nomor_rekom) as max_rekom'), DB::raw('MAX(next_nomor_izin) as max_izin'))
            ->whereNotNull('kode_perijinan')
            ->where('kode_perijinan', '!=', '')
            ->groupBy('kode_perijinan')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($groups as $group) {
            DB::table('perijinan')
                ->where('kode_perijinan', $group->kode_perijinan)
                ->update([
                    'next_nomor_rekom' => $group->max_rekom ?? 1,
                    'next_nomor_izin' => $group->max_izin ?? 1,
                ]);
        }

        // 2. Add index on kode_perijinan if not already indexed
        if (!Schema::hasIndex('perijinan', 'perijinan_kode_perijinan_index')) {
            Schema::table('perijinan', function (Blueprint $table) {
                $table->index('kode_perijinan', 'perijinan_kode_perijinan_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasIndex('perijinan', 'perijinan_kode_perijinan_index')) {
            Schema::table('perijinan', function (Blueprint $table) {
                $table->dropIndex('perijinan_kode_perijinan_index');
            });
        }
    }
};
