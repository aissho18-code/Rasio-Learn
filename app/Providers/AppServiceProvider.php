<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Memaksa protokol HTTPS saat di lingkungan produksi, Cloudflare Tunnel, atau jika APP_URL menggunakan HTTPS
        if (
            app()->environment('production') ||
            str_contains(config('app.url'), 'https://') ||
            request()->header('X-Forwarded-Proto') === 'https' ||
            str_contains(request()->header('Host', ''), 'trycloudflare.com')
        ) {
            URL::forceScheme('https');
            request()->server->set('HTTPS', 'on');
        }

        // 2. View composer untuk menyediakan variabel nama kelas siswa di seluruh view
        View::composer('*', function ($view) {
            $user = auth()->user();
            $kelasName = null;

            if ($user && ($user->role === 'siswa' || (method_exists($user, 'hasRole') && $user->hasRole('siswa')))) {
                $kelasName = optional(optional($user->siswaProfile)->kelas)->nama_kelas;
            }

            $view->with('currentUserKelasName', $kelasName);
        });
    }
}