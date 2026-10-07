<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\User;
use App\Models\Lkpd;
use App\Notifications\LearningNotification;
use App\Support\LearningNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LkpdController extends Controller
{
    public function index(Request $request)
    {
        $lkpds = Lkpd::query()
            ->with(['kelas', 'questions', 'submissions.siswa', 'guru'])
            ->withCount('submissions')
            ->latest()
            ->get();

        return view('admin-lkpd-index', compact('lkpds'));
    }

   public function create()
{
    $kelasList = Kelas::query()
        ->orderBy('nama_kelas')
        ->get();

    $guruList = User::query()
        ->where('role', 'guru')
        ->orderBy('name')
        ->get();

    return view('admin-lkpd-create', compact('kelasList', 'guruList'));
}

    public function store(Request $request, LearningNotificationService $notifications)
    {
        $data = $request->validate([
            'guru_id' => ['required', 'exists:users,id'],
            'kelas_id' => ['required', 'exists:kelas,id'],
            'judul' => ['required', 'string', 'max:255'],
            'deadline' => ['required', 'date'],
            'deskripsi' => ['nullable', 'string', 'max:5000'],
            'instruksi' => ['nullable', 'string', 'max:5000'],
            'modul' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx', 'max:10240'],
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.pertanyaan' => ['required', 'string'],
            'questions.*.rubrik_jawaban' => ['required', 'string'],
            'questions.*.pembahasan' => ['nullable', 'string'],
            'questions.*.gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $lkpd = DB::transaction(function () use ($request, $data) {
            $modulPath = $request->hasFile('modul')
                ? $request->file('modul')->store('lkpd/modul', 'public')
                : null;

            $lkpd = Lkpd::create([
                'guru_id' => $data['guru_id'],
                'kelas_id' => $data['kelas_id'],
                'judul' => $data['judul'],
                'deadline' => $data['deadline'],
                'deskripsi' => $data['deskripsi'] ?? null,
                'instruksi' => $data['instruksi'] ?? null,
                'modul_path' => $modulPath,
                'status' => 'published',
            ]);

            foreach ($data['questions'] as $index => $question) {
                $gambarPath = null;

                if ($request->hasFile("questions.$index.gambar")) {
                    $gambarPath = $request
                        ->file("questions.$index.gambar")
                        ->store('lkpd/questions', 'public');
                }

                $lkpd->questions()->create([
                    'urutan' => $index + 1,
                    'pertanyaan' => $question['pertanyaan'],
                    'rubrik_jawaban' => $question['rubrik_jawaban'],
                    'pembahasan' => $question['pembahasan'] ?? null,
                    'gambar_path' => $gambarPath,
                ]);
            }

            return $lkpd;
        });

        $notifications->notifyStudentsInClass(
            (int) $lkpd->kelas_id,
            new LearningNotification(
                type: 'activity',
                title: 'LKPD Baru',
                message: 'LKPD baru tersedia: ' . $lkpd->judul,
                url: route('siswa.lkpd.show', $lkpd),
                eventKey: 'lkpd.published:' . $lkpd->id
            )
        );

        return redirect()
            ->route('admin.lkpd.index')
            ->with('status', 'LKPD berhasil diterbitkan.');
    }

    public function edit(Lkpd $lkpd)
    {
        $kelasList = Kelas::query()
            ->orderBy('nama_kelas')
            ->get();

        $lkpd->load('questions');

        return view('admin-lkpd-edit', compact('lkpd', 'kelasList'));
    }

    public function update(Request $request, Lkpd $lkpd)
    {
        $data = $request->validate([
            'guru_id' => ['required', 'exists:users,id'],
            'kelas_id' => ['required', 'exists:kelas,id'],
            'judul' => ['required', 'string', 'max:255'],
            'deadline' => ['required', 'date'],
            'deskripsi' => ['nullable', 'string', 'max:5000'],
            'instruksi' => ['nullable', 'string', 'max:5000'],
            'modul' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx', 'max:10240'],
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.id' => ['nullable', 'integer'],
            'questions.*.pertanyaan' => ['required', 'string'],
            'questions.*.rubrik_jawaban' => ['required', 'string'],
            'questions.*.pembahasan' => ['nullable', 'string'],
            'questions.*.gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        DB::transaction(function () use ($request, $data, $lkpd) {
            if ($request->hasFile('modul')) {
                if ($lkpd->modul_path) {
                    Storage::disk('public')->delete($lkpd->modul_path);
                }

                $lkpd->modul_path = $request
                    ->file('modul')
                    ->store('lkpd/modul', 'public');
            }

            $lkpd->update([
                'guru_id' => $data['guru_id'],
                'kelas_id' => $data['kelas_id'],
                'judul' => $data['judul'],
                'deadline' => $data['deadline'],
                'deskripsi' => $data['deskripsi'] ?? null,
                'instruksi' => $data['instruksi'] ?? null,
            ]);

            $retainedQuestionIds = [];

            foreach ($data['questions'] as $index => $questionData) {
                $question = !empty($questionData['id'])
                    ? $lkpd->questions()->findOrFail($questionData['id'])
                    : $lkpd->questions()->make();

                if ($request->hasFile("questions.$index.gambar")) {
                    if ($question->gambar_path) {
                        Storage::disk('public')->delete($question->gambar_path);
                    }

                    $question->gambar_path = $request
                        ->file("questions.$index.gambar")
                        ->store('lkpd/questions', 'public');
                }

                $question->fill([
                    'urutan' => $index + 1,
                    'pertanyaan' => $questionData['pertanyaan'],
                    'rubrik_jawaban' => $questionData['rubrik_jawaban'],
                    'pembahasan' => $questionData['pembahasan'] ?? null,
                ]);

                $lkpd->questions()->save($question);
                $retainedQuestionIds[] = $question->id;
            }

            $removedQuestions = $lkpd->questions()
                ->whereNotIn('id', $retainedQuestionIds)
                ->get();

            foreach ($removedQuestions as $removedQuestion) {
                if ($removedQuestion->gambar_path) {
                    Storage::disk('public')->delete($removedQuestion->gambar_path);
                }

                $removedQuestion->delete();
            }
        });

        return redirect()
            ->route('admin.lkpd.index')
            ->with('status', 'LKPD berhasil diperbarui.');
    }

    public function destroy(Lkpd $lkpd)
    {
        if ($lkpd->modul_path) {
            Storage::disk('public')->delete($lkpd->modul_path);
        }

        foreach ($lkpd->questions as $question) {
            if ($question->gambar_path) {
                Storage::disk('public')->delete($question->gambar_path);
            }
        }

        $lkpd->delete();

        return back()->with('status', 'LKPD berhasil dihapus.');
    }
}
