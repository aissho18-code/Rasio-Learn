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
    ];

    protected $casts = [
        'locked' => 'boolean',
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
}