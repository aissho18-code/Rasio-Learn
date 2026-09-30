<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aktivitas;
use App\Models\Kelas;
use App\Models\User;
use App\Notifications\LearningNotification;
use App\Support\LearningNotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class GuruAktivitasController extends Controller
{
    public function index()
    {
        $guruId = Auth::id();
        $isAdmin = Auth::user()->role === 'admin';
        $kelasIds = Kelas::where('wali_kelas_id', $guruId)->pluck('id');
        $kelasList = $isAdmin ? Kelas::with('wali')->orderBy('nama_kelas')->get() : Kelas::whereIn('id', $kelasIds)->with('wali')->orderBy('nama_kelas')->get();
        $teacherList = $isAdmin ? User::forRoles('guru')->orderBy('name')->get() : collect();
        $selectedClassId = request()->integer('kelas_id');
        $selectedTeacherId = request()->integer('guru_id');
        $selectedStatus = request('status');

        $aktivitas = Aktivitas::with(['kelas', 'blocks', 'submissions'])
            ->when(!$isAdmin, fn ($query) => $query->where('guru_id', $guruId))
            ->when($isAdmin && $selectedClassId > 0, fn ($query) => $query->where('kelas_id', $selectedClassId))
            ->when($isAdmin && $selectedTeacherId > 0, fn ($query) => $query->where('guru_id', $selectedTeacherId))
            ->when($isAdmin && in_array($selectedStatus, ['draft', 'published'], true), fn ($query) => $query->where('status', $selectedStatus))
            ->latest()
            ->get();

        $routePrefix = $isAdmin ? 'admin' : 'guru';

        return view('guru-aktivitas-index', compact('aktivitas', 'isAdmin', 'routePrefix', 'kelasList', 'teacherList', 'selectedClassId', 'selectedTeacherId', 'selectedStatus'));
    }

    public function create()
    {
        $isAdmin = Auth::user()->role === 'admin';
        $kelasList = $isAdmin ? Kelas::orderBy('nama_kelas')->get() : Kelas::where('wali_kelas_id', Auth::id())->orderBy('nama_kelas')->get();
        $routePrefix = $isAdmin ? 'admin' : 'guru';
        $aktivitas = new Aktivitas();
        return view('guru-aktivitas-create', compact('kelasList', 'aktivitas', 'isAdmin', 'routePrefix'));
    }

    public function store(Request $r, LearningNotificationService $notifications)
    {
        $isAdmin = $r->user()->role === 'admin';
        $r->validate([
            'judul' => 'required|string|max:255',
            'tujuan' => 'nullable|string',
            'petunjuk' => 'nullable|string',
            'pertanyaan' => 'nullable|string',
            'respons_type' => 'nullable|in:file,text,both,interaktif',
            'lkpd' => 'nullable|file|mimes:pdf,doc,docx,png,jpg,jpeg|max:10240',
            'kelas_id' => [$isAdmin ? 'nullable' : 'required', 'exists:kelas,id'],
            'status' => 'nullable|in:draft,published',
            'blocks' => 'nullable|array',
        ]);

        if (!$isAdmin) {
            abort_unless(
                Kelas::whereKey($r->kelas_id)->where('wali_kelas_id', Auth::id())->exists(),
                403,
                'Aktivitas hanya dapat ditujukan ke kelas yang Anda ampu.'
            );
        }

        $aktivitas = DB::transaction(function () use ($r) {
            $data = $r->only(['judul', 'tujuan', 'petunjuk', 'pertanyaan', 'respons_type', 'kelas_id']);
            $data['guru_id'] = Auth::id();
            $data['status'] = $r->input('status', 'draft');

            if ($r->input('status') === 'published') {
                $data['published_at'] = now();
            }

            if ($r->hasFile('lkpd')) {
                $path = $r->file('lkpd')->store('lkpd', 'public');
                $data['lkpd_path'] = $path;
            }

            // 1. Simpan Data Utama Aktivitas
            $newAktivitas = Aktivitas::create($data);

            // 2. Simpan Blok Komponen Interaktif (jika ada)
            if ($r->has('blocks') && is_array($r->blocks)) {
                foreach ($r->blocks as $index => $blockData) {
                    $newAktivitas->blocks()->create([
                        'tahap' => $blockData['tahap'] ?? 'Eksplorasi',
                        'tipe' => $blockData['tipe'] ?? 'text',
                        'judul' => $blockData['judul'] ?? null,
                        'urutan' => $index,
                        'konfigurasi' => $blockData['konfigurasi'] ?? [],
                    ]);
                }
            }

            return $newAktivitas;
        });

        // 3. Kirim Notifikasi ke Siswa jika Status = Published
        if ($aktivitas->status === 'published' && $aktivitas->kelas_id) {
            $notification = new LearningNotification(
                type: 'activity',
                title: 'Aktivitas & LKPD Baru',
                message: 'Guru menerbitkan aktivitas baru: ' . $aktivitas->judul,
                url: route('siswa.aktivitas.show', $aktivitas),
                eventKey: 'activity.published:' . $aktivitas->id . ':' . $aktivitas->published_at->timestamp
            );

            $notifications->notifyStudentsInClass((int) $aktivitas->kelas_id, $notification);
        } elseif ($aktivitas->status === 'published') {
            $notifications->notifyAllStudents(new LearningNotification(
                type: 'activity',
                title: 'Aktivitas & LKPD Baru',
                message: 'Aktivitas baru tersedia: ' . $aktivitas->judul,
                url: route('siswa.aktivitas.show', $aktivitas),
                eventKey: 'activity.published:' . $aktivitas->id . ':' . $aktivitas->published_at->timestamp
            ));
        }

        $route = $isAdmin ? 'admin.aktivitas.index' : 'guru.aktivitas.index';

        return redirect()->route($route)->with('status', 'Aktivitas berhasil disimpan.');
    }

    public function edit($id)
    {
        $isAdmin = Auth::user()->role === 'admin';
        $aktivitasQuery = Aktivitas::with('blocks');
        if (!$isAdmin) {
            $aktivitasQuery->where('guru_id', Auth::id());
        }
        $aktivitas = $aktivitasQuery->findOrFail($id);
        $kelasList = $isAdmin ? Kelas::orderBy('nama_kelas')->get() : Kelas::where('wali_kelas_id', Auth::id())->orderBy('nama_kelas')->get();
        $routePrefix = $isAdmin ? 'admin' : 'guru';

        return view('guru-aktivitas-create', compact('aktivitas', 'kelasList', 'isAdmin', 'routePrefix'));
    }

    public function update(Request $r, $id, LearningNotificationService $notifications)
    {
        $isAdmin = $r->user()->role === 'admin';
        $aktivitasQuery = Aktivitas::query();
        if (!$isAdmin) {
            $aktivitasQuery->where('guru_id', Auth::id());
        }
        $aktivitas = $aktivitasQuery->findOrFail($id);

        $r->validate([
            'judul' => 'required|string|max:255',
            'tujuan' => 'nullable|string',
            'petunjuk' => 'nullable|string',
            'kelas_id' => [$isAdmin ? 'nullable' : 'required', 'exists:kelas,id'],
            'status' => 'nullable|in:draft,published',
            'blocks' => 'nullable|array',
        ]);

        if (!$isAdmin) {
            abort_unless(
                Kelas::whereKey($r->kelas_id)->where('wali_kelas_id', Auth::id())->exists(),
                403,
                'Aktivitas hanya dapat ditujukan ke kelas yang Anda ampu.'
            );
        }

        $wasPublished = $aktivitas->status === 'published';

        DB::transaction(function () use ($r, $aktivitas, $wasPublished) {
            $data = $r->only(['judul', 'tujuan', 'petunjuk', 'pertanyaan', 'respons_type', 'kelas_id']);
            $data['status'] = $r->input('status', 'draft');

            if ($data['status'] === 'published' && ! $wasPublished) {
                $data['published_at'] = now();
            }

            if ($r->hasFile('lkpd')) {
                if ($aktivitas->lkpd_path && Storage::disk('public')->exists($aktivitas->lkpd_path)) {
                    Storage::disk('public')->delete($aktivitas->lkpd_path);
                }
                $data['lkpd_path'] = $r->file('lkpd')->store('lkpd', 'public');
            }

            $aktivitas->update($data);

            // Perbarui komponen blok
            $aktivitas->blocks()->delete();
            if ($r->has('blocks') && is_array($r->blocks)) {
                foreach ($r->blocks as $index => $blockData) {
                    $aktivitas->blocks()->create([
                        'tahap' => $blockData['tahap'] ?? 'Eksplorasi',
                        'tipe' => $blockData['tipe'] ?? 'text',
                        'judul' => $blockData['judul'] ?? null,
                        'urutan' => $index,
                        'konfigurasi' => $blockData['konfigurasi'] ?? [],
                    ]);
                }
            }
        });

        $aktivitas->refresh();
        if ($aktivitas->status === 'published' && !$wasPublished && $aktivitas->kelas_id) {
            $notifications->notifyStudentsInClass((int) $aktivitas->kelas_id, new LearningNotification(
                type: 'activity',
                title: 'Aktivitas & LKPD Baru',
                message: 'Guru menerbitkan aktivitas baru: ' . $aktivitas->judul,
                url: route('siswa.aktivitas.show', $aktivitas),
                eventKey: 'activity.published:' . $aktivitas->id . ':' . $aktivitas->published_at->timestamp
            ));
        }

        if ($aktivitas->status === 'published' && !$wasPublished && !$aktivitas->kelas_id) {
            $notifications->notifyAllStudents(new LearningNotification(
                type: 'activity',
                title: 'Aktivitas & LKPD Baru',
                message: 'Aktivitas baru tersedia: ' . $aktivitas->judul,
                url: route('siswa.aktivitas.show', $aktivitas),
                eventKey: 'activity.published:' . $aktivitas->id . ':' . $aktivitas->published_at->timestamp
            ));
        }

        $route = $isAdmin ? 'admin.aktivitas.index' : 'guru.aktivitas.index';

        return redirect()->route($route)->with('status', 'Aktivitas berhasil diperbarui.');
    }

    public function destroy(Aktivitas $aktivitas)
    {
        if (Auth::user()->role !== 'admin' && $aktivitas->guru_id !== Auth::id()) {
            abort(403);
        }

        if ($aktivitas->lkpd_path && Storage::disk('public')->exists($aktivitas->lkpd_path)) {
            Storage::disk('public')->delete($aktivitas->lkpd_path);
        }

        $aktivitas->delete();
        return back()->with('status', 'Aktivitas berhasil dihapus.');
    }

    public function downloadLkpd(Aktivitas $aktivitas)
    {
        abort_unless(Auth::user()->role === 'admin' || (int) $aktivitas->guru_id === (int) Auth::id(), 403);

        if (!$aktivitas->lkpd_path || !Storage::disk('public')->exists($aktivitas->lkpd_path)) {
            abort(404, 'File LKPD tidak ditemukan.');
        }
        return Storage::disk('public')->download($aktivitas->lkpd_path);
    }

    public function submissions(Aktivitas $aktivitas)
    {
        if (Auth::user()->role !== 'admin' && $aktivitas->guru_id !== Auth::id()) {
            abort(403);
        }

        $subs = $aktivitas->submissions()->with('siswa')->latest()->get();
        return view('guru-aktivitas-submissions', compact('aktivitas', 'subs'));
    }
}