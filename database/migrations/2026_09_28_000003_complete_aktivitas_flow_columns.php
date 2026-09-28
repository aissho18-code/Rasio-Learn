<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aktivitas', function (Blueprint $table) {
            $table->timestamp('published_at')->nullable();
            $table->string('respons_type')->nullable();
            $table->text('pertanyaan')->nullable();
        });

        Schema::table('aktivitas_submissions', function (Blueprint $table) {
            $table->longText('text_answer')->nullable();
            $table->string('file_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('aktivitas_submissions', function (Blueprint $table) {
            $table->dropColumn(['text_answer', 'file_path']);
        });

        Schema::table('aktivitas', function (Blueprint $table) {
            $table->dropColumn(['published_at', 'respons_type', 'pertanyaan']);
        });
    }
};