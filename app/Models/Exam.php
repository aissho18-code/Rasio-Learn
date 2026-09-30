<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = [
        'title',
        'description',
        'kelas_id',
        'created_by',
        'locked',
        'max_violations',
        'starts_at',
        'ends_at',
        'exam_model',
        'duration_minutes',
        'question_count',
        'min_score',
        'max_attempts',
        'shuffle_questions',
        'shuffle_options',
        'show_score',
        'show_explanations',
        'status',
    ];

    protected $casts = [
        'locked' => 'boolean',
        'shuffle_questions' => 'boolean',
        'shuffle_options' => 'boolean',
        'show_score' => 'boolean',
        'show_explanations' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function logs()
    {
        return $this->hasMany(ProctoringLog::class, 'exam_id');
    }

    public function submissions()
    {
        return $this->hasMany(ExamSubmission::class);
    }

    public function questions()
    {
        return $this->hasMany(ExamQuestion::class)->orderBy('position');
    }
}