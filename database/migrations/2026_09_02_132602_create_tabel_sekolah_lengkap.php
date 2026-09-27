<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelas');
            $table->foreignId('wali_kelas_id')->nullable();
            $table->timestamps();
        });

        Schema::create('mata_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->string('nama_mapel');
            $table->foreignId('guru_id')->nullable();
            $table->timestamps();
        });

        Schema::create('guru_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('nip')->nullable();
            $table->foreignId('mapel_id')->nullable();
            $table->timestamps();
        });

        Schema::create('siswa_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('nis')->nullable();
            $table->foreignId('kelas_id')->nullable();
            $table->timestamps();
        });

        Schema::create('materi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id');
            $table->foreignId('mapel_id');
            $table->integer('urutan');
            $table->string('judul');
            $table->string('pekan')->nullable();
            $table->text('konten');
            $table->timestamps();
        });

        Schema::create('materi_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id');
            $table->foreignId('materi_id');
            $table->string('status')->default('locked');
            $table->foreignId('unlocked_by')->nullable();
            $table->timestamp('unlocked_at')->nullable();
            $table->timestamps();
        });

        Schema::create('tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('materi_id');
            $table->string('judul');
            $table->text('instruksi')->nullable();
            $table->dateTime('deadline')->nullable();
            $table->json('rubrik_penilaian')->nullable();
            $table->timestamps();
        });

        Schema::create('submission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id');
            $table->foreignId('tugas_id');
            $table->text('jawaban')->nullable();
            $table->string('file_path')->nullable();
            $table->float('ai_score')->nullable();
            $table->text('ai_feedback')->nullable();
            $table->float('final_score')->nullable();
            $table->text('final_feedback')->nullable();
            $table->string('status')->default('menunggu');
            $table->timestamps();
        });

        Schema::create('pre_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id');
            $table->text('detail')->nullable();
            $table->timestamps();
        });

        Schema::create('pre_test_hasils', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pre_test_id');
            $table->foreignId('siswa_id');
            $table->float('skor')->nullable();
            $table->json('jawaban')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        //
    }
};