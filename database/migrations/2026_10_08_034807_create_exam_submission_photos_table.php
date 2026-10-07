<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_submission_photos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('exam_submission_id')
                ->constrained('exam_submissions')
                ->cascadeOnDelete();

            $table->foreignId('exam_question_id')
                ->constrained('exam_questions')
                ->cascadeOnDelete();

            $table->string('photo_path');

            $table->timestamps();

            $table->unique([
                'exam_submission_id',
                'exam_question_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_submission_photos');
    }
};