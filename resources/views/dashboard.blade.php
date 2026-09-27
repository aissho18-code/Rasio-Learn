<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Dashboard - Ratio Learn</title>

    <!-- Google Font: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { font-family: 'Poppins', sans-serif; -webkit-font-smoothing: antialiased; }
    </style>
</head>
<body class="bg-[#F0F5FE] text-slate-800 h-screen w-screen overflow-hidden flex select-none">

    @php
        $role = auth()->user()->role ?? 'siswa';
    @endphp

    {{-- SIDEBAR DINAMIS BERDASARKAN ROLE (ADMIN, GURU, ATAU SISWA) --}}
    @if($role === 'admin')
        <!-- SIDEBAR ADMIN -->
        <aside class="w-64 bg-[#0F1A34] text-white flex flex-col justify-between shrink-0 h-full relative z-20 border-r border-slate-800/50">
            <div class="flex flex-col h-full overflow-y-auto">
                <div class="p-6 flex flex-col items-center border-b border-slate-800/60">
                    <img src="{{ asset('images/design-login.png') }}" alt="Ratio Learn Logo" class="h-12 w-auto object-contain">
                    <span class="text-[10px] text-blue-300 font-medium tracking-wide mt-1.5">Portal Admin</span>
                </div>
                <nav class="px-4 py-6 space-y-1.5 flex-1">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('dashboard') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                        <span class="text-base">🏠</span><span>Dashboard</span>
                    </a>
                    <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('admin.users*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                        <span class="text-base">👥</span><span>Kelola Pengguna</span>
                    </a>
                    <a href="{{ route('diskusi.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('diskusi*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                        <span class="text-base">💬</span><span>Forum Diskusi</span>
                    </a>
                </nav>
                <div class="p-4 border-t border-slate-800/60">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-slate-400 hover:text-red-400 hover:bg-red-500/10 text-xs font-semibold transition">
                            <span class="text-base">↪</span><span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>
    @elseif($role === 'guru')
        <!-- SIDEBAR GURU -->
        <aside class="w-64 bg-[#0F1A34] text-white flex flex-col justify-between shrink-0 h-full relative z-20 border-r border-slate-800/50">
            <div class="flex flex-col h-full overflow-y-auto">
                <div class="p-6 flex flex-col items-center border-b border-slate-800/60">
                    <img src="{{ asset('images/design-login.png') }}" alt="Ratio Learn Logo" class="h-12 w-auto object-contain">
                    <span class="text-[10px] text-blue-300 font-medium tracking-wide mt-1.5">Portal Guru</span>
                </div>
                <nav class="px-4 py-6 space-y-1.5 flex-1">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('dashboard') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                        <span class="text-base">🏠</span><span>Dashboard</span>
                    </a>
                    <a href="{{ route('guru.materi.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('guru.materi*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                        <span class="text-base">📖</span><span>Kelola Materi</span>
                    </a>
                    <a href="{{ route('guru.tugas.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('guru.tugas*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                        <span class="text-base">📋</span><span>Kelola Tugas</span>
                    </a>
                    <a href="{{ route('guru.ujian.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('guru.ujian*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                        <span class="text-base">📝</span><span>Kelola Ujian/Kuis</span>
                    </a>
                    <a href="{{ route('guru.penilaian.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('guru.penilaian*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                        <span class="text-base">📊</span><span>Penilaian & AI</span>
                    </a>
                    <a href="{{ route('guru.presensi.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('guru.presensi*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                        <span class="text-base">📅</span><span>Presensi Siswa</span>
                    </a>
                    <a href="{{ route('diskusi.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('diskusi*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                        <span class="text-base">💬</span><span>Forum Diskusi</span>
                    </a>
                </nav>
                <div class="p-4 border-t border-slate-800/60">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-slate-400 hover:text-red-400 hover:bg-red-500/10 text-xs font-semibold transition">
                            <span class="text-base">↪</span><span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>
    @else
        <!-- SIDEBAR SISWA -->
        <aside class="w-64 bg-[#0F1A34] text-white flex flex-col justify-between shrink-0 h-full relative z-20 border-r border-slate-800/50">
            <div class="flex flex-col h-full overflow-y-auto">
                <div class="p-6 flex flex-col items-center border-b border-slate-800/60">
                    <img src="{{ asset('images/design-login.png') }}" alt="Ratio Learn Logo" class="h-12 w-auto object-contain">
                    <span class="text-[10px] text-blue-300 font-medium tracking-wide mt-1.5">Belajar Rasio Jadi Seru!</span>
                </div>
                <nav class="px-4 py-6 space-y-1.5 flex-1">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('dashboard') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                        <span class="text-base">🏠</span><span>Dashboard</span>
                    </a>
                    <a href="{{ route('siswa.materi.index', ['type' => 'aktivitas']) }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('siswa.materi*') && request('type') == 'aktivitas' ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                        <span class="text-base">🎮</span><span>Aktivitas</span>
                    </a>
                    <a href="{{ route('siswa.absensi') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('siswa.absensi*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                        <span class="text-base">📅</span><span>Presensi</span>
                    </a>
                    <a href="{{ route('siswa.materi.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('siswa.materi*') && request('type') != 'aktivitas' ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                        <span class="text-base">📖</span><span>Materi</span>
                    </a>
                    <a href="{{ route('diskusi.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('diskusi*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                        <span class="text-base">💬</span><span>Forum Diskusi</span>
                    </a>
                    <a href="{{ route('siswa.tugas.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('siswa.tugas*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                        <span class="text-base">📋</span><span>Tugas</span>
                    </a>
                    <a href="{{ route('siswa.ujian.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('siswa.ujian*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                        <span class="text-base">📝</span><span>Ujian/Quiz</span>
                    </a>
                    <a href="{{ route('siswa.evaluasi') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('siswa.evaluasi*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                        <span class="text-base">📊</span><span>Evaluasi</span>
                    </a>
                </nav>
                <div class="px-6 py-4 opacity-20 text-[10px] text-blue-200 font-mono space-y-1 pointer-events-none">
                    <div>a : b = c : d</div>
                    <div class="text-right">2 : 3</div>
                    <div class="text-center">4 : 6</div>
                </div>
                <div class="p-4 border-t border-slate-800/60">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-slate-400 hover:text-red-400 hover:bg-red-500/10 text-xs font-semibold transition">
                            <span class="text-base">↪</span><span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>
    @endif

    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col h-full overflow-y-auto">
        
        <!-- HEADER TOP BAR -->
        <header class="px-8 py-4 flex items-center justify-end gap-5">
            <x-notification-bell />

            <div class="flex items-center gap-3 bg-white px-3.5 py-1.5 rounded-full shadow-xs border border-slate-100">
                <div class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs">👤</div>
                <div class="text-left leading-tight pr-1">
                    <div class="text-xs font-bold text-slate-800">{{ Auth::user()->name ?? 'Pengguna' }}</div>
                    <div class="text-[10px] text-slate-400 font-medium capitalize">{{ Auth::user()->role ?? 'Siswa' }}</div>
                </div>
            </div>
        </header>

        <!-- DASHBOARD CONTAINER -->
        <div class="px-8 pb-8 space-y-6 flex-1 max-w-7xl">
            
            <!-- PAGE TITLE HEADER -->
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Dashboard Pembelajaran</h1>
                <p class="text-xs text-slate-500 mt-0.5">Selamat datang kembali, <span class="font-bold text-slate-700">{{ Auth::user()->name ?? 'Pengguna' }}</span>! Anda berhasil masuk ke sistem Ratio Learn.</p>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-xs font-medium shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- WELCOME BANNER / KARTU UTAMA -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl shadow-sm p-8 text-white flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="space-y-2">
                    <span class="bg-blue-500/50 text-blue-100 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">Portal Rasio & Proporsi</span>
                    <h2 class="text-xl font-extrabold">Eksplorasi Matematika Jadi Lebih Menyenangkan!</h2>
                    <p class="text-xs text-blue-100 leading-relaxed max-w-xl">
                        Aplikasi pembelajaran interaktif berbasis kecerdasan buatan (AI) untuk membantu memahami materi rasio, perbandingan, tugas, ujian, dan evaluasi berkala.
                    </p>
                </div>
                <div class="bg-white/10 backdrop-blur-md p-6 rounded-xl border border-white/20 text-center shrink-0">
                    <div class="text-2xl font-black">Ratio Learn</div>
                    <div class="text-[10px] text-blue-200 mt-1">Platform Edukasi Modern v2.0</div>
                </div>
            </div>

            <!-- QUICK INFO CARDS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-2xl">📚</span>
                        <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full">Aktif</span>
                    </div>
                    <h4 class="font-bold text-gray-800 text-sm">Materi & Aktivitas</h4>
                    <p class="text-xs text-gray-500">Akses modul pembelajaran dan latihan interaktif setiap pekan.</p>
                </div>

                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-2xl">📋</span>
                        <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full">Tugas</span>
                    </div>
                    <h4 class="font-bold text-gray-800 text-sm">Pengumpulan Tugas</h4>
                    <p class="text-xs text-gray-500">Unggah berkas tugas harian dan dapatkan umpan balik evaluasi.</p>
                </div>

                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-2xl">🤖</span>
                        <span class="text-[10px] font-bold text-purple-600 bg-purple-50 px-2.5 py-1 rounded-full">AI Powered</span>
                    </div>
                    <h4 class="font-bold text-gray-800 text-sm">Penilaian & Evaluasi AI</h4>
                    <p class="text-xs text-gray-500">Analisis otomatis ujian dan rekomendasi belajar cerdas dari asisten AI.</p>
                </div>

            </div>

        </div>
    </main>

</body>
</html>