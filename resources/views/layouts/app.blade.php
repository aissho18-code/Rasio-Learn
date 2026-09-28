<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Ratio Learn') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- TAILWIND CSS CDN -->
        <script src="https://cdn.tailwindcss.com"></script>

        <!-- ALPINE.JS CDN -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <style>
            [x-cloak] { display: none !important; }
            /* Scrollbar Halus Khusus Area Konten Utama & Sidebar */
            .main-scroll::-webkit-scrollbar {
                width: 5px;
            }
            .main-scroll::-webkit-scrollbar-thumb {
                background-color: #334155;
                border-radius: 6px;
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-[#f4f7fe] text-slate-800 h-full overflow-hidden">
        
        <!-- KONTAINER UTAMA TERKUNCI SEUKURAN LAYAR -->
        <div class="h-screen w-screen flex overflow-hidden bg-[#f4f7fe]">
            
            <!-- SIDEBAR KIRI (THEME DARK NAVY PERSIS GAMBAR DASHBOARD) -->
            <aside class="w-64 bg-[#0a1128] border-r border-slate-800/80 hidden md:flex flex-col shrink-0 h-full overflow-hidden select-none text-white justify-between">
                
                <div class="flex flex-col flex-1 min-h-0">
                    <!-- Logo & Subtitle Portal -->
                    <div class="pt-7 pb-6 px-6 flex flex-col items-center justify-center shrink-0">
                        <div class="flex items-center gap-2.5">
                            <div class="bg-blue-600 p-2 rounded-xl text-white font-black text-lg shadow-lg shadow-blue-500/30 flex items-center justify-center">
                                📐
                            </div>
                            <span class="text-lg font-black tracking-wider text-blue-400 uppercase">RATIO LEARN</span>
                        </div>
                        <span class="text-xs text-slate-400 font-semibold mt-2 tracking-wide">
                            {{ auth()->user()->role === 'guru' ? 'Portal Guru' : 'Portal Siswa' }}
                        </span>
                    </div>

                    <!-- DAFTAR NAVIGASI SIDEBAR GURU / SISWA -->
                    <nav class="px-4 space-y-1.5 text-xs font-semibold overflow-y-auto flex-1 main-scroll">
                        @php
                            $isGuru = auth()->user()->role === 'guru';
                        @endphp

                        <!-- 1. Dashboard -->
                        <a href="{{ route('dashboard') }}" 
                           class="flex items-center gap-3.5 px-4 py-3 rounded-2xl transition {{ request()->routeIs('dashboard') ? 'bg-[#eef4ff] text-[#2563eb] font-bold shadow-sm' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            <span class="text-base">🏫</span>
                            <span>Dashboard</span>
                        </a>

                        <!-- 2. Kelola LKPD / Aktivitas & LKPD -->
                        @if($isGuru)
                            <a href="{{ route('guru.lkpd.index') }}" 
                               class="flex items-center gap-3.5 px-4 py-3 rounded-2xl transition {{ request()->routeIs('guru.lkpd.*') ? 'bg-[#eef4ff] text-[#2563eb] font-bold shadow-sm' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                                <span class="text-base">📋</span>
                                <span>Kelola LKPD</span>
                            </a>
                        @else
                            <a href="{{ route('siswa.lkpd.index') }}" 
                               class="flex items-center gap-3.5 px-4 py-3 rounded-2xl transition {{ request()->routeIs('siswa.lkpd.*') ? 'bg-[#eef4ff] text-[#2563eb] font-bold shadow-sm' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                                <span class="text-base">📋</span>
                                <span>Aktivitas & LKPD</span>
                            </a>
                        @endif

                        <!-- 3. Kelola Materi -->
                        <a href="{{ $isGuru ? route('guru.materi.index') : route('siswa.materi.index') }}" 
                           class="flex items-center gap-3.5 px-4 py-3 rounded-2xl transition {{ request()->routeIs('guru.materi.*') || request()->routeIs('siswa.materi.*') ? 'bg-[#eef4ff] text-[#2563eb] font-bold shadow-sm' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            <span class="text-base">📖</span>
                            <span>{{ $isGuru ? 'Kelola Materi' : 'Materi' }}</span>
                        </a>

                        <!-- 4. Kelola Tugas -->
                        <a href="{{ $isGuru ? route('guru.tugas.index') : route('siswa.tugas.index') }}" 
                           class="flex items-center gap-3.5 px-4 py-3 rounded-2xl transition {{ request()->routeIs('guru.tugas.*') || request()->routeIs('siswa.tugas.*') ? 'bg-[#eef4ff] text-[#2563eb] font-bold shadow-sm' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            <span class="text-base">📝</span>
                            <span>{{ $isGuru ? 'Kelola Tugas' : 'Tugas' }}</span>
                        </a>

                        <!-- 5. Kelola Ujian/Kuis -->
                        <a href="{{ $isGuru ? route('guru.ujian.index') : route('siswa.ujian.index') }}" 
                           class="flex items-center gap-3.5 px-4 py-3 rounded-2xl transition {{ request()->routeIs('guru.ujian.*') || request()->routeIs('siswa.ujian.*') ? 'bg-[#eef4ff] text-[#2563eb] font-bold shadow-sm' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            <span class="text-base">✏️</span>
                            <span>{{ $isGuru ? 'Kelola Ujian/Kuis' : 'Ujian / Quiz' }}</span>
                        </a>

                        <!-- 6. Penilaian & Evaluasi -->
                        <a href="{{ $isGuru ? route('guru.penilaian.index') : route('siswa.evaluasi') }}" 
                           class="flex items-center gap-3.5 px-4 py-3 rounded-2xl transition {{ request()->routeIs('guru.penilaian.*') || request()->routeIs('siswa.evaluasi') ? 'bg-[#eef4ff] text-[#2563eb] font-bold shadow-sm' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            <span class="text-base">📊</span>
                            <span>{{ $isGuru ? 'Penilaian & Evaluasi' : 'Evaluasi Siswa' }}</span>
                        </a>

                        <!-- 7. Rekap Presensi Siswa -->
                        <a href="{{ $isGuru ? route('guru.presensi.index') : route('siswa.absensi') }}" 
                           class="flex items-center gap-3.5 px-4 py-3 rounded-2xl transition {{ request()->routeIs('guru.presensi.*') || request()->routeIs('siswa.absensi') ? 'bg-[#eef4ff] text-[#2563eb] font-bold shadow-sm' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            <span class="text-base">🗓️</span>
                            <span>{{ $isGuru ? 'Rekap Presensi Siswa' : 'Presensi' }}</span>
                        </a>

                        <!-- 8. Forum Diskusi -->
                        <a href="{{ route('diskusi.index') }}" 
                           class="flex items-center gap-3.5 px-4 py-3 rounded-2xl transition {{ request()->routeIs('diskusi.*') ? 'bg-[#eef4ff] text-[#2563eb] font-bold shadow-sm' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            <span class="text-base">💬</span>
                            <span>Forum Diskusi</span>
                        </a>

                        <!-- 9. Kelola Refleksi -->
                        @if(Route::has('guru.refleksi.index'))
                            <a href="{{ $isGuru ? route('guru.refleksi.index') : route('siswa.refleksi.index') }}" 
                               class="flex items-center gap-3.5 px-4 py-3 rounded-2xl transition {{ request()->routeIs('guru.refleksi.*') || request()->routeIs('siswa.refleksi.*') ? 'bg-[#eef4ff] text-[#2563eb] font-bold shadow-sm' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                                <span class="text-base">💭</span>
                                <span>{{ $isGuru ? 'Kelola Refleksi' : 'Refleksi Siswa' }}</span>
                            </a>
                        @endif
                    </nav>
                </div>

                <!-- Tombol Logout Dibawah Sidebar -->
                <div class="p-4 border-t border-slate-800/80 shrink-0">
                    <form method="POST" action="/logout">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-slate-300 hover:text-red-400 hover:bg-slate-800/50 rounded-xl transition">
                            <span class="text-sm">↪</span>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>

            </aside>

            <!-- KONTEN UTAMA DI KANAN -->
            <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden bg-[#f4f7fe]">
                <!-- Header Atas (Pengumuman, Notifikasi, Avatar User) -->
                <header class="h-16 bg-transparent px-8 flex items-center justify-between shrink-0">
                    <div>
                        @isset($header)
                            {{ $header }}
                        @endisset
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Lonceng Notifikasi -->
                        <x-notification-bell />

                        <!-- User Profile Badge -->
                        <div class="flex items-center gap-3 bg-white px-4 py-1.5 rounded-full shadow-sm border border-slate-100">
                            <div class="w-8 h-8 rounded-full bg-slate-900 text-white font-bold flex items-center justify-center text-xs">
                                {{ strtoupper(substr(auth()->user()->name ?? 'G', 0, 1)) }}
                            </div>
                            <div class="text-left pr-1">
                                <div class="text-xs font-extrabold text-slate-800">{{ auth()->user()->name ?? 'User' }}</div>
                                <div class="text-[10px] font-semibold text-slate-400 capitalize">{{ auth()->user()->role ?? 'Guru' }}</div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- AREA KONTEN UTAMA -->
                <main class="flex-1 overflow-y-auto p-8 main-scroll">
                    <div class="max-w-7xl mx-auto">
                        {!! $slot ?? $__env->yieldContent('content') !!}
                    </div>
                </main>
            </div>

        </div>
    </body>
</html>