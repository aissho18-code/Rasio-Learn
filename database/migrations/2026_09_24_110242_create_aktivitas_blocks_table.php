<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aktivitas_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aktivitas_id')->constrained('aktivitas')->cascadeOnDelete();
            $table->string('tahap')->default('Eksplorasi');
            $table->string('tipe');
            $table->string('judul')->nullable();
            $table->unsignedInteger('urutan')->default(0);
            $table->json('konfigurasi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aktivitas_blocks');
    }
};