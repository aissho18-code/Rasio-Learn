<?php

namespace App\Http\Controllers;

use App\Models\ExamQuestion;
use App\Models\Exam;
use App\Models\ExamSubmission;
use App\Models\Kelas;
use App\Models\User;
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
            ->where('status', 'published')
            ->withCount(['submissions as student_attempts' => fn ($query) => $query->where('student_id', $user->id)])
            ->with(['submissions' => fn ($query) => $query
                ->where('student_id', $user->id)
                ->orderByDesc('attempt_number')])
            ->latest()
            ->get();

        return view('siswa-ujian-index', compact('exams'));
    }

    public function show(Request $request, $id)
    {
        $student = $request->user();
        $exam = $this->studentExamQuery($student, $id)->with(['kelas', 'creator', 'questions'])->firstOrFail();

        if ($exam->locked) {
            return redirect()->route('siswa.ujian.index')->with('error', 'Ujian ini sedang dikunci oleh pengawas/admin.');
        }
        abort_if($exam->starts_at && now()->lt($exam->starts_at), 403, 'Ujian belum dimulai.');

        $submission = $exam->submissions()
            ->where('student_id', $student->id)
            ->whereNull('completed_at')
            ->whereNull('submitted_at')
            ->latest('attempt_number')
            ->first();

        if (!$submission) {
            $attemptCount = $exam->submissions()->where('student_id', $student->id)->count();
            if ($attemptCount >= $exam->max_attempts) {
                $lastSubmission = $exam->submissions()->where('student_id', $student->id)->latest('attempt_number')->first();
                if ($lastSubmission) {
                    $exam->setRelation('submissions', collect([$lastSubmission]));
                    $submission = $lastSubmission;
                    $isResultOnly = true;
                    $canSeeScore = $exam->show_score && $submission->graded_at !== null;
                    $canSeeExplanations = $canSeeScore && $exam->show_explanations;

                    return view('siswa-ujian-show', compact('exam', 'submission', 'isResultOnly', 'canSeeScore', 'canSeeExplanations'));
                }
                return redirect()->route('siswa.ujian.index')->with('error', 'Batas percobaan ujian telah tercapai.');
            }

            $submission = $exam->submissions()->create([
                'student_id' => $student->id,
                'attempt_number' => $attemptCount + 1,
                'response' => '',
                'started_at' => now(),
            ]);
        }

        if ($exam->ends_at && now()->gt($exam->ends_at)) {
            $this->completeAttempt($exam, $submission, true);
            return redirect()->route('siswa.ujian.result', $submission);
        }

        if ($this->remainingSeconds($exam, $submission) <= 0) {
            $this->completeAttempt($exam, $submission, true);
            return redirect()->route('siswa.ujian.result', $submission);
        }

        $questions = $this->displayQuestions($exam, $submission);
        $exam->setRelation('submissions', collect([$submission]));
        $remainingSeconds = $this->remainingSeconds($exam, $submission);

        return view('siswa-ujian-show', compact('exam', 'submission', 'questions', 'remainingSeconds'));
    }

    public function result(Request $request, ExamSubmission $submission)
    {
        abort_unless((int) $submission->student_id === (int) $request->user()->id, 404);

        $submission->load('exam.kelas', 'exam.questions');
        $exam = $submission->exam;
        abort_unless($submission->completed_at !== null || $submission->submitted_at !== null, 404);
        $exam->setRelation('submissions', collect([$submission]));
        $isResultOnly = true;
        $canSeeScore = $exam->show_score && $submission->graded_at !== null;
        $canSeeExplanations = $canSeeScore && $exam->show_explanations;

        return view('siswa-ujian-show', compact('exam', 'submission', 'isResultOnly', 'canSeeScore', 'canSeeExplanations'));
    }

    public function saveAnswers(Request $request, $id)
    {
        $student = $request->user();
        $exam = $this->studentExamQuery($student, $id)->with('questions')->firstOrFail();
        $submission = $this->activeSubmission($exam, $student);
        abort_if($exam->locked || $exam->status !== 'published', 403, 'Ujian tidak tersedia.');

        if ($this->remainingSeconds($exam, $submission) <= 0) {
            $this->completeAttempt($exam, $submission, true);
            return response()->json(['expired' => true], 409);
        }

        $this->saveSubmittedAnswers($request, $exam, $submission);

        return response()->json(['saved' => true, 'saved_at' => now()->format('H:i:s')]);
    }

    public function submit(Request $request, $id, LearningNotificationService $notifications)
    {
        $student = $request->user();
        $exam = $this->studentExamQuery($student, $id)->with('questions')->firstOrFail();
        $submission = $exam->submissions()
            ->where('student_id', $student->id)
            ->whereNull('completed_at')
            ->whereNull('submitted_at')
            ->latest('attempt_number')
            ->first();

        abort_if($exam->locked || $exam->status !== 'published', 403, 'Ujian tidak tersedia.');
        if (!$submission) {
            $attemptCount = $exam->submissions()->where('student_id', $student->id)->count();
            abort_if($attemptCount >= $exam->max_attempts, 403, 'Batas percobaan ujian telah tercapai.');
            $submission = $exam->submissions()->create([
                'student_id' => $student->id,
                'attempt_number' => $attemptCount + 1,
                'response' => '',
                'started_at' => now(),
            ]);
        }

        if ($exam->questions->isEmpty()) {
            $data = $request->validate([
                'response' => [$request->boolean('auto_submit') ? 'nullable' : 'required', 'string', 'max:50000'],
            ]);
            $submission->response = $data['response'] ?? $submission->response ?? '';
        } else {
            $this->saveSubmittedAnswers($request, $exam, $submission);
        }

        $autoSubmitted = $this->remainingSeconds($exam, $submission) <= 0 || $request->boolean('auto_submit');
        $this->completeAttempt($exam, $submission, $autoSubmitted);
        $submission->refresh();

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

        $redirect = $exam->questions->isEmpty()
            ? redirect()->route('siswa.ujian.show', $exam->id)
            : redirect()->route('siswa.ujian.result', $submission);

        return $redirect->with('success', 'Jawaban ujian berhasil dikirim.');
    }

    private function studentExamQuery(User $student, int|string $examId)
    {
        $kelasId = optional($student->siswaProfile)->kelas_id;

        return Exam::whereKey($examId)
            ->where('status', 'published')
            ->where(function ($query) use ($kelasId) {
                $query->whereNull('kelas_id')->orWhere('kelas_id', $kelasId);
            });
    }

    private function activeSubmission(Exam $exam, User $student): ExamSubmission
    {
        $submission = $exam->submissions()
            ->where('student_id', $student->id)
            ->whereNull('completed_at')
            ->whereNull('submitted_at')
            ->latest('attempt_number')
            ->first();

        abort_unless($submission, 403, 'Tidak ada percobaan ujian aktif.');

        return $submission;
    }

    private function saveSubmittedAnswers(Request $request, Exam $exam, ExamSubmission $submission): void
    {
        $data = $request->validate([
            'answers' => ['sometimes', 'array'],
            'answers.*' => ['nullable'],
            'response' => ['nullable', 'string', 'max:50000'],
        ]);

        if ($exam->questions->isEmpty()) {
            $submission->response = $data['response'] ?? $submission->response;
            $submission->save();
            return;
        }

        $answers = $submission->answers ?? [];
        foreach ($data['answers'] ?? [] as $questionId => $answer) {
            $question = $exam->questions->firstWhere('id', (int) $questionId);
            abort_unless($question, 422, 'Soal yang dikirim tidak termasuk ujian ini.');
            $answer ??= '';

            if (in_array($question->type, ['multiple_choice', 'true_false'], true)) {
                abort_unless(is_string($answer) && in_array($answer, $question->options ?? [], true), 422, 'Pilihan jawaban tidak valid.');
            } elseif ($question->type === 'multiple_response') {
                abort_unless(is_array($answer), 422, 'Jawaban pilihan ganda kompleks tidak valid.');
                $answer = array_values(array_unique($answer));
                abort_if(collect($answer)->contains(fn ($item) => !in_array($item, $question->options ?? [], true)), 422, 'Pilihan jawaban tidak valid.');
            } else {
                abort_unless(is_string($answer) && mb_strlen($answer) <= 5000, 422, 'Jawaban teks tidak valid.');
            }

            $answers[$questionId] = $answer;
        }

        $submission->update(['answers' => $answers]);
    }

    private function completeAttempt(Exam $exam, ExamSubmission $submission, bool $autoSubmitted): void
    {
        if ($submission->completed_at || $submission->submitted_at) {
            return;
        }

        $answers = $submission->answers ?? [];
        $earnedPoints = 0;
        $correctCount = 0;
        $wrongCount = 0;
        $hasEssay = $exam->questions->isEmpty();

        foreach ($exam->questions as $question) {
            if ($question->type === 'essay') {
                $hasEssay = true;
                continue;
            }

            $answer = $answers[$question->id] ?? null;
            $correct = $this->isCorrectAnswer($question, $answer);
            if ($correct) {
                $earnedPoints += (float) $question->points;
                $correctCount++;
            } else {
                $wrongCount++;
            }
        }

        $totalPoints = (float) $exam->questions->sum('points');
        $score = !$hasEssay && $totalPoints > 0
            ? (int) round(($earnedPoints / $totalPoints) * 100)
            : null;
        $now = now();

        $submission->update([
            'submitted_at' => $now,
            'completed_at' => $now,
            'duration_seconds' => $submission->started_at ? $submission->started_at->diffInSeconds($now) : null,
            'auto_submitted' => $autoSubmitted,
            'earned_points' => $earnedPoints,
            'correct_count' => $correctCount,
            'wrong_count' => $wrongCount,
            'score' => $score,
            'graded_at' => $hasEssay ? null : $now,
        ]);
    }

    private function isCorrectAnswer(ExamQuestion $question, mixed $answer): bool
    {
        if ($answer === null || $answer === '') {
            return false;
        }

        $expected = $question->correct_answer ?? [];
        if ($question->type === 'multiple_response') {
            $actual = array_map('strval', (array) $answer);
            $expected = array_map('strval', $expected);
            sort($actual);
            sort($expected);
            return $actual === $expected;
        }

        if ($question->type === 'short_answer') {
            return mb_strtolower(trim((string) $answer)) === mb_strtolower(trim((string) ($expected[0] ?? '')));
        }

        return (string) $answer === (string) ($expected[0] ?? '');
    }

    private function remainingSeconds(Exam $exam, ExamSubmission $submission): int
    {
        if (!$submission->started_at) {
            return 0;
        }

        $deadline = $submission->started_at->copy()->addMinutes((int) $exam->duration_minutes);
        if ($exam->ends_at && $exam->ends_at->lessThan($deadline)) {
            $deadline = $exam->ends_at;
        }

        return max(0, now()->diffInSeconds($deadline, false));
    }

    private function displayQuestions(Exam $exam, ExamSubmission $submission)
    {
        $questions = $exam->questions;
        if ($exam->shuffle_questions) {
            $questions = $questions->sortBy(fn ($question) => sha1($submission->id . ':' . $question->id))->values();
        }

        return $questions->map(function ($question) use ($exam, $submission) {
            $options = $question->options ?? [];
            if ($exam->shuffle_options) {
                usort($options, fn ($left, $right) => strcmp(
                    sha1($submission->id . ':' . $question->id . ':' . $left),
                    sha1($submission->id . ':' . $question->id . ':' . $right)
                ));
            }
            $question->setAttribute('display_options', $options);
            return $question;
        });
    }
}