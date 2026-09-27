<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aktivitas;
use App\Models\AktivitasSubmission;
use App\Models\User;
use App\Notifications\LearningNotification;
use App\Support\LearningNotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SiswaAktivitasController extends Controller
{
    public function index()
    {
        $siswa = Auth::user();
        $kelasId = optional($siswa->siswaProfile)->kelas_id;

        $aktivitas = Aktivitas::where(function ($q) use ($kelasId) {
            $q->whereNull('kelas_id');
            if ($kelasId) {
                $q->orWhere('kelas_id', $kelasId);
            }
        })
        ->where('status', 'published')
        ->with('guru')
        ->latest('published_at')
        ->latest()
        ->get();

        return view('siswa-aktivitas-index', compact('aktivitas'));
    }

    // Endpoint API untuk Polling Real-Time data aktivitas siswa
    public function apiIndex()
    {
        $siswa = Auth::user();
        $kelasId = optional($siswa->siswaProfile)->kelas_id ?? $siswa->kelas_id;

        $aktivitas = Aktivitas::where(function ($q) use ($kelasId) {
            $q->whereNull('kelas_id');
            if ($kelasId) {
                $q->orWhere('kelas_id', $kelasId);
            }
        })
        ->where('status', 'published')
        ->with('guru:id,name')
        ->latest('published_at')
        ->latest()
        ->get()
        ->map(function ($item) {
            return [
                'id' => $item->id,
                'judul' => $item->judul,
                'tujuan' => $item->tujuan,
                'guru_name' => optional($item->guru)->name ?? 'Guru',
                'respons_type' => $item->respons_type,
                'has_lkpd' => !empty($item->lkpd_path),
                'download_url' => route('siswa.aktivitas.lkpd.download', $item->id),
                'show_url' => route('siswa.aktivitas.show', $item->id),
                'created_at_formatted' => $item->created_at->diffForHumans(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $aktivitas
        ]);
    }

    public function show(Aktivitas $aktivitas)
    {
        $kelasId = optional(Auth::user()->siswaProfile)->kelas_id;
        if ($aktivitas->kelas_id && $aktivitas->kelas_id != $kelasId) {
            abort(403, 'Aktivitas ini tidak ditujukan untuk kelas Anda.');
        }

        // Muat relasi blocks untuk komponen interaktif
        $aktivitas->load('blocks');

        $submission = AktivitasSubmission::where('aktivitas_id', $aktivitas->id)
            ->where('siswa_id', Auth::id())
            ->first();

        return view('siswa-aktivitas-show', compact('aktivitas', 'submission'));
    }

    public function submit(Request $r, Aktivitas $aktivitas, LearningNotificationService $notifications)
    {
        $kelasId = optional(Auth::user()->siswaProfile)->kelas_id;
        abort_unless(
            $aktivitas->status === 'published'
                && (is_null($aktivitas->kelas_id) || (int) $aktivitas->kelas_id === (int) $kelasId),
            403
        );

        $r->validate([
            'text_answer' => 'nullable|string',
            'jawaban' => 'nullable|array', // Menerima array jawaban dari blok interaktif
            'file' => 'nullable|file|mimes:pdf,doc,docx,png,jpg,jpeg|max:10240',
        ]);

        $studentId = Auth::id();
        $submission = AktivitasSubmission::firstOrNew([
            'aktivitas_id' => $aktivitas->id,
            'siswa_id' => $studentId,
        ]);

        // Simpan jawaban teks tunggal atau array blok interaktif
        $submission->text_answer = $r->input('text_answer');
        if ($r->has('jawaban')) {
            $submission->jawaban = $r->input('jawaban');
        }

        if ($r->hasFile('file')) {
            if ($submission->file_path && Storage::disk('public')->exists($submission->file_path)) {
                Storage::disk('public')->delete($submission->file_path);
            }
            $path = $r->file('file')->store('aktivitas_submissions', 'public');
            $submission->file_path = $path;
        }

        $submission->status = 'submitted';
        $submission->submitted_at = now();
        $submission->save();

        // --- KIRIM NOTIFIKASI KE GURU PEMBUAT AKTIVITAS ---
        if (!empty($aktivitas->guru_id)) {
            $guru = User::find($aktivitas->guru_id);
            if ($guru) {
                $notifications->sendOnce($guru, new LearningNotification(
                    type: 'assignment',
                    title: 'Pengumpulan LKPD Baru',
                    message: Auth::user()->name . ' telah mengumpulkan jawaban pada ' . $aktivitas->judul,
                    url: route('guru.aktivitas.submissions', $aktivitas->id),
                    eventKey: 'activity.submitted:' . $submission->id . ':' . $submission->updated_at->timestamp
                ));
            }
        }

        return back()->with('status', 'Jawaban berhasil dikirim.');
    }

    public function downloadLkpd(Aktivitas $aktivitas)
    {
        if (!$aktivitas->lkpd_path || !Storage::disk('public')->exists($aktivitas->lkpd_path)) {
            abort(404, 'File LKPD tidak ditemukan.');
        }
        return Storage::disk('public')->download($aktivitas->lkpd_path);
    }
}