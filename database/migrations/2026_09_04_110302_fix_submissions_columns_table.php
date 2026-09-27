<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('submissions', 'status')) {
                $table->string('status')->default('menunggu')->nullable()->after('file_path');
            }
            if (!Schema::hasColumn('submissions', 'jawaban')) {
                $table->text('jawaban')->nullable()->after('tugas_id');
            }
            if (!Schema::hasColumn('submissions', 'ai_score')) {
                $table->integer('ai_score')->nullable();
            }
            if (!Schema::hasColumn('submissions', 'ai_feedback')) {
                $table->text('ai_feedback')->nullable();
            }
            if (!Schema::hasColumn('submissions', 'final_score')) {
                $table->integer('final_score')->nullable();
            }
            if (!Schema::hasColumn('submissions', 'final_feedback')) {
                $table->text('final_feedback')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn(['status', 'jawaban', 'ai_score', 'ai_feedback', 'final_score', 'final_feedback']);
        });
    }
};