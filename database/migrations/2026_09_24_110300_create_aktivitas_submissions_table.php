<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aktivitas_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aktivitas_id')->constrained('aktivitas')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('users')->cascadeOnDelete();
            $table->json('jawaban')->nullable();
            $table->enum('status', ['draft', 'submitted'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->unique(['aktivitas_id', 'siswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aktivitas_submissions');
    }
};