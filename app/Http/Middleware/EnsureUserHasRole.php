<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        abort_unless($user, 401);
        abort_unless(in_array($user->role, ['admin', 'guru', 'siswa'], true), 403, 'Role akun tidak valid.');
        abort_unless(in_array($user->role, $roles, true), 403, 'Anda tidak memiliki akses ke portal ini.');

        return $next($request);
    }
}