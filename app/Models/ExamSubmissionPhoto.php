<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSubmissionPhoto extends Model
{
    protected $fillable = [
        'exam_submission_id',
        'exam_question_id',
        'photo_path',
    ];

    public function submission()
    {
        return $this->belongsTo(ExamSubmission::class, 'exam_submission_id');
    }

    public function question()
    {
        return $this->belongsTo(ExamQuestion::class, 'exam_question_id');
    }
public function photos()
{
    return $this->hasMany(ExamSubmissionPhoto::class, 'exam_submission_id');
}
}