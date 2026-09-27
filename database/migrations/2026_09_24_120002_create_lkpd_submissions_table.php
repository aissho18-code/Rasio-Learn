<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lkpd_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lkpd_id')->constrained('lkpds')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('users')->cascadeOnDelete();
            $table->json('jawaban')->nullable();
            $table->decimal('nilai', 5, 2)->nullable();
            $table->enum('status', ['draft', 'submitted', 'reviewed'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->unique(['lkpd_id', 'siswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lkpd_submissions');
    }
};