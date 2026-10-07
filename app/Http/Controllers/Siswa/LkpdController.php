<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Exceptions\LkpdAssessmentException;
use App\Models\Lkpd;
use App\Models\LkpdQuestion;
use App\Models\LkpdSubmission;
use App\Models\User;
use App\Notifications\LearningNotification;
use App\Services\LkpdAiAssessmentService;
use App\Support\LearningNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LkpdController extends Controller
{
    public function index(Request $request)
    {
        $student = $request->user();
        $kelasId = optional($student->siswaProfile)->kelas_id;

        $lkpds = Lkpd::query()
            ->where('kelas_id', $kelasId)
            ->where('status', 'published')
            ->with(['guru', 'questions'])
            ->latest()
            ->get();

        return view('siswa-lkpd-index', compact('lkpds'));
    }

    public function show(Request $request, Lkpd $lkpd)
    {
        $this->ensureStudentCanAccess($request, $lkpd);

        $lkpd->load('questions');
        $submission = LkpdSubmission::where('lkpd_id', $lkpd->id)
            ->where('siswa_id', $request->user()->id)
            ->first();

       $isReview = $request->boolean('review');

return view('siswa-lkpd-show', compact('lkpd', 'submission', 'isReview'));
    }

    public function questionImage(Request $request, Lkpd $lkpd, LkpdQuestion $question)
    {
        $this->ensureStudentCanAccess($request, $lkpd);
        abort_unless((int) $question->lkpd_id === (int) $lkpd->id, 404);
        abort_unless(
            $question->gambar_path && Storage::disk('public')->exists($question->gambar_path),
            404,
            'Gambar soal tidak ditemukan.'
        );

        return Storage::disk('public')->response($question->gambar_path);
    }

    public function submit(
        Request $request,
        Lkpd $lkpd,
        LkpdAiAssessmentService $assessmentService,
        LearningNotificationService $notifications
    ) {
        $this->ensureStudentCanAccess($request, $lkpd);

        $data = $request->validate([
            'jawaban' => ['required', 'array', 'min:1'],
            'jawaban.*' => ['required', 'string'],
        ]);

        $questions = $lkpd->questions()->get();
        $questionIds = $questions->pluck('id')->map(fn ($id) => (string) $id)->all();
        abort_if($questionIds === [], 422, 'LKPD ini belum memiliki soal untuk dinilai.');
        $answerIds = array_map('strval', array_keys($data['jawaban']));
        abort_if(array_diff($answerIds, $questionIds) || array_diff($questionIds, $answerIds), 422);

        try {
            $hasilPenilaian = $assessmentService->assess($questions->all(), $data['jawaban']);
        } catch (LkpdAssessmentException $exception) {
            return back()
                ->withInput()
                ->withErrors(['assessment' => $exception->getMessage()]);
        }

        $nilai = round(
            collect($hasilPenilaian)->where('is_correct', true)->count() / count($questionIds) * 100,
            2
        );

        $submission = LkpdSubmission::updateOrCreate(
            ['lkpd_id' => $lkpd->id, 'siswa_id' => $request->user()->id],
            [
                'jawaban' => $data['jawaban'],
                'hasil_penilaian' => $hasilPenilaian,
                'nilai' => $nilai,
                'status' => 'submitted',
                'submitted_at' => now(),
            ]
        );

        $teacher = User::find($lkpd->guru_id);
        if ($teacher) {
            $notifications->sendOnce($teacher, new LearningNotification(
                type: 'assignment',
                title: 'Pengumpulan LKPD Baru',
                message: $request->user()->name . ' mengumpulkan LKPD ' . $lkpd->judul . '.',
                url: route('guru.lkpd.index'),
                eventKey: 'lkpd.submitted:' . $submission->id . ':' . $submission->updated_at->timestamp
            ));
        }

        return redirect()->route('siswa.aktivitas.index')->with('status', 'Jawaban LKPD berhasil dikirim.');
    }

    private function ensureStudentCanAccess(Request $request, Lkpd $lkpd): void
    {
        $kelasId = optional($request->user()->siswaProfile)->kelas_id;

        abort_unless($lkpd->status === 'published' && (int) $lkpd->kelas_id === (int) $kelasId, 403);
    }
}