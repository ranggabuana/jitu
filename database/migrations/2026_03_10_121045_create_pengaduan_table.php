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
        Schema::create('pengaduan', function (Blueprint $table) {
            $table->id();
            $table->string('no_pengaduan')->unique();
            $table->string('nama');
            $table->string('email');
            $table->string('no_hp');
            $table->string('kategori');
            $table->text('isi_pengaduan');
            $table->string('lampiran')->nullable();
            $table->string('file_type')->nullable();
            $table->integer('file_size')->nullable();
            $table->enum('status', ['pending', 'proses', 'selesai', 'ditolak'])->default('pending');
            $table->text('respon')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('tanggal_pengaduan')->useCurrent();
            $table->timestamp('tanggal_respon')->nullable();
            $table->timestamps();
            
            $table->index('no_pengaduan');
            $table->index('status');
            $table->index('tanggal_pengaduan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaduan');
    }
};
