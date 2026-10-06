<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\ExamSubmission;
use App\Models\Kelas;
use App\Models\User;
use App\Notifications\LearningNotification;
use App\Support\LearningNotificationService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class GuruExamController extends Controller
{
    public function index(Request $request)
    {
        $guru = $request->user();

        $kelasGuru = Kelas::where('wali_kelas_id', $guru->id)
            ->orderBy('nama_kelas')
            ->get();

        $kelasIds = $kelasGuru->pluck('id');

        $exams = Exam::with(['kelas', 'questions'])
            ->where(fn ($query) => $query
                ->where('created_by', $guru->id)
                ->orWhereIn('kelas_id', $kelasIds))
            ->latest()
            ->get();

        $submissions = ExamSubmission::with(['student', 'exam'])
            ->whereHas('exam', fn ($query) => $query->where(fn ($examQuery) => $examQuery
                ->where('created_by', $guru->id)
                ->orWhereIn('kelas_id', $kelasIds)))
            ->whereNotNull('submitted_at')
            ->with(['exam.questions', 'student'])
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

        $data = $this->validatedExamData($request);

        $this->ensureTeacherOwnsClass($guru->id, (int) $data['kelas_id']);
        if ($data['status'] === 'published') {
            throw ValidationException::withMessages(['status' => 'Simpan sebagai draft, tambahkan soal, lalu publikasikan ujian.']);
        }

        $exam = Exam::create([
            ...$data,
            'created_by' => $guru->id,
            'locked' => false,
        ]);

        $this->notifyPublishedExam($exam, $notifications);

        return redirect()->route('guru.ujian.questions.index', $exam->id)
            ->with('status', 'Informasi ujian tersimpan. Lengkapi soal sebelum dipublikasikan.');
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

        $wasPublished = $exam->status === 'published';
        $data = $this->validatedExamData($request);

        $this->ensureTeacherOwnsClass($guru->id, (int) $data['kelas_id']);
        if ($data['status'] === 'published' && $exam->questions()->count() !== (int) $data['question_count']) {
            throw ValidationException::withMessages(['status' => 'Jumlah soal harus sesuai dengan jumlah yang diatur sebelum ujian dipublikasikan.']);
        }

        $exam->update($data);
        if (!$wasPublished && $exam->status === 'published') {
            $this->notifyPublishedExam($exam, app(LearningNotificationService::class));
        }

        return redirect()->route('guru.ujian.questions.index', $exam->id)
            ->with('status', 'Informasi ujian berhasil diperbarui.');
    }

    public function questions(Request $request, $id)
    {
        $exam = Exam::with('questions')->findOrFail($id);
        $this->ensureTeacherCanManage($request->user()->id, $exam);
        $isAdmin = $request->user()->role === 'admin';

        return view('guru-ujian-questions', compact('exam', 'isAdmin'));
    }

    public function createQuestion(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);
        $this->ensureTeacherCanManage($request->user()->id, $exam);

        return view('guru-ujian-question-form', [
            'exam' => $exam,
            'question' => new ExamQuestion(),
            'isAdmin' => $request->user()->role === 'admin',
        ]);
    }

    public function storeQuestion(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);
        $this->ensureTeacherCanManage($request->user()->id, $exam);
        $questionData = $this->validatedQuestionData($request);
        $this->ensureQuestionFitsModel($exam, $questionData['type']);
        $exam->questions()->create($questionData);

        $route = $request->user()->role === 'admin' ? 'admin.exams.edit' : 'guru.ujian.questions.index';

        return redirect()->route($route, $exam->id)->with('status', 'Soal berhasil ditambahkan.');
    }

    public function editQuestion(Request $request, $id, $questionId)
    {
        $exam = Exam::findOrFail($id);
        $this->ensureTeacherCanManage($request->user()->id, $exam);
        $question = $exam->questions()->findOrFail($questionId);

        $isAdmin = $request->user()->role === 'admin';

        return view('guru-ujian-question-form', compact('exam', 'question', 'isAdmin'));
    }

    public function updateQuestion(Request $request, $id, $questionId)
    {
        $exam = Exam::findOrFail($id);
        $this->ensureTeacherCanManage($request->user()->id, $exam);
        $question = $exam->questions()->findOrFail($questionId);
        $questionData = $this->validatedQuestionData($request);
        $this->ensureQuestionFitsModel($exam, $questionData['type']);
        $question->update($questionData);

        $route = $request->user()->role === 'admin' ? 'admin.exams.edit' : 'guru.ujian.questions.index';

        return redirect()->route($route, $exam->id)->with('status', 'Soal berhasil diperbarui.');
    }

    public function destroyQuestion(Request $request, $id, $questionId)
    {
        $exam = Exam::findOrFail($id);
        $this->ensureTeacherCanManage($request->user()->id, $exam);
        $exam->questions()->findOrFail($questionId)->delete();

        $route = $request->user()->role === 'admin' ? 'admin.exams.edit' : 'guru.ujian.questions.index';

        return redirect()->route($route, $exam->id)->with('status', 'Soal berhasil dihapus.');
    }

    public function grade(Request $request, ExamSubmission $submission, LearningNotificationService $notifications)
    {
        $guru = $request->user();
        $submission->load('exam', 'student');
        $this->ensureTeacherCanManage($guru->id, $submission->exam);

        $essayQuestions = $submission->exam->questions->where('type', 'essay');
        abort_if($submission->exam->questions->isNotEmpty() && $essayQuestions->isEmpty(), 422, 'Nilai soal objektif dihitung otomatis oleh sistem.');
        $data = $request->validate([
            'score' => [$essayQuestions->isEmpty() ? 'required' : 'nullable', 'integer', 'between:0,100'],
            'essay_scores' => ['sometimes', 'array'],
            'essay_scores.*' => ['numeric', 'min:0', 'max:1000'],
            'feedback' => ['nullable', 'string', 'max:5000'],
        ]);

        if ($submission->exam->questions->isEmpty()) {
            $submission->update([
                'score' => $data['score'],
                'feedback' => $data['feedback'] ?? null,
                'graded_at' => now(),
            ]);
        } else {
            $essayScores = $submission->essay_scores ?? [];
            foreach ($data['essay_scores'] ?? [] as $questionId => $score) {
                $question = $essayQuestions->firstWhere('id', (int) $questionId);
                abort_unless($question, 422, 'Nilai hanya dapat diberikan pada soal esai ujian ini.');
                abort_if((float) $score > (float) $question->points, 422, 'Nilai melebihi bobot soal.');
                $essayScores[$questionId] = (float) $score;
            }

            $autoPoints = (float) ($submission->earned_points ?? 0);
            $essayPoints = collect($essayScores)->sum();
            $totalPoints = (float) $submission->exam->questions->sum('points');
            $isFullyGraded = $essayQuestions->every(fn ($question) => array_key_exists($question->id, $essayScores));
            $submission->update([
                'essay_scores' => $essayScores,
                'score' => $totalPoints > 0 ? (int) round((($autoPoints + $essayPoints) / $totalPoints) * 100) : 0,
                'feedback' => $data['feedback'] ?? $submission->feedback,
                'graded_at' => $isFullyGraded ? now() : null,
            ]);
        }

        if ($submission->student) {
            $notifications->sendOnce($submission->student, new LearningNotification(
                type: 'feedback',
                title: 'Ujian Telah Dinilai',
                message: 'Ujian ' . $submission->exam->title . ' telah dinilai oleh Guru.',
                url: route('siswa.ujian.result', $submission),
                eventKey: 'exam.graded:' . $submission->id . ':' . $submission->updated_at->timestamp
            ));
        }

        $route = $guru->role === 'admin' ? 'admin.exams.index' : 'guru.ujian.index';

        return redirect()->route($route)->with('status', 'Nilai ujian berhasil disimpan.');
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
        $canManage = User::whereKey($guruId)->where('role', 'admin')->exists()
            || $exam->created_by === $guruId
            || Kelas::where('id', $exam->kelas_id)->where('wali_kelas_id', $guruId)->exists();

        abort_unless($canManage, 403, 'Anda tidak memiliki akses mengelola ujian ini.');
    }

    private function validatedExamData(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'kelas_id' => ['required', 'exists:kelas,id'],
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

    private function validatedQuestionData(Request $request): array
    {
        $type = $request->input('type');
        $rules = [
            'prompt' => ['required', 'string', 'max:20000'],
            'type' => ['required', 'in:multiple_choice,multiple_response,true_false,short_answer,essay'],
            'points' => ['required', 'numeric', 'gt:0', 'max:1000'],
            'explanation' => ['nullable', 'string', 'max:5000'],
            'position' => ['nullable', 'integer', 'min:0', 'max:500'],
        ];

        if (in_array($type, ['multiple_choice', 'multiple_response'], true)) {
            $rules['options'] = ['required', 'array', 'min:2', 'max:8'];
            $rules['options.*'] = ['required', 'string', 'max:1000'];
            $rules['correct_option'] = ['required_if:type,multiple_choice', 'integer', 'min:0'];
            $rules['correct_options'] = ['required_if:type,multiple_response', 'array', 'min:1'];
            $rules['correct_options.*'] = ['integer', 'min:0'];
        } elseif ($type === 'true_false') {
            $rules['correct_option'] = ['required', 'in:Benar,Salah'];
        } elseif ($type === 'short_answer') {
            $rules['answer_key'] = ['required', 'string', 'max:1000'];
        }

        $data = $request->validate($rules);
        $options = array_values(array_map('trim', $data['options'] ?? []));
        $correctAnswer = null;

        if ($type === 'multiple_choice') {
            $index = (int) $data['correct_option'];
            abort_unless(array_key_exists($index, $options), 422, 'Pilihan jawaban benar tidak valid.');
            $correctAnswer = [$options[$index]];
        } elseif ($type === 'multiple_response') {
            $indexes = array_unique(array_map('intval', $data['correct_options']));
            abort_if(collect($indexes)->contains(fn ($index) => !array_key_exists($index, $options)), 422, 'Pilihan jawaban benar tidak valid.');
            $correctAnswer = array_values(array_map(fn ($index) => $options[$index], $indexes));
        } elseif ($type === 'true_false') {
            $options = ['Benar', 'Salah'];
            $correctAnswer = [$data['correct_option']];
        } elseif ($type === 'short_answer') {
            $correctAnswer = [trim($data['answer_key'])];
        }

        return [
            'prompt' => $data['prompt'],
            'type' => $type,
            'options' => $options ?: null,
            'correct_answer' => $correctAnswer,
            'explanation' => $data['explanation'] ?? null,
            'points' => $data['points'],
            'position' => $data['position'] ?? 0,
        ];
    }

    private function notifyPublishedExam(Exam $exam, LearningNotificationService $notifications): void
    {
        if ($exam->status !== 'published' || !$exam->kelas_id) {
            return;
        }

        $notifications->notifyStudentsInClass((int) $exam->kelas_id, new LearningNotification(
            type: 'exam',
            title: 'Ujian Baru Tersedia',
            message: 'Ujian ' . $exam->title . ' telah diterbitkan untuk kelas Anda.',
            url: route('siswa.ujian.index'),
            eventKey: 'exam.published:' . $exam->id . ':' . $exam->updated_at?->timestamp
        ));
    }

    private function ensureQuestionFitsModel(Exam $exam, string $type): void
    {
        abort_if($exam->exam_model === 'essay' && $type !== 'essay', 422, 'Model Esai hanya menerima soal esai.');
    }
}