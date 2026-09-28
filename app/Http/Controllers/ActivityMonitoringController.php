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

}