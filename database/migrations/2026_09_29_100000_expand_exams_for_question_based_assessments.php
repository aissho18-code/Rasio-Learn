<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->string('exam_model', 30)->default('cbt');
            $table->unsignedInteger('duration_minutes')->default(60);
            $table->unsignedInteger('question_count')->default(0);
            $table->unsignedTinyInteger('min_score')->default(0);
            $table->unsignedTinyInteger('max_attempts')->default(1);
            $table->boolean('shuffle_questions')->default(false);
            $table->boolean('shuffle_options')->default(false);
            $table->boolean('show_score')->default(true);
            $table->boolean('show_explanations')->default(false);
            $table->string('status', 20)->default('published');
            $table->index(['kelas_id', 'status']);
        });

        Schema::create('exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->longText('prompt');
            $table->string('type', 30);
            $table->json('options')->nullable();
            $table->json('correct_answer')->nullable();
            $table->text('explanation')->nullable();
            $table->decimal('points', 8, 2)->default(1);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
            $table->index(['exam_id', 'position']);
        });

        Schema::table('exam_submissions', function (Blueprint $table) {
            $table->dropUnique('exam_submissions_exam_id_student_id_unique');
            $table->unsignedTinyInteger('attempt_number')->default(1);
            $table->json('answers')->nullable();
            $table->json('essay_scores')->nullable();
            $table->decimal('earned_points', 8, 2)->nullable();
            $table->unsignedInteger('correct_count')->nullable();
            $table->unsignedInteger('wrong_count')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->boolean('auto_submitted')->default(false);
            $table->unique(['exam_id', 'student_id', 'attempt_number'], 'exam_submission_attempt_unique');
        });

        DB::table('exam_submissions')
            ->whereNotNull('submitted_at')
            ->update(['completed_at' => DB::raw('submitted_at')]);
    }

    public function down(): void
    {
        Schema::table('exam_submissions', function (Blueprint $table) {
            $table->dropUnique('exam_submission_attempt_unique');
            $table->dropColumn([
                'attempt_number', 'answers', 'essay_scores', 'earned_points',
                'correct_count', 'wrong_count', 'started_at', 'completed_at',
                'duration_seconds', 'auto_submitted',
            ]);
            $table->unique(['exam_id', 'student_id']);
        });

        Schema::dropIfExists('exam_questions');

        Schema::table('exams', function (Blueprint $table) {
            $table->dropIndex(['kelas_id', 'status']);
            $table->dropColumn([
                'exam_model', 'duration_minutes', 'question_count', 'min_score',
                'max_attempts', 'shuffle_questions', 'shuffle_options', 'show_score',
                'show_explanations', 'status',
            ]);
        });
    }
};