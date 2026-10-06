<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aktivitas;
use App\Models\AktivitasSubmission;
use App\Models\Exam;
use App\Models\User;
use App\Models\Materi;
use App\Models\MateriProgress;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Submission;
use App\Models\UjianSubmission;
use App\Models\Ujian;
use App\Models\Presensi;
use App\Models\Tugas;
use App\Models\Refleksi;
use App\Models\RefleksiSubmission;
use App\Models\Pengumuman;
use App\Notifications\LearningNotification;
use App\Support\MarkdownRenderer; // Impor class MarkdownRenderer
use App\Support\LearningNotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;


class GuruController extends Controller
{
    // ------------------------------------------------------------------
    // FITUR DASHBOARD
    // ------------------------------------------------------------------

    public function dashboard(Request $request)
    {
        $guru = Auth::user();
        $selectedKelasId = $request->input('kelas_id');

        // 1. Ambil seluruh kelas untuk dropdown filter
        $kelasList = Kelas::where('wali_kelas_id', $guru->id)
            ->orderBy('nama_kelas')
            ->get();

        if ($selectedKelasId !== null && $selectedKelasId !== '') {
            abort_unless($kelasList->contains('id', (int) $selectedKelasId), 404);
            $selectedKelasId = (int) $selectedKelasId;
        } else {
            $selectedKelasId = null;
        }

        $kelasMonitoring = $kelasList
            ->when($selectedKelasId, fn ($classes) => $classes->where('id', $selectedKelasId))
            ->values();

        foreach ($kelasMonitoring as $kelas) {
            $kelas->load(['siswa' => fn ($query) => $query->forRoles('siswa')->orderBy('name')]);
        }

        $totalSiswa = $kelasMonitoring->sum(fn ($kelas) => $kelas->siswa->count());
        $totalAktifSiswa = $kelasMonitoring->sum(
            fn ($kelas) => $kelas->siswa->filter(fn ($siswa) => $siswa->isOnline())->count()
        );

        // 2. Kalkulasi Statistik Real-Time dari Database
        $totalKelas = $kelasList->count();

        $totalAktivitas = class_exists(Aktivitas::class)
            ? Aktivitas::where('guru_id', $guru->id)
                ->when($selectedKelasId, fn($q) => $q->where('kelas_id', $selectedKelasId))
                ->count()
            : 0;

        $totalPendingSubmissions = class_exists(AktivitasSubmission::class)
            ? AktivitasSubmission::whereHas('aktivitas', function ($q) use ($guru, $selectedKelasId) {
                $q->where('guru_id', $guru->id);
                if ($selectedKelasId) {
                    $q->where('kelas_id', $selectedKelasId);
                }
            })->where('status', 'menunggu')->count()
            : 0;

        $totalExams = class_exists(Exam::class)
            ? Exam::where('created_by', $guru->id)
                ->when($selectedKelasId, fn($q) => $q->where('kelas_id', $selectedKelasId))
                ->count()
            : 0;

        // 3. Ambil Pengumuman Guru
        $pengumuman = Schema::hasTable('pengumuman')
            ? Pengumuman::where(function ($query) use ($guru) {
                $query->where('guru_id', $guru->id)
                    ->orWhere(function ($query) {
                        $query->whereIn('target_audience', ['guru', 'semua'])
                            ->whereHas('guru', fn ($query) => $query->where('role', 'admin'));
                    });
            })
                ->when($selectedKelasId, function ($query) use ($selectedKelasId) {
                    $query->where(function ($q) use ($selectedKelasId) {
                        $q->whereNull('kelas_id')->orWhere('kelas_id', $selectedKelasId);
                    });
                })
                ->latest()
                ->get()
            : collect();

        return view('dashboard-guru', compact(
            'guru',
            'kelasList',
            'kelasMonitoring',
            'selectedKelasId',
            'totalKelas',
            'totalAktivitas',
            'totalPendingSubmissions',
            'totalExams',
            'totalSiswa',
            'totalAktifSiswa',
            'pengumuman'
        ));
    }

