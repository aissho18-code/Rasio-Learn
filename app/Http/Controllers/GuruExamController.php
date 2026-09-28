<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamSubmission;
use App\Models\Kelas;
use App\Notifications\LearningNotification;
use App\Support\LearningNotificationService;
use Illuminate\Http\Request;

class GuruExamController extends Controller
{
    public function index(Request $request)
    {
        $guru = $request->user();

        $kelasGuru = Kelas::where('wali_kelas_id', $guru->id)
            ->orderBy('nama_kelas')
            ->get();

        $kelasIds = $kelasGuru->pluck('id');

        $exams = Exam::with('kelas')
            ->where('created_by', $guru->id)
            ->orWhereIn('kelas_id', $kelasIds)
            ->latest()
            ->get();

        $submissions = ExamSubmission::with(['student', 'exam'])
            ->whereHas('exam', fn ($query) => $query
                ->where('created_by', $guru->id)
                ->orWhereIn('kelas_id', $kelasIds))
            ->latest('submitted_at')
            ->get();

        return view('guru-ujian-index', compact('exams', 'kelasGuru', 'submissions'));
    }

    public function create(Request $request)
    {
        $kelasGuru = Kelas::where('wali_kelas_id', $request->user()->id)
            ->orderBy('nama_kelas')
            ->get();

        return view('guru-ujian-form', [
            'exam' => new Exam(),
            'kelasGuru' => $kelasGuru,
        ]);
    }

    public function store(Request $request, LearningNotificationService $notifications)
    {
        $guru = $request->user();

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'kelas_id' => ['required', 'exists:kelas,id'],
            'max_violations' => ['required', 'integer', 'min:1', 'max:20'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        $this->ensureTeacherOwnsClass($guru->id, (int) $data['kelas_id']);

        $exam = Exam::create([
            ...$data,
            'created_by' => $guru->id,
            'locked' => false,
        ]);

        $notifications->notifyStudentsInClass((int) $exam->kelas_id, new LearningNotification(
            type: 'exam',
            title: 'Ujian Baru Tersedia',
            message: 'Ujian ' . $exam->title . ' telah diterbitkan untuk kelas Anda.',
            url: route('siswa.ujian.index'),
            eventKey: 'exam.published:' . $exam->id
        ));

        return redirect()
            ->route('guru.ujian.index')
            ->with('status', 'Ujian berhasil dibuat.');
    }

    public function edit(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);
        $this->ensureTeacherCanManage($request->user()->id, $exam);

        $kelasGuru = Kelas::where('wali_kelas_id', $request->user()->id)
            ->orderBy('nama_kelas')
            ->get();

        return view('guru-ujian-form', compact('exam', 'kelasGuru'));
    }

    public function update(Request $request, $id)
    {
        $guru = $request->user();
        $exam = Exam::findOrFail($id);

        $this->ensureTeacherCanManage($guru->id, $exam);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'kelas_id' => ['required', 'exists:kelas,id'],
            'max_violations' => ['required', 'integer', 'min:1', 'max:20'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        $this->ensureTeacherOwnsClass($guru->id, (int) $data['kelas_id']);

        $exam->update($data);

        return redirect()
            ->route('guru.ujian.index')
            ->with('status', 'Ujian berhasil diperbarui.');
    }

    public function grade(Request $request, ExamSubmission $submission, LearningNotificationService $notifications)
    {
        $guru = $request->user();
        $submission->load('exam', 'student');
        $this->ensureTeacherCanManage($guru->id, $submission->exam);

        $data = $request->validate([
            'score' => ['required', 'integer', 'between:0,100'],
            'feedback' => ['nullable', 'string', 'max:5000'],
        ]);

        $submission->update([
            'score' => $data['score'],
            'feedback' => $data['feedback'] ?? null,
            'graded_at' => now(),
        ]);

        if ($submission->student) {
            $notifications->sendOnce($submission->student, new LearningNotification(
                type: 'feedback',
                title: 'Ujian Telah Dinilai',
                message: 'Ujian ' . $submission->exam->title . ' telah dinilai oleh Guru.',
                url: route('siswa.ujian.result', $submission),
                eventKey: 'exam.graded:' . $submission->id . ':' . $submission->updated_at->timestamp
            ));
        }

        return redirect()->route('guru.ujian.index')->with('status', 'Nilai ujian berhasil disimpan.');
    }

    public function destroy(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);
        $this->ensureTeacherCanManage($request->user()->id, $exam);

        $exam->delete();

        return back()->with('status', 'Ujian berhasil dihapus.');
    }

    public function toggleLock(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);
        $this->ensureTeacherCanManage($request->user()->id, $exam);

        $exam->update([
            'locked' => !$exam->locked,
        ]);

        return back()->with(
            'status',
            $exam->locked ? 'Ujian berhasil dikunci.' : 'Ujian berhasil dibuka.'
        );
    }

    private function ensureTeacherOwnsClass(int $guruId, int $kelasId): void
    {
        abort_unless(
            Kelas::where('id', $kelasId)->where('wali_kelas_id', $guruId)->exists(),
            403,
            'Anda tidak mengajar kelas tersebut.'
        );
    }

    private function ensureTeacherCanManage(int $guruId, Exam $exam): void
    {
        $canManage = $exam->created_by === $guruId
            || Kelas::where('id', $exam->kelas_id)->where('wali_kelas_id', $guruId)->exists();

        abort_unless($canManage, 403, 'Anda tidak memiliki akses mengelola ujian ini.');
    }
}