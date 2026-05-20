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
        Schema::table('perijinan', function (Blueprint $table) {
            $table->longText('template_pernyataan')->nullable()->after('gambar_alur');
            $table->longText('template_permohonan')->nullable()->after('template_pernyataan');
            $table->longText('template_keabsahan')->nullable()->after('template_permohonan');
        });

        Schema::table('data_perijinan', function (Blueprint $table) {
            $table->string('file_pernyataan')->nullable()->after('form_files');
            $table->string('file_permohonan')->nullable()->after('file_pernyataan');
            $table->string('file_keabsahan')->nullable()->after('file_permohonan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perijinan', function (Blueprint $table) {
            $table->dropColumn(['template_pernyataan', 'template_permohonan', 'template_keabsahan']);
        });

        Schema::table('data_perijinan', function (Blueprint $table) {
            $table->dropColumn(['file_pernyataan', 'file_permohonan', 'file_keabsahan']);
        });
    }
};
