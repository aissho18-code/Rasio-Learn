<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
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
        $guru = $request->user();

        $lkpds = Lkpd::query()
            ->where('guru_id', $guru->id)
            ->with(['kelas', 'questions', 'submissions.siswa'])
            ->withCount('submissions')
            ->latest()
            ->get();

        return view('guru-lkpd-index', compact('lkpds'));
    }

    public function create(Request $request)
    {
        $kelasGuru = Kelas::query()->where('wali_kelas_id', $request->user()->id)->orderBy('nama_kelas')->get();

        return view('guru-lkpd-create', [
            'kelasGuru' => $kelasGuru,
            'kelasList' => $kelasGuru,
        ]);
    }

    public function store(Request $request, LearningNotificationService $notifications)
    {
        $guru = $request->user();

        $data = $request->validate([
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

        abort_unless(
            Kelas::whereKey($data['kelas_id'])->where('wali_kelas_id', $guru->id)->exists(),
            403,
            'LKPD hanya dapat diterbitkan untuk kelas yang Anda ampu.'
        );

        $lkpd = DB::transaction(function () use ($request, $data, $guru) {
            $modulPath = $request->hasFile('modul')
                ? $request->file('modul')->store('lkpd/modul', 'public')
                : null;

            $lkpd = Lkpd::create([
                'guru_id' => $guru->id,
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
                    $gambarPath = $request->file("questions.$index.gambar")->store('lkpd/questions', 'public');
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

        $notifications->notifyStudentsInClass((int) $lkpd->kelas_id, new LearningNotification(
            type: 'activity',
            title: 'LKPD Baru',
            message: 'LKPD baru tersedia: ' . $lkpd->judul,
            url: route('siswa.lkpd.show', $lkpd),
            eventKey: 'lkpd.published:' . $lkpd->id
        ));

        return redirect()->route('guru.lkpd.index')->with('status', 'LKPD berhasil diterbitkan.');
    }

    public function edit(Request $request, Lkpd $lkpd)
    {
        abort_unless((int) $lkpd->guru_id === (int) $request->user()->id, 403);

        $kelasGuru = Kelas::query()->where('wali_kelas_id', $request->user()->id)->orderBy('nama_kelas')->get();
        $lkpd->load('questions');

        return view('guru-lkpd-edit', [
            'lkpd' => $lkpd,
            'kelasGuru' => $kelasGuru,
            'kelasList' => $kelasGuru,
        ]);
    }

    public function update(Request $request, Lkpd $lkpd)
    {
        $guru = $request->user();
        abort_unless((int) $lkpd->guru_id === (int) $guru->id, 403);

        $data = $request->validate([
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

        abort_unless(
            Kelas::whereKey($data['kelas_id'])->where('wali_kelas_id', $guru->id)->exists(),
            403,
            'LKPD hanya dapat ditujukan ke kelas yang Anda ampu.'
        );

        DB::transaction(function () use ($request, $data, $lkpd) {
            if ($request->hasFile('modul')) {
                if ($lkpd->modul_path) {
                    Storage::disk('public')->delete($lkpd->modul_path);
                }
                $lkpd->modul_path = $request->file('modul')->store('lkpd/modul', 'public');
            }

            $lkpd->update([
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
                    $question->gambar_path = $request->file("questions.$index.gambar")->store('lkpd/questions', 'public');
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

            $removedQuestions = $lkpd->questions()->whereNotIn('id', $retainedQuestionIds)->get();
            foreach ($removedQuestions as $removedQuestion) {
                if ($removedQuestion->gambar_path) {
                    Storage::disk('public')->delete($removedQuestion->gambar_path);
                }
                $removedQuestion->delete();
            }
        });

        return redirect()->route('guru.lkpd.index')->with('status', 'LKPD berhasil diperbarui.');
    }

    public function destroy(Request $request, Lkpd $lkpd)
    {
        abort_unless((int) $lkpd->guru_id === (int) $request->user()->id, 403);

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