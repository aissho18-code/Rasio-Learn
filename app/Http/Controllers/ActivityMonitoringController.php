<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityMonitoringController extends Controller
{
    public function adminStatus(): JsonResponse
    {
        $users = User::forRoles(['guru', 'siswa'])
            ->with('siswaProfile.kelas')
            ->orderBy('name')
            ->get();

        return response()->json([
            'counts' => [
                'guru' => $users->filter(fn ($user) => $user->monitoringRole() === 'guru')->count(),
                'guru_active' => $users->filter(fn ($user) => $user->monitoringRole() === 'guru' && $user->isOnline())->count(),
                'siswa' => $users->filter(fn ($user) => $user->monitoringRole() === 'siswa')->count(),
                'siswa_active' => $users->filter(fn ($user) => $user->monitoringRole() === 'siswa' && $user->isOnline())->count(),
                'total_active' => $users->filter(fn ($user) => $user->isOnline())->count(),
            ],
            'users' => $users->map(fn ($user) => [
                'id' => $user->id,
                'is_active' => $user->isOnline(),
                'last_active' => $user->lastActiveLabel(),
            ])->values(),
        ]);
    }

    public function guruStatus(Request $request): JsonResponse
    {
        $kelasList = Kelas::where('wali_kelas_id', $request->user()->id)
            ->when($request->filled('kelas_id'), function ($query) use ($request) {
                $query->whereKey($request->integer('kelas_id'));
            })
            ->with(['siswa' => fn ($query) => $query->forRoles('siswa')->orderBy('name')])
            ->orderBy('nama_kelas')
            ->get();

        if ($request->filled('kelas_id') && $kelasList->isEmpty()) {
            abort(404);
        }

        return response()->json([
            'classes' => $kelasList->map(fn ($kelas) => [
                'id' => $kelas->id,
                'total' => $kelas->siswa->count(),
                'active' => $kelas->siswa->filter(fn ($siswa) => $siswa->isOnline())->count(),
                'students' => $kelas->siswa->map(fn ($siswa) => [
                    'id' => $siswa->id,
                    'is_active' => $siswa->isOnline(),
                    'last_active' => $siswa->lastActiveLabel(),
                ])->values(),
            ])->values(),
        ]);
    }

    public function adminIndex(Request $request)
    {
        $totalSiswa = User::where('role', 'siswa')->count();
        $totalGuru = User::where('role', 'guru')->count();
        $totalKelas = Kelas::count();

        $totalMateri = class_exists(\App\Models\Materi::class)
            ? \App\Models\Materi::count()
            : 0;

        $totalLkpd = class_exists(\App\Models\Lkpd::class)
            ? \App\Models\Lkpd::count()
            : 0;

        $totalTugas = class_exists(\App\Models\Tugas::class)
            ? \App\Models\Tugas::count()
            : 0;

        $totalUjian = class_exists(\App\Models\Ujian::class)
            ? \App\Models\Ujian::count()
            : 0;

        $siswaAktif = User::where('role', 'siswa')
            ->get()
            ->filter(fn ($user) => $user->isOnline())
            ->count();

        $guruAktif = User::where('role', 'guru')
            ->get()
            ->filter(fn ($user) => $user->isOnline())
            ->count();

        return view('admin-monitoring', compact(
            'totalSiswa',
            'totalGuru',
            'totalKelas',
            'totalMateri',
            'totalLkpd',
            'totalTugas',
            'totalUjian',
            'siswaAktif',
            'guruAktif'
        ));
    }
}