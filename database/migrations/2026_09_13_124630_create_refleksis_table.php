<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Tabel Refleksi yang dibuat oleh Guru
        Schema::create('refleksis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('guru_id');
            $table->string('judul_refleksi');
            $table->unsignedInteger('pertemuan')->default(1);
            $table->text('deskripsi')->nullable();
            $table->json('questions')->nullable(); // Pertanyaan kustom atau default
            $table->enum('status', ['published', 'draft'])->default('published');
            $table->timestamps();

            $table->foreign('guru_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 2. Tabel Pengumpulan Jawaban Refleksi oleh Siswa
        Schema::create('refleksi_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('refleksi_id');
            $table->unsignedBigInteger('siswa_id');
            $table->json('answers')->nullable(); // Jawaban q1, q2, q3, q4
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('refleksi_id')->references('id')->on('refleksis')->onDelete('cascade');
            $table->foreign('siswa_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['refleksi_id', 'siswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refleksi_submissions');
        Schema::dropIfExists('refleksis');
    }
};