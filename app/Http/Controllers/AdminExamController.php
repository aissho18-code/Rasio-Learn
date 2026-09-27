<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Kelas;
use App\Models\ProctorLog;
use Illuminate\Http\Request;

class AdminExamController extends Controller
{
    // 1. Menampilkan Halaman Daftar Ujian + Filter Kelas
    public function index(Request $request)
    {
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $selectedClassId = $request->integer('kelas_id');

        $exams = Exam::with(['kelas', 'creator'])
            ->when($selectedClassId > 0, function ($query) use ($selectedClassId) {
                $query->where('kelas_id', $selectedClassId);
            })
            ->latest()
            ->get();

        return view('admin-exams-index', compact('exams', 'kelasList', 'selectedClassId'));
    }

    // 2. Form Tambah Ujian
    public function create()
    {
        $kelasList = Kelas::orderBy('nama_kelas')->get();

        return view('admin-exams-form', [
            'exam' => new Exam(),
            'kelasList' => $kelasList,
        ]);
    }

    // 3. Simpan Ujian Baru
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'kelas_id' => ['nullable', 'exists:kelas,id'],
            'max_violations' => ['required', 'integer', 'min:1', 'max:20'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        Exam::create([
            ...$data,
            'created_by' => $request->user()->id,
            'locked' => false,
        ]);

        return redirect()->route('admin.exams.index')->with('status', 'Ujian berhasil dibuat.');
    }

    // 4. Form Edit Ujian
    public function edit($id)
    {
        $exam = Exam::findOrFail($id);
        $kelasList = Kelas::orderBy('nama_kelas')->get();

        return view('admin-exams-edit', compact('exam', 'kelasList'));
    }

    // 5. Update Ujian
    public function update(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'kelas_id' => ['nullable', 'exists:kelas,id'],
            'max_violations' => ['required', 'integer', 'min:1', 'max:20'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        $exam->update($data);

        return redirect()->route('admin.exams.index')->with('status', 'Ujian berhasil diperbarui.');
    }

    // 6. Kunci / Buka Ujian
    public function toggleLock($id)
    {
        $exam = Exam::findOrFail($id);
        $exam->update(['locked' => !$exam->locked]);

        return back()->with('status', $exam->locked ? 'Ujian berhasil dikunci.' : 'Ujian berhasil dibuka.');
    }

    // 7. Hapus Ujian
    public function destroy($id)
    {
        $exam = Exam::findOrFail($id);
        $exam->delete();

        return back()->with('status', 'Ujian berhasil dihapus.');
    }

    // 8. Ekspor Log
    public function exportLogs()
    {
        $logs = ProctorLog::with('exam')->latest()->get();

        $csv = "Waktu,Siswa,Ujian,Tipe,Detail,Severity\n";

        foreach ($logs as $log) {
            $row = [
                $log->created_at,
                $log->student_id,
                $log->exam?->title,
                $log->type,
                $log->detail,
                $log->severity,
            ];

            $csv .= collect($row)
                ->map(fn ($value) => '"' . str_replace('"', '""', (string) $value) . '"')
                ->implode(',') . "\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="proctoring-logs.csv"',
        ]);
    }
}