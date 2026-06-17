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
        Schema::table('perijinan_form_fields', function (Blueprint $blueprint) {
            $blueprint->foreignId('opd_id')->nullable()->after('perijinan_id')->constrained('opd')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perijinan_form_fields', function (Blueprint $blueprint) {
            $blueprint->dropForeign(['opd_id']);
            $blueprint->dropColumn('opd_id');
        });
    }
};
