<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Kelas;
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

        return view('guru-ujian-index', compact('exams', 'kelasGuru'));
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

    public function store(Request $request)
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

        Exam::create([
            ...$data,
            'created_by' => $guru->id,
            'locked' => false,
        ]);

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