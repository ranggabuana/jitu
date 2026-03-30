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
        Schema::create('data_skm', function (Blueprint $table) {
            $table->id();
            $table->string('pertanyaan');
            $table->integer('bobot_max')->default(4); // Bobot maksimal fixed 4
            $table->integer('urutan')->default(0);
            $table->enum('status', ['aktif', 'tidak_aktif'])->default('aktif');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_skm');
    }
};
