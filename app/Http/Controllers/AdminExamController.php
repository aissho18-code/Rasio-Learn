<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamSubmission;
use App\Models\Kelas;
use App\Models\ProctoringLog;
use App\Notifications\LearningNotification;
use App\Support\LearningNotificationService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AdminExamController extends Controller
{
    public function index(Request $request)
    {
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $selectedClassId = $request->integer('kelas_id');

        $exams = Exam::with(['kelas', 'creator', 'questions'])
            ->when($selectedClassId > 0, fn ($query) => $query->where('kelas_id', $selectedClassId))
            ->latest()
            ->get();

        $submissions = ExamSubmission::with(['exam.questions', 'student'])
            ->whereNotNull('submitted_at')
            ->whereHas('exam', fn ($query) => $query->when($selectedClassId > 0, fn ($examQuery) => $examQuery->where('kelas_id', $selectedClassId)))
            ->latest('submitted_at')
            ->get();

        return view('admin-exams-index', compact('exams', 'kelasList', 'selectedClassId', 'submissions'));
    }

    public function create()
    {
        $kelasList = Kelas::orderBy('nama_kelas')->get();

        return view('admin-exams-form', [
            'exam' => new Exam(),
            'kelasList' => $kelasList,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedExamData($request);
        if ($data['status'] === 'published') {
            throw ValidationException::withMessages(['status' => 'Simpan sebagai draft, tambahkan soal, lalu publikasikan ujian.']);
        }

        $exam = Exam::create([
            ...$data,
            'created_by' => $request->user()->id,
            'locked' => false,
        ]);

        return redirect()->route('admin.exams.edit', $exam->id)
            ->with('status', 'Draft ujian tersimpan. Tambahkan soal sebelum dipublikasikan.');
    }

    public function edit($id)
    {
        $exam = Exam::with('questions')->findOrFail($id);
        $kelasList = Kelas::orderBy('nama_kelas')->get();

        return view('admin-exams-form', compact('exam', 'kelasList'));
    }

    public function update(Request $request, $id, LearningNotificationService $notifications)
    {
        $exam = Exam::findOrFail($id);
        $data = $this->validatedExamData($request);

        if ($data['status'] === 'published' && $exam->questions()->count() !== (int) $data['question_count']) {
            throw ValidationException::withMessages(['status' => 'Jumlah soal harus sesuai dengan pengaturan sebelum ujian dipublikasikan.']);
        }

        $wasPublished = $exam->status === 'published';
        $exam->update($data);

        if (!$wasPublished && $exam->status === 'published') {
            $this->notifyStudents($exam, $notifications);
        }

        return redirect()->route('admin.exams.edit', $exam->id)
            ->with('status', 'Ujian berhasil diperbarui.');
    }

    public function toggleLock($id)
    {
        $exam = Exam::findOrFail($id);
        $exam->update(['locked' => !$exam->locked]);

        return back()->with('status', $exam->locked ? 'Ujian berhasil dikunci.' : 'Ujian berhasil dibuka.');
    }

    public function destroy($id)
    {
        Exam::findOrFail($id)->delete();

        return back()->with('status', 'Ujian berhasil dihapus.');
    }

    public function exportLogs()
    {
        $logs = ProctoringLog::with('exam')->latest()->get();
        $csv = "Waktu,Siswa,Ujian,Tipe,Detail,Severity\n";

        foreach ($logs as $log) {
            $row = [$log->created_at, $log->student_id, $log->exam?->title, $log->type, $log->detail, $log->severity];
            $csv .= collect($row)->map(fn ($value) => '"' . str_replace('"', '""', (string) $value) . '"')->implode(',') . "\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="proctoring-logs.csv"',
        ]);
    }

    private function validatedExamData(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'kelas_id' => ['nullable', 'exists:kelas,id'],
            'exam_model' => ['required', 'in:cbt,essay,mixed,quiz_interactive'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:600'],
            'question_count' => ['required', 'integer', 'min:1', 'max:500'],
            'min_score' => ['required', 'integer', 'between:0,100'],
            'max_attempts' => ['required', 'integer', 'between:1,10'],
            'shuffle_questions' => ['required', 'boolean'],
            'shuffle_options' => ['required', 'boolean'],
            'show_score' => ['required', 'boolean'],
            'show_explanations' => ['required', 'boolean'],
            'status' => ['required', 'in:draft,published,closed'],
            'max_violations' => ['required', 'integer', 'min:1', 'max:20'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        foreach (['shuffle_questions', 'shuffle_options', 'show_score', 'show_explanations'] as $setting) {
            $data[$setting] = (bool) $data[$setting];
        }

        return $data;
    }

    private function notifyStudents(Exam $exam, LearningNotificationService $notifications): void
    {
        $notification = new LearningNotification(
            type: 'exam',
            title: 'Ujian Baru Tersedia',
            message: 'Ujian ' . $exam->title . ' telah diterbitkan oleh Admin.',
            url: route('siswa.ujian.index'),
            eventKey: 'exam.published:' . $exam->id . ':' . $exam->updated_at?->timestamp
        );

        if ($exam->kelas_id) {
            $notifications->notifyStudentsInClass((int) $exam->kelas_id, $notification);
        } else {
            $notifications->notifyAllStudents($notification);
        }
    }
}