    public function storeAnnouncement(Request $request)
    {
        $guru = Auth::user();

        $request->validate([
            'kelas_id' => 'nullable|exists:kelas,id',
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
        ]);

        Pengumuman::create([
            'guru_id' => $guru->id,
            'kelas_id' => $request->kelas_id,
            'judul' => $request->judul,
            'isi' => $request->isi,
            'diterbitkan_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Pengumuman baru berhasil dipublikasikan!');
    }

    // ------------------------------------------------------------------
    // FITUR MATERI
    // ------------------------------------------------------------------

    // 1. Menampilkan halaman daftar materi untuk guru (Card Grid Style) + Markdown Render
    public function materiIndex(MarkdownRenderer $markdownRenderer)
    {
        $isAdmin = Auth::user()->role === 'admin';
        $kelasIds = Kelas::where('wali_kelas_id', Auth::id())->pluck('id');
        $kelasList = $isAdmin
            ? Kelas::with('wali')->orderBy('nama_kelas')->get()
            : Kelas::whereIn('id', $kelasIds)->with('wali')->orderBy('nama_kelas')->get();
        $teacherList = $isAdmin ? User::forRoles('guru')->orderBy('name')->get() : collect();
        $selectedClassId = request()->integer('kelas_id');
        $selectedTeacherId = request()->integer('guru_id');
        $selectedStatus = request('status');

        $materiQuery = Materi::with(['kelas.wali', 'mapel']);
        if (!$isAdmin) {
            $materiQuery->whereIn('kelas_id', $kelasIds);
        } else {
            $materiQuery
                ->when($selectedClassId > 0, fn ($query) => $query->where('kelas_id', $selectedClassId))
                ->when($selectedTeacherId > 0, fn ($query) => $query->whereHas('kelas', fn ($kelasQuery) => $kelasQuery->where('wali_kelas_id', $selectedTeacherId)))
                ->when(in_array($selectedStatus, ['draft', 'aktif', 'terkunci'], true), fn ($query) => $query->where('status', $selectedStatus));
        }
        $materiList = $materiQuery->orderBy('urutan')->get();

        // Render Markdown pada kolom konten untuk setiap materi
        $materiList->transform(function ($item) use ($markdownRenderer) {
            $item->rendered_konten = $markdownRenderer->render($item->konten);
            return $item;
        });

        // Mengirim variabel $materiList ke view 'guru-materi'
        return view('guru-materi', compact('materiList', 'kelasList', 'isAdmin', 'teacherList', 'selectedClassId', 'selectedTeacherId', 'selectedStatus'));
    }

    public function materiEdit($id)
    {
        $isAdmin = Auth::user()->role === 'admin';
        $kelasList = Kelas::query()
            ->when(!$isAdmin, fn ($query) => $query->where('wali_kelas_id', Auth::id()))
            ->with('wali')
            ->orderBy('nama_kelas')
            ->get();

        $materiQuery = Materi::with(['kelas', 'mapel']);
        if (!$isAdmin) {
            $materiQuery->whereIn('kelas_id', $kelasList->modelKeys());
        }
        $materi = $materiQuery->findOrFail($id);

        return view('guru-materi-edit', compact('materi', 'kelasList', 'isAdmin'));
    }

    // 2. Menyimpan materi baru + Unggah File + Pekan
    public function materiStore(Request $request, LearningNotificationService $notifications)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'pekan' => 'required|string|max:50',
            'kelas_id' => 'required|exists:kelas,id',
            'konten' => 'nullable|string',
            'status' => 'nullable|in:draft,aktif,terkunci',
            'file_materi' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx|max:10240',
        ]);

        if ($request->user()->role !== 'admin') {
            abort_unless(
                Kelas::whereKey($request->kelas_id)->where('wali_kelas_id', Auth::id())->exists(),
                403,
                'Materi hanya dapat diterbitkan untuk kelas yang Anda ampu.'
            );
        }

        $filePath = null;
        if ($request->hasFile('file_materi')) {
            $filePath = $request->file('file_materi')->store('materi_files', 'public');
        }

        $mapel = MataPelajaran::firstOrCreate(['nama_mapel' => 'Matematika']);
        $nextUrutan = Materi::where('mapel_id', $mapel->id)->where('kelas_id', $request->kelas_id)->count() + 1;

        $materi = Materi::create([
            'judul' => $request->judul,
            'pekan' => $request->pekan,
            'konten' => $request->konten ?? 'Materi Pembelajaran',
            'file_path' => $filePath,
            'mapel_id' => $mapel->id,
            'kelas_id' => $request->kelas_id,
            'urutan' => $nextUrutan,
            'status' => $request->status ?? 'aktif',
        ]);

        if ($materi->status === 'aktif') {
            $notifications->notifyStudentsInClass((int) $materi->kelas_id, new LearningNotification(
                type: 'material',
                title: 'Materi Baru',
                message: 'Materi baru tersedia: ' . $materi->judul,
                url: route('siswa.materi.show', $materi->id),
                eventKey: 'material.published:' . $materi->id
            ));
        }

        $route = $request->user()->role === 'admin' ? 'admin.materi.index' : 'guru.materi.index';

        return redirect()->route($route)->with('success', 'Modul materi berhasil disimpan.');
    }

    // 3. Memperbarui Materi (Edit)
    public function materiUpdate(Request $request, $id, LearningNotificationService $notifications)
    {
        $data = $request->validate([
            'kelas_id' => ['sometimes', 'required', 'exists:kelas,id'],
            'judul' => 'required|string|max:255',
            'pekan' => 'required|string|max:50',
            'konten' => 'nullable|string',
            'status' => 'nullable|in:draft,aktif,terkunci',
            'file_materi' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx|max:10240',
        ]);

        $kelasIds = Kelas::where('wali_kelas_id', Auth::id())->pluck('id');
        $materiQuery = Materi::query();
        if ($request->user()->role !== 'admin') {
            $materiQuery->whereIn('kelas_id', $kelasIds);
            if (isset($data['kelas_id'])) {
                abort_unless($kelasIds->contains((int) $data['kelas_id']), 403, 'Materi hanya dapat dipindahkan ke kelas yang Anda ampu.');
            }
        }
        $materi = $materiQuery->findOrFail($id);
        $wasActive = $materi->status === 'aktif';

        if ($request->hasFile('file_materi')) {
            if ($materi->file_path && Storage::disk('public')->exists($materi->file_path)) {
                Storage::disk('public')->delete($materi->file_path);
            }
            $materi->file_path = $request->file('file_materi')->store('materi_files', 'public');
        }

        $classChanged = isset($data['kelas_id']) && (int) $data['kelas_id'] !== (int) $materi->kelas_id;
        $updates = [
            'judul' => $request->judul,
            'pekan' => $request->pekan,
            'konten' => $request->konten,
            'status' => $request->status ?? $materi->status,
        ];
        if (isset($data['kelas_id'])) {
            $updates['kelas_id'] = $data['kelas_id'];
        }
        $materi->update($updates);

        if ($materi->status === 'aktif' && (! $wasActive || $classChanged)) {
            $notifications->notifyStudentsInClass((int) $materi->kelas_id, new LearningNotification(
                type: 'material',
                title: 'Materi Baru',
                message: 'Materi baru tersedia: ' . $materi->judul,
                url: route('siswa.materi.show', $materi->id),
                eventKey: 'material.published:' . $materi->id . ':' . $materi->kelas_id . ':' . $materi->updated_at->timestamp
            ));
        }

        $route = $request->user()->role === 'admin' ? 'admin.materi.index' : 'guru.materi.index';

        return redirect()->route($route)->with('success', 'Modul materi berhasil diperbarui!');
    }

    // 4. Mengubah Status Lock / Unlock Materi
    public function materiToggleLock(Request $request, $id, LearningNotificationService $notifications)
    {
        $materi = Materi::findOrFail($id);
        if ($request->user()->role !== 'admin') {
            abort_unless(
                Kelas::whereKey($materi->kelas_id)->where('wali_kelas_id', Auth::id())->exists(),
                403,
                'Anda tidak mengampu kelas materi ini.'
            );
        }
        $newStatus = $request->input('status', $materi->status === 'aktif' ? 'terkunci' : 'aktif');

        if (! in_array($newStatus, ['aktif', 'terkunci'], true)) {
            $newStatus = $materi->status === 'aktif' ? 'terkunci' : 'aktif';
        }

        $materi->update([
            'status' => $newStatus,
        ]);

        if ($newStatus === 'aktif') {
            $notifications->notifyStudentsInClass((int) $materi->kelas_id, new LearningNotification(
                type: 'material',
                title: 'Materi Dibuka',
                message: 'Materi ' . $materi->judul . ' sekarang dapat diakses.',
                url: route('siswa.materi.show', $materi->id),
                eventKey: 'material.unlocked:' . $materi->id . ':' . $materi->updated_at->timestamp
            ));
        }

        $pesan = $newStatus === 'aktif'
            ? 'Materi berhasil dibuka (Unlocked) untuk siswa!'
            : 'Materi berhasil dikunci (Locked).';

        $route = $request->user()->role === 'admin' ? 'admin.materi.index' : 'guru.materi.index';

        return redirect()->route($route)->with('success', $pesan);
    }

    // 5. Menghapus materi
    public function materiDestroy($id)
    {
        $materi = Materi::findOrFail($id);
        if (Auth::user()->role !== 'admin') {
            abort_unless(
                Kelas::whereKey($materi->kelas_id)->where('wali_kelas_id', Auth::id())->exists(),
                403,
                'Anda tidak mengampu kelas materi ini.'
            );
        }

        if ($materi->file_path && Storage::disk('public')->exists($materi->file_path)) {
            Storage::disk('public')->delete($materi->file_path);
        }

        $materi->delete();

        $route = Auth::user()->role === 'admin' ? 'admin.materi.index' : 'guru.materi.index';

        return redirect()->route($route)->with('success', 'Modul materi beserta dokumen berhasil dihapus.');
    }

    // 6. Fungsi untuk membuka (unlock) materi siswa berikutnya
    public function unlockMateri(Request $request, $id)
    {
        $progressQuery = MateriProgress::whereKey($id);
        if ($request->user()->role !== 'admin') {
            $progressQuery->whereHas('materi.kelas', fn ($query) => $query->where('wali_kelas_id', Auth::id()));
        }
        $progress = $progressQuery->firstOrFail();
        $progress->update([
            'status' => 'unlocked',
            'unlocked_by' => auth()->id(),
            'unlocked_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Materi berhasil dibuka untuk siswa!');
    }

    // ------------------------------------------------------------------
    // FITUR TUGAS (ASSIGNMENT)
    // ------------------------------------------------------------------

    // 1. Menampilkan Dashboard Daftar Tugas Guru (Grid Card View)
    public function tugasIndex()
    {
        $isAdmin = Auth::user()->role === 'admin';
        $kelasIds = Kelas::where('wali_kelas_id', Auth::id())->pluck('id');
        $kelasList = $isAdmin ? Kelas::with('wali')->orderBy('nama_kelas')->get() : Kelas::whereIn('id', $kelasIds)->with('wali')->orderBy('nama_kelas')->get();
        $teacherList = $isAdmin ? User::forRoles('guru')->orderBy('name')->get() : collect();
        $selectedClassId = request()->integer('kelas_id');
        $selectedTeacherId = request()->integer('guru_id');
        $selectedStatus = request('status');
        $query = Tugas::with(['materi.kelas.wali']);

        if (!$isAdmin) {
            $query->whereHas('materi', fn ($materiQuery) => $materiQuery->whereIn('kelas_id', $kelasIds));
        } else {
            $query
                ->when($selectedClassId > 0, fn ($tugasQuery) => $tugasQuery->whereHas('materi', fn ($materiQuery) => $materiQuery->where('kelas_id', $selectedClassId)))
                ->when($selectedTeacherId > 0, fn ($tugasQuery) => $tugasQuery->whereHas('materi.kelas', fn ($kelasQuery) => $kelasQuery->where('wali_kelas_id', $selectedTeacherId)))
                ->when(in_array($selectedStatus, ['aktif', 'terkunci'], true), fn ($tugasQuery) => $tugasQuery->where('status', $selectedStatus));
        }

        $tugasList = $query->latest()->get();
        $tugases = $tugasList;
        $routePrefix = $isAdmin ? 'admin' : 'guru';

        return view('guru-tugas-index', compact('tugasList', 'tugases', 'isAdmin', 'routePrefix', 'kelasList', 'teacherList', 'selectedClassId', 'selectedTeacherId', 'selectedStatus'));
    }

    // 2. Menampilkan Form Buat Tugas Baru
    public function tugasCreate()
    {
        $isAdmin = Auth::user()->role === 'admin';
        $kelasIds = Kelas::where('wali_kelas_id', Auth::id())->pluck('id');
        $materiList = Materi::with(['kelas.wali'])
            ->when(!$isAdmin, fn ($query) => $query->whereIn('kelas_id', $kelasIds))
            ->orderBy('judul')
            ->get();
        $routePrefix = $isAdmin ? 'admin' : 'guru';

        return view('guru-tugas-form', compact('materiList', 'isAdmin', 'routePrefix'));
    }

    // 3. Menyimpan Tugas Baru (Otomatis Mengisi materi_id)
    public function tugasStore(Request $request, LearningNotificationService $notifications)
    {
        $request->validate([
            'materi_id' => 'required|exists:materi,id',
            'judul' => 'required|string|max:255',
            'pekan' => 'nullable|string|max:50',
            'tenggat_waktu' => 'required',
            'deskripsi' => 'nullable|string',
            'file_tugas' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,jpg,jpeg,png,zip,rar|max:10240',
            'status' => 'nullable|in:aktif,terkunci',
        ]);

        $kelasIds = Kelas::where('wali_kelas_id', Auth::id())->pluck('id');
        $materiQuery = Materi::query();
        if ($request->user()->role !== 'admin') {
            $materiQuery->whereIn('kelas_id', $kelasIds);
        }
        $materi = $materiQuery->findOrFail($request->materi_id);

        $filePath = null;
        if ($request->hasFile('file_tugas')) {
            $filePath = $request->file('file_tugas')->store('tugas_files', 'public');
        }

        $tugas = Tugas::create([
            'materi_id' => $materi->id,
            'judul' => $request->judul,
            'pekan' => $request->pekan ?? 'Pekan 1',
            'tenggat_waktu' => $request->tenggat_waktu,
            'deskripsi' => $request->deskripsi,
            'file_path' => $filePath,
            'status' => $request->input('status', 'aktif'),
        ]);

        if ($tugas->status === 'aktif') {
        $notifications->notifyStudentsInClass((int) $materi->kelas_id, new LearningNotification(
            type: 'assignment',
            title: 'Tugas Baru',
            message: 'Tugas baru tersedia: ' . $tugas->judul,
            url: route('siswa.tugas.show', $tugas->id),
            eventKey: 'task.published:' . $tugas->id
        ));
        }

        $route = $request->user()->role === 'admin' ? 'admin.tugas.index' : 'guru.tugas.index';

        return redirect()->route($route)->with('success', 'Paket tugas berhasil disimpan.');
    }

    // 4. Menampilkan Form Edit Tugas
    public function tugasEdit($id)
    {
        $isAdmin = Auth::user()->role === 'admin';
        $kelasIds = Kelas::where('wali_kelas_id', Auth::id())->pluck('id');
        $tugasQuery = Tugas::query();
        $materiQuery = Materi::with('kelas.wali');
        if (!$isAdmin) {
            $tugasQuery->whereHas('materi', fn ($query) => $query->whereIn('kelas_id', $kelasIds));
            $materiQuery->whereIn('kelas_id', $kelasIds);
        }
        $tugas = $tugasQuery->findOrFail($id);
        $materiList = $materiQuery->orderBy('judul')->get();
        $routePrefix = $isAdmin ? 'admin' : 'guru';

        return view('guru-tugas-form', compact('tugas', 'materiList', 'isAdmin', 'routePrefix'));
    }

    public function tugasUpdate(Request $request, $id, LearningNotificationService $notifications)
    {
        $request->validate([
            'materi_id' => 'required|exists:materi,id',
            'judul' => 'required|string|max:255',
            'pekan' => 'nullable|string|max:50',
            'tenggat_waktu' => 'required',
            'deskripsi' => 'nullable|string',
            'file_tugas' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,jpg,jpeg,png,zip,rar|max:10240',
            'status' => 'nullable|in:aktif,terkunci',
        ]);

        $kelasIds = Kelas::where('wali_kelas_id', Auth::id())->pluck('id');
        $tugasQuery = Tugas::query();
        $materiQuery = Materi::query();
        if ($request->user()->role !== 'admin') {
            $tugasQuery->whereHas('materi', fn ($query) => $query->whereIn('kelas_id', $kelasIds));
            $materiQuery->whereIn('kelas_id', $kelasIds);
        }
        $tugas = $tugasQuery->findOrFail($id);
        $wasActive = $tugas->status === 'aktif';
        $materi = $materiQuery->findOrFail($request->materi_id);

        if ($request->hasFile('file_tugas')) {
            if ($tugas->file_path && Storage::disk('public')->exists($tugas->file_path)) {
                Storage::disk('public')->delete($tugas->file_path);
            }
            $tugas->file_path = $request->file('file_tugas')->store('tugas_files', 'public');
        }

        $tugas->update([
            'materi_id' => $materi->id,
            'judul' => $request->judul,
            'pekan' => $request->pekan ?? 'Pekan 1',
            'tenggat_waktu' => $request->tenggat_waktu,
            'deskripsi' => $request->deskripsi,
            'status' => $request->input('status', $tugas->status ?? 'aktif'),
        ]);

        if ($tugas->status === 'aktif' && !$wasActive) {
            $notifications->notifyStudentsInClass((int) $materi->kelas_id, new LearningNotification(
                type: 'assignment',
                title: 'Tugas Baru',
                message: 'Tugas baru tersedia: ' . $tugas->judul,
                url: route('siswa.tugas.show', $tugas->id),
                eventKey: 'task.published:' . $tugas->id . ':' . $tugas->updated_at->timestamp
            ));
        }

        $route = $request->user()->role === 'admin' ? 'admin.tugas.index' : 'guru.tugas.index';

        return redirect()->route($route)->with('success', 'Paket tugas berhasil diperbarui!');
    }

    // 6. Menghapus Tugas
    public function tugasDestroy($id)
    {
        $kelasIds = Kelas::where('wali_kelas_id', Auth::id())->pluck('id');
        $tugasQuery = Tugas::query();
        if (Auth::user()->role !== 'admin') {
            $tugasQuery->whereHas('materi', fn ($query) => $query->whereIn('kelas_id', $kelasIds));
        }
        $tugas = $tugasQuery->findOrFail($id);
        if ($tugas->file_path && Storage::disk('public')->exists($tugas->file_path)) {
            Storage::disk('public')->delete($tugas->file_path);
        }
        $tugas->delete();

        $route = Auth::user()->role === 'admin' ? 'admin.tugas.index' : 'guru.tugas.index';

        return redirect()->route($route)->with('success', 'Paket tugas berhasil dihapus.');
    }

    // ------------------------------------------------------------------
    // FITUR UJIAN / QUIZ
    // ------------------------------------------------------------------

    // Menampilkan Dashboard Daftar Ujian
    public function ujianIndex()
    {
        $ujians = Ujian::orderBy('created_at', 'desc')->get();
        return view('guru-ujian-index', compact('ujians'));
    }

    // Menampilkan Form Buat Ujian Baru
    public function ujianCreate()
    {
        return view('guru-ujian-form');
    }

    // Menyimpan Ujian Baru ke Database
    public function ujianStore(Request $request)
    {
        $request->validate([
            'judul_ujian' => 'required|string|max:255',
            'durasi_menit' => 'required|integer|min:1',
            'questions' => 'required|array|min:1',
        ]);

        Ujian::create([
            'judul_ujian' => $request->judul_ujian,
            'deskripsi_ujian' => $request->deskripsi_ujian,
            'durasi_menit' => $request->durasi_menit,
            'questions' => $request->questions,
        ]);

        return redirect()->route('guru.ujian.index')->with('success', 'Paket ujian berhasil dibuat!');
    }

    // Menampilkan Form Edit Ujian
    public function ujianEdit($id)
    {
        $ujian = Ujian::findOrFail($id);
        return view('guru-ujian-form', compact('ujian'));
    }

    // Memperbarui Ujian
    public function ujianUpdate(Request $request, $id)
    {
        $request->validate([
            'judul_ujian' => 'required|string|max:255',
            'durasi_menit' => 'required|integer|min:1',
            'questions' => 'required|array|min:1',
        ]);

        $ujian = Ujian::findOrFail($id);
        $ujian->update([
            'judul_ujian' => $request->judul_ujian,
            'deskripsi_ujian' => $request->deskripsi_ujian,
            'durasi_menit' => $request->durasi_menit,
            'questions' => $request->questions,
        ]);

        return redirect()->route('guru.ujian.index')->with('success', 'Paket ujian berhasil diperbarui!');
    }

    // Menghapus Ujian
    public function ujianDestroy($id)
    {
        $ujian = Ujian::findOrFail($id);
        $ujian->delete();

        return redirect()->route('guru.ujian.index')->with('success', 'Paket ujian berhasil dihapus.');
    }

    // ------------------------------------------------------------------
    // FITUR PENILAIAN & EVALUASI
    // ------------------------------------------------------------------

    // Menampilkan daftar tugas & ujian yang dikumpulkan siswa
    public function penilaianIndex()
    {
        $submissions = $this->teacherSubmissions()->with(['tugas.materi.mapel', 'siswa'])
            ->orderBy('created_at', 'desc')
            ->get();

        $ujianSubmissions = UjianSubmission::with(['siswa', 'ujian'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('guru-penilaian', compact('submissions', 'ujianSubmissions'));
    }

    private function teacherSubmissions()
    {
        $kelasIds = Kelas::where('wali_kelas_id', Auth::id())->select('id');

        return Submission::whereHas('tugas.materi', fn ($query) => $query->whereIn('kelas_id', $kelasIds));
    }

    private function notifyStudentGrade(
        ?User $student,
        string $title,
        string $message,
        string $url,
        string $eventKey,
        LearningNotificationService $notifications
    ): void {
        if (!$student) {
            return;
        }

        $notifications->sendOnce($student, new LearningNotification(
            type: 'feedback',
            title: $title,
            message: $message,
            url: $url,
            eventKey: $eventKey
        ));
    }

    // Menampilkan Laman Detail Pemeriksaan Ujian per Siswa
    public function penilaianUjianDetail($id)
    {
        $sub = UjianSubmission::with(['siswa', 'ujian'])->findOrFail($id);
        return view('guru-penilaian-ujian-detail', compact('sub'));
    }

    // Menyimpan nilai dan catatan evaluasi tugas dari guru
    public function storePenilaian(Request $request, $id, LearningNotificationService $notifications)
    {
        $request->validate([
            'nilai' => 'required|integer|min:0|max:100',
            'catatan_guru' => 'nullable|string|max:500',
        ]);

        $submission = $this->teacherSubmissions()->findOrFail($id);
        $submission->update([
            'nilai' => $request->nilai,
            'catatan_guru' => $request->catatan_guru,
            'status' => 'dinilai',
        ]);

        $submission->loadMissing(['siswa', 'tugas']);
        $this->notifyStudentGrade(
            $submission->siswa,
            'Penilaian Tugas Tersedia',
            'Nilai dan feedback untuk tugas ' . $submission->tugas?->judul . ' sudah tersedia.',
            route('siswa.evaluasi'),
            'task.graded:' . $submission->id . ':' . $submission->updated_at->timestamp,
            $notifications
        );

        return redirect()->back()->with('success', 'Nilai dan evaluasi tugas berhasil disimpan!');
    }

    // Menyimpan Penilaian Ujian (Nilai dan Rekomendasi Belajar)
    public function storePenilaianUjian(Request $request, $id, LearningNotificationService $notifications)
    {
        $request->validate([
            'nilai' => 'required|integer|min:0|max:100',
            'rekomendasi_belajar' => 'nullable|string',
        ]);

        $sub = UjianSubmission::findOrFail($id);
        $sub->update([
            'nilai' => $request->nilai,
            'rekomendasi_belajar' => $request->rekomendasi_belajar,
            'status' => 'dinilai',
        ]);

        $sub->loadMissing(['siswa', 'ujian']);
        $this->notifyStudentGrade(
            $sub->siswa,
            'Hasil Kuis Tersedia',
            'Hasil kuis ' . $sub->ujian?->judul_ujian . ' dan rekomendasi belajar sudah tersedia.',
            route('siswa.evaluasi.ujian.detail', $sub->id),
            'quiz.graded:' . $sub->id . ':' . $sub->updated_at->timestamp,
            $notifications
        );

        return redirect()->route('guru.penilaian.index')->with('success', 'Penilaian dan rekomendasi belajar ujian siswa berhasil disimpan!');
    }

    // Fungsi simulasi Penilaian Tugas dengan Bantuan AI
    public function nilaiDenganAi($id)
    {
        $submission = $this->teacherSubmissions()->findOrFail($id);

        $jawabanSiswa = strtolower($submission->jawaban);
        $skorAi = 85; 
        $feedbackAi = "Analisis AI: Jawaban siswa menunjukkan pemahaman konsep yang baik, struktur penjelasan sudah runtut, namun perlu diperdalam pada bagian contoh implementasi.";

        if (strlen($jawabanSiswa) < 10) {
            $skorAi = 45;
            $feedbackAi = "Analisis AI: Jawaban terlalu singkat dan kurang mendefinisikan konsep utama dengan akurat.";
        }

        $submission->update([
            'ai_score' => $skorAi,
            'ai_feedback' => $feedbackAi,
            'final_score' => $skorAi, 
            'final_feedback' => $feedbackAi,
            'status' => 'dinilai_ai',
        ]);

        return redirect()->back()->with('success', 'Penilaian AI berhasil dijalankan! Guru dapat meninjau dan memodifikasinya.');
    }

    // Fungsi untuk menyimpan revisi/modifikasi nilai final oleh Guru (Tugas)
    public function updateNilaiFinal(Request $request, $id, LearningNotificationService $notifications)
    {
        $request->validate([
            'final_score' => 'required|numeric|min:0|max:100',
            'final_feedback' => 'required|string',
        ]);

        $submission = $this->teacherSubmissions()->findOrFail($id);
        $submission->update([
            'final_score' => $request->final_score,
            'final_feedback' => $request->final_feedback,
            'status' => 'selesai',
        ]);

        $submission->loadMissing(['siswa', 'tugas']);
        $this->notifyStudentGrade(
            $submission->siswa,
            'Nilai dan Feedback Tersedia',
            'Nilai akhir dan feedback untuk tugas ' . $submission->tugas?->judul . ' sudah tersedia.',
            route('siswa.evaluasi'),
            'task.final-graded:' . $submission->id . ':' . $submission->updated_at->timestamp,
            $notifications
        );

        return redirect()->back()->with('success', 'Nilai dan feedback final berhasil disimpan!');
    }

    // Simulasi AI Auto-Correct Ujian
    public function aiAutoCorrectUjian($id)
    {
        $sub = UjianSubmission::with('ujian')->findOrFail($id);
        
        $questions = $sub->ujian->questions ?? [];
        $studentAnswers = $sub->jawaban ?? [];
        $totalQuestions = count($questions);
        $correctCount = 0;

        foreach ($questions as $index => $q) {
            $studentAns = isset($studentAnswers[$index]) ? trim(strtolower((string)$studentAnswers[$index])) : '';
            $correctAns = isset($q['answer']) ? trim(strtolower((string)$q['answer'])) : '';

            if ($studentAns !== '' && $correctAns !== '') {
                if ($studentAns === $correctAns || str_contains($studentAns, $correctAns) || str_contains($correctAns, $studentAns)) {
                    $correctCount++;
                }
            }
        }

        $skorAi = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100) : 0;

        $rekomendasiTinggi = [
            "Ananda menunjukkan penguasaan yang sangat baik terhadap konsep rasio dan proporsi. Sebagai tindak lanjut akademik, disarankan untuk memperkaya pemahaman melalui eksplorasi soal pemecahan masalah (HOTS) terkait skala pada denah dan perbandingan berbalik nilai.",
            "Prestasi yang sangat memuaskan dalam menyelesaikan evaluasi materi perbandingan. Ananda dapat diberikan pengayaan berupa proyek mini penerapan rasio dalam kehidupan sehari-hari."
        ];

        $rekomendasiSedang = [
            "Ananda telah memahami konsep dasar rasio dengan cukup baik, namun masih memerlukan ketelitian dalam menyederhanakan bentuk perbandingan. Disarankan untuk memperbanyak latihan soal mandiri terkait rasio senilai dan proporsi.",
            "Pemahaman konsep sudah cukup baik, tetapi langkah-langkah penyelesaian pada soal uraian perlu dituliskan secara lebih sistematis sesuai kaidah matematika kelas VII."
        ];

        $rekomendasiRendah = [
            "Ananda memerlukan bimbingan remedial pada konsep fundamental rasio, khususnya dalam membandingkan dua besaran dengan satuan sejenis. Disarankan untuk mengulang kembali modul materi dasar.",
            "Dibutuhkan pendampingan intensif dalam menerjemahkan kalimat soal cerita perbandingan ke dalam model matematika. Mari fokuskan pada latihan dasar penyederhanaan pecahan."
        ];

        if ($skorAi >= 85) {
            $rekomendasiAi = $rekomendasiTinggi[array_rand($rekomendasiTinggi)];
        } elseif ($skorAi >= 70) {
            $rekomendasiAi = $rekomendasiSedang[array_rand($rekomendasiSedang)];
        } else {
            $rekomendasiAi = $rekomendasiRendah[array_rand($rekomendasiRendah)];
        }

        $sub->update([
            'nilai' => $skorAi,
            'rekomendasi_belajar' => $rekomendasiAi,
            'status' => 'dinilai',
        ]);

        return redirect()->back()->with('success', '🤖 AI Auto-Correct berhasil mengevaluasi ketepatan jawaban siswa dan menetapkan nilai secara objektif!');
    }

    // ------------------------------------------------------------------
    // FITUR PRESENSI
    // ------------------------------------------------------------------

    // Menampilkan rekapitulasi presensi seluruh siswa untuk Guru
    public function presensiIndex()
    {
        $absensis = Presensi::with('siswa')->orderBy('created_at', 'desc')->get();
        return view('guru-presensi', compact('absensis'));
    }

    // Mengunduh rekapitulasi presensi dalam format PDF
    public function presensiPdf()
    {
        $absensis = Presensi::with('siswa')->orderBy('created_at', 'desc')->get();
        return view('guru-presensi-pdf', compact('absensis'));
    }

    // Menampilkan Laman Detail Pemeriksaan Tugas per Siswa
    public function penilaianTugasDetail($id)
    {
        $sub = $this->teacherSubmissions()->with(['siswa', 'tugas.materi.mapel'])->findOrFail($id);
        return view('guru-penilaian-tugas-detail', compact('sub'));
    }

    // AI Auto-Correct & Umpan Balik Otomatis untuk Tugas
    public function aiAutoCorrectTugas($id)
    {
        $sub = $this->teacherSubmissions()->with('tugas')->findOrFail($id);

        $jawabanSiswa = trim((string) $sub->jawaban);
        $hasFile = !empty($sub->file_path) && Storage::disk('public')->exists($sub->file_path);
        
        $skorAi = 85;
        $feedbackAi = "Analisis AI: Berkas tugas dan jawaban siswa menunjukkan pemahaman konsep yang baik, penjelasan sudah runtut dan sesuai instruksi tugas.";

        if (strlen($jawabanSiswa) < 10 && !$hasFile) {
            $skorAi = 45;
            $feedbackAi = "Analisis AI: Jawaban terlalu singkat dan kurang mendefinisikan konsep utama dengan akurat.";
        } elseif ($hasFile && strlen($jawabanSiswa) < 10) {
            $skorAi = 88;
            $feedbackAi = "Analisis AI: Dokumen berkas tugas berhasil diunggah dengan lengkap. Struktur jawaban tertata rapi sesuai petunjuk pengerjaan.";
        }

        // Susun data yang akan di-update secara terproteksi
        $updateData = [
            'nilai' => $sub->nilai ?? $skorAi,
            'catatan_guru' => $sub->catatan_guru ?? $feedbackAi,
        ];

        if (Schema::hasColumn('submissions', 'ai_score')) {
            $updateData['ai_score'] = $skorAi;
        }

        if (Schema::hasColumn('submissions', 'ai_feedback')) {
            $updateData['ai_feedback'] = $feedbackAi;
        }

        if (Schema::hasColumn('submissions', 'final_score')) {
            $updateData['final_score'] = $sub->final_score ?? $skorAi;
        }

        if (Schema::hasColumn('submissions', 'final_feedback')) {
            $updateData['final_feedback'] = $sub->final_feedback ?? $feedbackAi;
        }

        if (Schema::hasColumn('submissions', 'status')) {
            $updateData['status'] = 'dinilai_ai';
        }

        $sub->update($updateData);

        return redirect()->back()->with('success', '🤖 AI berhasil mengevaluasi tugas dan menghasilkan Nilai & Umpan Balik!');
    }

    // Menyimpan Penilaian Final Tugas dari Guru
    public function storePenilaianTugas(Request $request, $id, LearningNotificationService $notifications)
    {
        $request->validate([
            'nilai' => 'required|integer|min:0|max:100',
            'catatan_guru' => 'required|string',
        ]);

        $sub = $this->teacherSubmissions()->findOrFail($id);

        $updateData = [
            'nilai' => $request->nilai,
            'catatan_guru' => $request->catatan_guru,
        ];

        if (Schema::hasColumn('submissions', 'final_score')) {
            $updateData['final_score'] = $request->nilai;
        }

        if (Schema::hasColumn('submissions', 'final_feedback')) {
            $updateData['final_feedback'] = $request->catatan_guru;
        }

        if (Schema::hasColumn('submissions', 'status')) {
            $updateData['status'] = 'dinilai';
        }

        $sub->update($updateData);

        $sub->loadMissing(['siswa', 'tugas']);
        $this->notifyStudentGrade(
            $sub->siswa,
            'Nilai dan Feedback Tugas Tersedia',
            'Tugas ' . $sub->tugas?->judul . ' sudah dinilai dan feedback dapat dilihat.',
            route('siswa.evaluasi'),
            'task.final-graded:' . $sub->id . ':' . $sub->updated_at->timestamp,
            $notifications
        );

        return redirect()->route('guru.penilaian.index')->with('success', 'Penilaian final dan umpan balik tugas siswa berhasil disimpan!');
    }

    // ==========================================
    // KELOLA REFLEKSI PEMBELAJARAN (POV GURU)
    // ==========================================

    public function refleksiIndex()
    {
        $refleksis = Refleksi::withCount('submissions')
            ->where('guru_id', auth()->id())
            ->orderBy('pertemuan', 'asc')
            ->get();

        return view('guru-refleksi', compact('refleksis'));
    }

    public function refleksiCreate()
    {
        return view('guru-refleksi-create');
    }

    public function refleksiStore(Request $request)
    {
        $request->validate([
            'judul_refleksi' => 'required|string|max:255',
            'pertemuan'      => 'required|integer|min:1',
            'deskripsi'      => 'nullable|string',
        ]);

        Refleksi::create([
            'guru_id'        => auth()->id(),
            'judul_refleksi' => $request->judul_refleksi,
            'pertemuan'      => $request->pertemuan,
            'deskripsi'      => $request->deskripsi,
            'status'         => 'published',
        ]);

        return redirect()->route('guru.refleksi.index')->with('success', 'Refleksi pembelajaran berhasil dipublikasikan ke siswa!');
    }

    public function refleksiDetail($id)
    {
        $refleksi = Refleksi::with(['submissions.siswa'])->findOrFail($id);
        return view('guru-refleksi-detail', compact('refleksi'));
    }

    public function refleksiDestroy($id)
    {
        $refleksi = Refleksi::where('guru_id', auth()->id())->findOrFail($id);
        $refleksi->delete();

        return redirect()->route('guru.refleksi.index')->with('success', 'Refleksi berhasil dihapus.');
    }
}