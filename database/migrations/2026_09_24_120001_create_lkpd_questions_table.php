<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lkpd_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lkpd_id')->constrained('lkpds')->cascadeOnDelete();
            $table->unsignedInteger('urutan')->default(1);
            $table->text('pertanyaan');
            $table->string('gambar_path')->nullable();
            $table->text('rubrik_jawaban')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lkpd_questions');
    }
};