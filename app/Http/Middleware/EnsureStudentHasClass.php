<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureStudentHasClass
{
    /**
     * Jika siswa tidak punya kelas, arahkan kembali ke dashboard dengan pesan flash.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        $kelasId = optional($user?->siswaProfile)->kelas_id;

        if (empty($kelasId)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Anda belum tergabung ke kelas. Mintalah guru/admin untuk menambahkan Anda.'
                ], 403);
            }

            return redirect()->route('dashboard')
                ->with('no_kelas', 'Anda masih belum masuk ke dalam kelas. Mintalah guru/admin untuk memasukkan ke dalam kelas anda.');
        }

        return $next($request);
    }
}