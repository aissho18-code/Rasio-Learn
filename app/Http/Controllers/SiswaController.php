<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Materi;
use App\Models\MateriProgress;
use App\Models\Submission;
use App\Models\Presensi;
use App\Models\Ujian;
use App\Models\Tugas;
use App\Models\Refleksi;
use App\Models\RefleksiSubmission; 
use App\Support\MarkdownRenderer; // Impor class MarkdownRenderer
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SiswaController extends Controller
{
    // Menampilkan detail materi untuk siswa (DIPERBAIKI + Markdown Render)
    public function showMateri($id, MarkdownRenderer $markdownRenderer)
    {
        $materi = Materi::with(['tugas', 'mapel', 'kelas'])->findOrFail($id);
        $siswaId = auth()->id();
        
        // Periksa progress penguncian materi
        $progress = MateriProgress::where('siswa_id', $siswaId)
            ->where('materi_id', $id)
            ->first();

        // 1. Cek status penguncian dari tabel Materi (jika guru mengunci langsung)
        $isLockedByGuru = (isset($materi->is_locked) && $materi->is_locked) || 
                           (isset($materi->status) && $materi->status === 'locked');

        // 2. Cek status penguncian dari tabel MateriProgress
        $isLockedByProgress = $progress && $progress->status === 'locked';

        // Jika materi dalam kondisi dikunci, kembalikan ke daftar materi (BUKAN KE DASHBOARD)
        if ($isLockedByGuru || $isLockedByProgress) {
            return redirect()->route('siswa.materi.index')->with('error', 'Maaf, materi ini sedang dikunci oleh guru.');
        }

        // Jika materi terbuka tetapi belum ada record progress, buatkan otomatis
        if (!$progress) {
            $progress = MateriProgress::create([
                'siswa_id'  => $siswaId,
                'materi_id' => $id,
                'status'    => 'unlocked',
            ]);
        }

        // Render Markdown pada kolom konten agar menjadi HTML terformat di halaman detail
        $materi->rendered_konten = $markdownRenderer->render($materi->konten);

        return view('siswa-materi-detail', compact('materi', 'progress'));
    }

    // Menampilkan daftar materi lengkap secara real-time untuk siswa (DIPERBAIKI + Markdown Render)
    public function materiIndex(MarkdownRenderer $markdownRenderer)
    {
        $siswaId = auth()->id();

        // Mengambil materi diurutkan berdasarkan mapel dan urutan terbaru
        $materis = Materi::with(['mapel', 'kelas'])->orderBy('mapel_id')->orderBy('urutan', 'asc')->get();
        
        // Render Markdown pada kolom konten untuk setiap item materi
        $materis->transform(function ($item) use ($markdownRenderer) {
            $item->rendered_konten = $markdownRenderer->render($item->konten);
            return $item;
        });

        // Ambil seluruh status progress materi milik siswa ini untuk integrasi Lock/Unlock
        $progresses = MateriProgress::where('siswa_id', $siswaId)
            ->get()
            ->keyBy('materi_id');

        $groupedMateris = $materis->groupBy(function($item) {
            return $item->mapel->nama_mapel ?? 'Mata Pelajaran';
        });

        return view('siswa-materi', compact('groupedMateris', 'progresses'));
    }

    // Menampilkan daftar seluruh tugas untuk siswa (dengan auto-redirect untuk guru)
    public function indexTugas()
    {
        $user = auth()->user();

        if ($user->role === 'guru' || (method_exists($user, 'hasRole') && $user->hasRole('guru'))) {
            return redirect()->route('guru.tugas.index');
        }

        $tugasList = Tugas::orderBy('created_at', 'desc')->get();
        
        $submissions = Submission::where('siswa_id', auth()->id())
            ->get()
            ->keyBy('tugas_id');

        return view('siswa-tugas', compact('tugasList', 'submissions'));
    }

    // Menampilkan Laman Detail Tugas & Submission Status Moodle-Style
    public function showTugas($id)
    {
        $tugas = Tugas::findOrFail($id);
        $submission = Submission::where('siswa_id', auth()->id())->where('tugas_id', $id)->first();

        $timeRemainingText = '-';
        $isEarly = false;

        if ($tugas->tenggat_waktu) {
            $due = Carbon::parse($tugas->tenggat_waktu);

            if ($submission) {
                $subTime = Carbon::parse($submission->updated_at);

                if ($subTime->lessThanOrEqualTo($due)) {
                    $diffDays = (int) $subTime->diffInDays($due);
                    $tempDate = $subTime->copy()->addDays($diffDays);
                    $diffHours = (int) $tempDate->diffInHours($due);
                    $tempDate->addHours($diffHours);
                    $diffMins = (int) $tempDate->diffInMinutes($due);

                    $parts = [];
                    if ($diffDays > 0) $parts[] = $diffDays . ' ' . ($diffDays == 1 ? 'day' : 'days');
                    if ($diffHours > 0) $parts[] = $diffHours . ' ' . ($diffHours == 1 ? 'hour' : 'hours');
                    if ($diffMins > 0 && $diffDays == 0) $parts[] = $diffMins . ' ' . ($diffMins == 1 ? 'min' : 'mins');
                    if (empty($parts)) $parts[] = '0 mins';

                    $timeRemainingText = "Assignment was submitted " . implode(' ', $parts) . " early";
                    $isEarly = true;
                } else {
                    $diffDays = (int) $due->diffInDays($subTime);
                    $tempDate = $due->copy()->addDays($diffDays);
                    $diffHours = (int) $tempDate->diffInHours($subTime);
                    $tempDate->addHours($diffHours);
                    $diffMins = (int) $tempDate->diffInMinutes($subTime);

                    $parts = [];
                    if ($diffDays > 0) $parts[] = $diffDays . ' ' . ($diffDays == 1 ? 'day' : 'days');
                    if ($diffHours > 0) $parts[] = $diffHours . ' ' . ($diffHours == 1 ? 'hour' : 'hours');
                    if ($diffMins > 0 && $diffDays == 0) $parts[] = $diffMins . ' ' . ($diffMins == 1 ? 'min' : 'mins');
                    if (empty($parts)) $parts[] = '0 mins';

                    $timeRemainingText = "Assignment was submitted " . implode(' ', $parts) . " late";
                    $isEarly = false;
                }
            } else {
                $now = now();
                if ($now->lessThanOrEqualTo($due)) {
                    $diffDays = (int) $now->diffInDays($due);
                    $tempDate = $now->copy()->addDays($diffDays);
                    $diffHours = (int) $tempDate->diffInHours($due);
                    $tempDate->addHours($diffHours);
                    $diffMins = (int) $tempDate->diffInMinutes($due);

                    $parts = [];
                    if ($diffDays > 0) $parts[] = $diffDays . ' ' . ($diffDays == 1 ? 'day' : 'days');
                    if ($diffHours > 0) $parts[] = $diffHours . ' ' . ($diffHours == 1 ? 'hour' : 'hours');
                    if ($diffMins > 0 && $diffDays == 0) $parts[] = $diffMins . ' ' . ($diffMins == 1 ? 'min' : 'mins');
                    if (empty($parts)) $parts[] = '0 mins';

                    $timeRemainingText = implode(' ', $parts) . " remaining";
                    $isEarly = true;
                } else {
                    $diffDays = (int) $due->diffInDays($now);
                    $tempDate = $due->copy()->addDays($diffDays);
                    $diffHours = (int) $tempDate->diffInHours($now);
                    $tempDate->addHours($diffHours);
                    $diffMins = (int) $tempDate->diffInMinutes($now);

                    $parts = [];
                    if ($diffDays > 0) $parts[] = $diffDays . ' ' . ($diffDays == 1 ? 'day' : 'days');
                    if ($diffHours > 0) $parts[] = $diffHours . ' ' . ($diffHours == 1 ? 'hour' : 'hours');
                    if ($diffMins > 0 && $diffDays == 0) $parts[] = $diffMins . ' ' . ($diffMins == 1 ? 'min' : 'mins');
                    if (empty($parts)) $parts[] = '0 mins';

                    $timeRemainingText = "Assignment is overdue by " . implode(' ', $parts);
                    $isEarly = false;
                }
            }
        }

        return view('siswa-tugas-detail', compact('tugas', 'submission', 'timeRemainingText', 'isEarly'));
    }

    // Proses Pengumpulan Tugas
    public function submitTugas(Request $request, $tugasId)
    {
        $tugas = Tugas::findOrFail($tugasId);

        if ($tugas->tenggat_waktu && now()->greaterThan($tugas->tenggat_waktu)) {
            return redirect()->back()->with('error', '⛔ Pengumpulan gagal! Waktu pengerjaan telah melewati tenggat yang ditentukan.');
        }

        $existingSub = Submission::where('siswa_id', auth()->id())->where('tugas_id', $tugasId)->first();
        $hasExistingFile = $existingSub && $existingSub->file_path && Storage::disk('public')->exists($existingSub->file_path);

        $rules = [
            'jawaban' => 'nullable|string',
            'file_submission' => $hasExistingFile 
                ? 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,zip,rar|max:20480'
                : 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,zip,rar|max:20480',
        ];

        $messages = [
            'file_submission.required' => 'Wajib mengunggah berkas tugas (PDF/Word/Excel/Gambar) sebelum mengumpulkan!',
            'file_submission.mimes' => 'Format berkas tidak didukung. Gunakan PDF, Word, Excel, Gambar, atau ZIP/RAR.',
            'file_submission.max' => 'Ukuran berkas melebihi batas maksimal 20MB.',
        ];

        $request->validate($rules, $messages);

        $oldFilePath = $existingSub ? $existingSub->file_path : null;
        $newFilePath = $oldFilePath;

        if ($request->hasFile('file_submission')) {
            $newFilePath = $request->file('file_submission')->store('submission_files', 'public');
        }

        $dataToSave = [
            'file_path' => $newFilePath,
        ];

        if (Schema::hasColumn('submissions', 'status')) {
            $dataToSave['status'] = $existingSub && $existingSub->status ? $existingSub->status : 'menunggu';
        }

        if (Schema::hasColumn('submissions', 'jawaban')) {
            $dataToSave['jawaban'] = $request->jawaban;
        }

        Submission::updateOrCreate(
            [
                'siswa_id' => auth()->id(),
                'tugas_id' => $tugasId,
            ],
            $dataToSave
        );

        if ($request->hasFile('file_submission') && $oldFilePath && $oldFilePath !== $newFilePath) {
            if (Storage::disk('public')->exists($oldFilePath)) {
                Storage::disk('public')->delete($oldFilePath);
            }
        }

        return redirect()->route('siswa.tugas.show', $tugasId)->with('success', 'Tugas berhasil dikumpulkan!');
    }

    // Menampilkan halaman presensi berdasarkan database real-time
    public function absensi()
    {
        $siswaId = auth()->id();
        $today = Carbon::today()->toDateString();

        $riwayatAbsen = Presensi::where('siswa_id', $siswaId)
            ->orderBy('created_at', 'desc')
            ->get();

        $sudahAbsenHariIni = Presensi::where('siswa_id', $siswaId)
            ->whereDate('created_at', $today)
            ->exists();

        $totalHadir = Presensi::where('siswa_id', $siswaId)->where('status', 'Hadir')->count();
        $totalIzin  = Presensi::where('siswa_id', $siswaId)->where('status', 'Izin')->count();
        $totalSakit = Presensi::where('siswa_id', $siswaId)->where('status', 'Sakit')->count();

        $totalPertemuan = $totalHadir + $totalIzin + $totalSakit;
        $persentase = $totalPertemuan > 0 ? round(($totalHadir / $totalPertemuan) * 100) : 0;

        return view('siswa-absensi', compact(
            'riwayatAbsen', 
            'persentase', 
            'totalHadir', 
            'totalIzin', 
            'totalSakit', 
            'sudahAbsenHariIni', 
            'today'
        ));
    }

    // Menyimpan presensi real-time
    public function storeAbsensi(Request $request)
    {
        $request->validate([
            'status' => 'required|in:Hadir,Terlambat,Izin,Sakit',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $siswaId = auth()->id();
        $today = Carbon::today()->toDateString();

        $cek = Presensi::where('siswa_id', $siswaId)
            ->whereDate('created_at', $today)
            ->first();
        
        if (!$cek) {
            Presensi::create([
                'siswa_id'   => $siswaId,
                'status'     => $request->status,
                'keterangan' => $request->keterangan ?: 'Presensi mandiri (Real-time)',
            ]);
            return redirect()->route('siswa.absensi')->with('success', 'Presensi berhasil dicatat dengan status: ' . $request->status);
        }

        return redirect()->route('siswa.absensi')->with('error', 'Anda sudah melakukan presensi hari ini.');
    }

    // Menampilkan daftar ujian / quiz untuk siswa
    public function ujianIndex()
    {
        $siswaId = auth()->id();
        $ujians = Ujian::orderBy('created_at', 'desc')->get();
        
        $submissions = \App\Models\UjianSubmission::where('siswa_id', $siswaId)
            ->get()
            ->keyBy('ujian_id');

        return view('siswa-ujian', compact('ujians', 'submissions'));
    }

    // Menyimpan jawaban ujian
    public function storeUjian(Request $request, $id)
    {
        $siswaId = auth()->id();

        \App\Models\UjianSubmission::updateOrCreate(
            [
                'ujian_id' => $id,
                'siswa_id' => $siswaId,
            ],
            [
                'jawaban' => $request->jawaban,
                'nilai'   => null,
                'status'  => 'menunggu',
            ]
        );

        return redirect()->route('siswa.ujian.index')->with('success', 'Ujian berhasil dikumpulkan! Menunggu penilaian dari guru.');
    }

    // Menampilkan halaman evaluasi siswa
    public function evaluasiIndex()
    {
        $siswaId = auth()->id();

        $submissions = Submission::with(['tugas.materi.mapel'])
            ->where('siswa_id', $siswaId)
            ->orderBy('created_at', 'desc')
            ->get();

        $ujianSubmissions = \App\Models\UjianSubmission::with(['ujian'])
            ->where('siswa_id', $siswaId)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalTugasDiunggah = $submissions->count() + $ujianSubmissions->count();
        
        $allNilai = collect();
        foreach($submissions as $s) { 
            $score = $s->nilai ?? $s->final_score;
            if($score !== null) $allNilai->push($score); 
        }
        foreach($ujianSubmissions as $us) { 
            if($us->nilai !== null) $allNilai->push($us->nilai); 
        }

        $tugasSudahDinilai = $allNilai->count();
        $rataRataNilai = $allNilai->count() > 0 ? round($allNilai->avg(), 1) : 0;

        return view('siswa-evaluasi', compact('submissions', 'ujianSubmissions', 'totalTugasDiunggah', 'tugasSudahDinilai', 'rataRataNilai'));
    }

    // Detail Hasil Penilaian Ujian
    public function evaluasiUjianDetail($id)
    {
        $sub = \App\Models\UjianSubmission::with(['ujian'])->where('siswa_id', auth()->id())->findOrFail($id);
        return view('siswa-evaluasi-ujian-detail', compact('sub'));
    }

    // FITUR REFLEKSI SISWA

    // 1. Tampilkan Daftar Refleksi yang Dibuat oleh Guru
    public function refleksiIndex()
    {
        $siswaId = auth()->id();

        // Hanya ambil refleksi yang ber-status 'published' oleh Guru
        $refleksis = Refleksi::where('status', 'published')
            ->orderBy('pertemuan', 'asc')
            ->get();

        // Ambil rekam jawaban siswa ini untuk mengecek status (Sudah/Belum Diisi)
        $submissions = RefleksiSubmission::where('siswa_id', $siswaId)
            ->get()
            ->keyBy('refleksi_id');

        return view('siswa-refleksi', compact('refleksis', 'submissions'));
    }

    // 2. Laman Detail / Form Pengisian Refleksi Siswa
    public function refleksiShow($id)
    {
        $refleksi = Refleksi::findOrFail($id);
        $submission = RefleksiSubmission::where('siswa_id', auth()->id())
            ->where('refleksi_id', $id)
            ->first();

        return view('siswa-refleksi-detail', compact('refleksi', 'submission'));
    }

    // 3. Simpan / Update Jawaban Refleksi Siswa
    public function storeRefleksi(Request $request, $id)
    {
        $request->validate([
            'q1' => 'nullable|string|max:2000',
            'q2' => 'nullable|string|max:2000',
            'q3' => 'nullable|string|max:2000',
            'q4' => 'nullable|string|max:2000',
            'catatan' => 'nullable|string|max:5000',
        ]);

        $answers = [
            'q1' => $request->q1,
            'q2' => $request->q2,
            'q3' => $request->q3,
            'q4' => $request->q4,
        ];

        RefleksiSubmission::updateOrCreate(
            [
                'refleksi_id' => $id,
                'siswa_id'    => auth()->id(),
            ],
            [
                'answers' => $answers,
                'catatan' => $request->catatan,
            ]
        );
        return redirect()->route('siswa.refleksi.index')->with('success', 'Refleksi pembelajaran berhasil dikirim!');
    }
}