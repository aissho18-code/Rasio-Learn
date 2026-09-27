<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Lkpd;
use App\Models\LkpdSubmission;
use App\Models\User;
use App\Notifications\LearningNotification;
use App\Support\LearningNotificationService;
use Illuminate\Http\Request;

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

        return view('siswa-lkpd-show', compact('lkpd', 'submission'));
    }

    public function submit(Request $request, Lkpd $lkpd, LearningNotificationService $notifications)
    {
        $this->ensureStudentCanAccess($request, $lkpd);

        $data = $request->validate([
            'jawaban' => ['required', 'array', 'min:1'],
            'jawaban.*' => ['required', 'string'],
        ]);

        $questionIds = $lkpd->questions()->pluck('id')->map(fn ($id) => (string) $id)->all();
        abort_if(array_diff(array_keys($data['jawaban']), $questionIds), 422);

        $submission = LkpdSubmission::updateOrCreate(
            ['lkpd_id' => $lkpd->id, 'siswa_id' => $request->user()->id],
            ['jawaban' => $data['jawaban'], 'status' => 'submitted', 'submitted_at' => now()]
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

        return redirect()->route('siswa.lkpd.show', $lkpd)->with('status', 'Jawaban LKPD berhasil dikirim.');
    }

    private function ensureStudentCanAccess(Request $request, Lkpd $lkpd): void
    {
        $kelasId = optional($request->user()->siswaProfile)->kelas_id;

        abort_unless($lkpd->status === 'published' && (int) $lkpd->kelas_id === (int) $kelasId, 403);
    }
}