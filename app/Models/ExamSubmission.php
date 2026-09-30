<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSubmission extends Model
{
    protected $fillable = [
        'exam_id',
        'student_id',
        'response',
        'submitted_at',
        'score',
        'feedback',
        'graded_at',
        'attempt_number',
        'answers',
        'essay_scores',
        'earned_points',
        'correct_count',
        'wrong_count',
        'started_at',
        'completed_at',
        'duration_seconds',
        'auto_submitted',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'answers' => 'array',
        'essay_scores' => 'array',
        'earned_points' => 'decimal:2',
        'auto_submitted' => 'boolean',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}