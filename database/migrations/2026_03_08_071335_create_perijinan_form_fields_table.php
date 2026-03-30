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
        Schema::create('perijinan_form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perijinan_id')->constrained('perijinan')->onDelete('cascade');
            $table->string('label'); // Label yang ditampilkan
            $table->string('name'); // Name field untuk form
            $table->string('type'); // text, textarea, number, date, email, phone, select, radio, checkbox, file
            $table->boolean('is_required')->default(false);
            $table->text('placeholder')->nullable();
            $table->text('help_text')->nullable(); // Teks bantuan di bawah field
            $table->json('options')->nullable(); // Untuk select/radio/checkbox dalam format JSON
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('perijinan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perijinan_form_fields');
    }
};
