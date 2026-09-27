<?php

namespace App\Http\Controllers;

use App\Models\Exam;
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
            ->where('locked', false)
            ->latest()
            ->get();

        return view('siswa-ujian-index', compact('exams'));
    }

    public function show($id)
    {
        $exam = Exam::with(['kelas'])->findOrFail($id);

        if ($exam->locked) {
            return redirect()->route('siswa.ujian.index')->with('error', 'Ujian ini sedang dikunci oleh pengawas/admin.');
        }

        return view('siswa-ujian-show', compact('exam'));
    }
}