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
        Schema::table('perijinan_form_fields', function (Blueprint $table) {
            // File configuration fields
            $table->string('file_types')->nullable()->after('options')->comment('Comma-separated list of allowed file types (e.g., pdf,doc,docx)');
            $table->integer('max_file_size')->nullable()->after('file_types')->comment('Maximum file size in KB');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perijinan_form_fields', function (Blueprint $table) {
            $table->dropColumn(['file_types', 'max_file_size']);
        });
    }
};
