<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamSubmission;
use App\Models\Kelas;
use App\Notifications\LearningNotification;
use App\Support\LearningNotificationService;
use Illuminate\Http\Request;

class SiswaExamController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $kelasId = optional($user->siswaProfile)->kelas_id;

        $exams = Exam::with(['kelas', 'creator'])
            ->where(function ($query) use ($kelasId) {
                $query->whereNull('kelas_id')
                      ->orWhere('kelas_id', $kelasId);
            })
            ->where('locked', false)
            ->latest()
            ->get();

        return view('siswa-ujian-index', compact('exams'));
    }

    public function show($id)
    {
        $student = request()->user();
        $kelasId = optional($student->siswaProfile)->kelas_id;
        $exam = Exam::with(['kelas', 'submissions' => fn ($query) => $query->where('student_id', $student->id)])
            ->where(function ($query) use ($kelasId) {
                $query->whereNull('kelas_id')->orWhere('kelas_id', $kelasId);
            })
            ->findOrFail($id);

        if ($exam->locked) {
            return redirect()->route('siswa.ujian.index')->with('error', 'Ujian ini sedang dikunci oleh pengawas/admin.');
        }

        return view('siswa-ujian-show', compact('exam'));
    }

    public function result(Request $request, ExamSubmission $submission)
    {
        abort_unless(
            (int) $submission->student_id === (int) $request->user()->id
                && $submission->graded_at !== null,
            404
        );

        $submission->load('exam.kelas');
        $exam = $submission->exam;
        $exam->setRelation('submissions', collect([$submission]));
        $isResultOnly = true;

        return view('siswa-ujian-show', compact('exam', 'isResultOnly'));
    }

    public function submit(Request $request, $id, LearningNotificationService $notifications)
    {
        $student = $request->user();
        $kelasId = optional($student->siswaProfile)->kelas_id;
        $exam = Exam::where(function ($query) use ($kelasId) {
            $query->whereNull('kelas_id')->orWhere('kelas_id', $kelasId);
        })->findOrFail($id);

        abort_if($exam->locked, 403, 'Ujian ini sedang dikunci.');
        abort_if($exam->starts_at && now()->lt($exam->starts_at), 403, 'Ujian belum dimulai.');
        abort_if($exam->ends_at && now()->gt($exam->ends_at), 403, 'Waktu ujian telah berakhir.');

        $data = $request->validate([
            'response' => ['required', 'string', 'max:50000'],
        ]);

        $submission = ExamSubmission::updateOrCreate(
            ['exam_id' => $exam->id, 'student_id' => $student->id],
            [
                'response' => $data['response'],
                'submitted_at' => now(),
                'score' => null,
                'feedback' => null,
                'graded_at' => null,
            ]
        );

        $teacher = $exam->creator;
        if (!$teacher && $exam->kelas_id) {
            $teacher = Kelas::find($exam->kelas_id)?->wali;
        }

        if ($teacher) {
            $notifications->sendOnce($teacher, new LearningNotification(
                type: 'exam',
                title: 'Pengumpulan Ujian Baru',
                message: $student->name . ' telah menyelesaikan ujian ' . $exam->title . '.',
                url: $teacher->role === 'admin' ? route('admin.exams.index') : route('guru.ujian.index'),
                eventKey: 'exam.submitted:' . $submission->id . ':' . $submission->updated_at->timestamp
            ));
        }

        return redirect()->route('siswa.ujian.show', $exam->id)->with('success', 'Jawaban ujian berhasil dikirim.');
    }
}