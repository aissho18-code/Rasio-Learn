<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Portal Siswa - Ratio Learn' }}</title>

    <!-- Google Font: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { font-family: 'Poppins', sans-serif; -webkit-font-smoothing: antialiased; }
        [x-cloak] { display: none !important; }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const pathname = window.location.pathname;
            const listRoutes = [
                /^\/siswa\/aktivitas$/,
                /^\/siswa\/materi$/,
                /^\/siswa\/tugas$/,
                /^\/siswa\/ujian$/,
                /^\/siswa\/pengumuman$/,
                /^\/siswa\/evaluasi$/
            ];

            if (!listRoutes.some((pattern) => pattern.test(pathname))) {
                return;
            }

            const currentMain = document.querySelector('main');
            if (!currentMain) {
                return;
            }

            const refreshContent = () => {
                if (document.visibilityState !== 'visible') {
                    return;
                }

                const url = new URL(window.location.href);
                url.searchParams.set('_poll', '1');

                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    }
                })
                .then((response) => response.text())
                .then((html) => {
                    const parser = new DOMParser();
                    const nextDocument = parser.parseFromString(html, 'text/html');
                    const nextMain = nextDocument.querySelector('main');

                    if (nextMain && currentMain) {
                        currentMain.innerHTML = nextMain.innerHTML;
                    }
                })
                .catch(() => {
                    // silent fail, keep current page stable
                });
            };

            refreshContent();
            setInterval(refreshContent, 15000);
        });
    </script>
</head>
<body class="bg-[#F0F5FE] text-slate-800 h-screen w-screen overflow-hidden flex select-none">

    <div class="flex h-full w-full">
        <!-- SIDEBAR SISWA PRESISI -->
        @include('layouts.sidebar-siswa')

        <!-- MAIN CONTENT AREA -->
        <main class="flex-1 flex flex-col h-full overflow-y-auto">
            
            <!-- HEADER TOP BAR VERSI TERBARU -->
            <header class="px-8 py-4 flex items-center justify-end gap-5">
                {{-- KOMPONEN LONCENG NOTIFIKASI DINAMIS --}}
                <x-notification-bell />

                {{-- PROFIL SISWA DENGAN INFORMASI KELAS --}}
                <a href="{{ route('profile.show') }}" class="flex items-center gap-3 bg-white px-3.5 py-1.5 rounded-full shadow-xs border border-slate-100 hover:border-blue-300 transition">
                    @if (auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" class="w-8 h-8 rounded-full object-cover">
                    @else
                        <div class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs">
                            {{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 1)) }}
                        </div>
                    @endif

                    <div class="text-left leading-tight pr-1">
                        <div class="text-xs font-bold text-slate-800">{{ auth()->user()->name ?? 'Siswa' }}</div>
                        <div class="text-[10px] text-slate-400 font-medium">
                            Siswa @if (optional(optional(auth()->user()->siswaProfile)->kelas)->nama_kelas) • Kelas {{ optional(optional(auth()->user()->siswaProfile)->kelas)->nama_kelas }} @endif
                        </div>
                    </div>
                </a>
            </header>

            <!-- KONTEN HALAMAN -->
            <div class="px-8 pb-8 space-y-6 flex-1 max-w-7xl">
                @yield('content')
            </div>
        </main>
    </div>

</body>
</html>