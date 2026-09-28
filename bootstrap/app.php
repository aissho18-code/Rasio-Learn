<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        __DIR__.'/../app/Console/Commands',
    ])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->appendToGroup('web', \App\Http\Middleware\TrackUserActivity::class);

        // 1. Percayai proxy Cloudflare Tunnel (HTTPS) untuk mencegah error 419 Page Expired
        $middleware->trustProxies(at: '*');

        // 2. Daftarkan Alias Middleware Siswa
        $middleware->alias([
            'student.has_class' => \App\Http\Middleware\EnsureStudentHasClass::class,
            'role' => \App\Http\Middleware\EnsureUserHasRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();