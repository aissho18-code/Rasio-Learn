<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Pemeriksaan Tugas Siswa - Ratio Learn</title>

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

    <!-- SIDEBAR GURU -->
    <aside class="w-64 bg-[#0F1A34] text-white flex flex-col justify-between shrink-0 h-full relative z-20 border-r border-slate-800/50">
        <div class="flex flex-col h-full overflow-y-auto">
            
            <!-- LOGO HEADER -->
            <div class="p-6 flex flex-col items-center border-b border-slate-800/60">
                <img src="{{ asset('images/design-login.png') }}" alt="Ratio Learn Logo" class="h-12 w-auto object-contain">
                <span class="text-[10px] text-blue-300 font-medium tracking-wide mt-1.5">Portal Guru</span>
            </div>

            <!-- MENU SIDEBAR GURU -->
            <nav class="px-4 py-6 space-y-1.5 flex-1">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('guru.dashboard') || request()->routeIs('dashboard') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">🏠</span><span>Dashboard</span>
                </a>
                
                <!-- KELOLA MATERI (ACTIVE) -->
                <a href="{{ route('guru.aktivitas.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('guru.aktivitas*') ? 'bg-white text-slate-900 shadow-sm font-bold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                    <span class="text-base">📋</span>
                        <span>Aktivitas Siswa</span>
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
                    <span class="text-base">📊</span><span>Penilaian & Evaluasi</span>
                </a>

                <a href="{{ route('guru.presensi.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('guru.presensi*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">📅</span><span>Rekap Presensi Siswa</span>
                </a>

                <a href="{{ route('diskusi.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('diskusi*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">💬</span><span>Forum Diskusi</span>
                </a>

                <a href="{{ route('guru.refleksi.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('guru.refleksi*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">💭</span><span>Kelola Refleksi</span>
                </a>
            </nav>

            <!-- DOODLE -->
            <div class="px-6 py-4 opacity-20 text-[10px] text-blue-200 font-mono space-y-1 pointer-events-none">
                <div>a : b = c : d</div>
                <div class="text-right">2 : 3</div>
                <div class="text-center">4 : 6</div>
            </div>

            <!-- LOGOUT -->
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

    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col h-full overflow-y-auto">
        
        <!-- HEADER TOP BAR -->
        <header class="px-8 py-4 flex items-center justify-end gap-5">
            <x-notification-bell />

            <a href="{{ route('profile.show') }}" class="flex items-center gap-3 bg-white px-3.5 py-1.5 rounded-full shadow-xs border border-slate-100 hover:border-blue-300 transition">
                <div class="w-8 h-8 rounded-full overflow-hidden bg-slate-900 text-white flex items-center justify-center font-bold text-xs shrink-0">
                    @if(auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Foto Profil" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr(auth()->user()->name ?? 'G', 0, 1)) }}
                    @endif
                </div>
                <div class="text-left leading-tight pr-1">
                    <div class="text-xs font-bold text-slate-800">{{ Auth::user()->name ?? 'Guru Pengajar' }}</div>
                    <div class="text-[10px] text-slate-400 font-medium capitalize">{{ Auth::user()->role ?? 'Guru' }}</div>
                </div>
            </a>
        </header>

        <!-- DASHBOARD CONTAINER -->
        <div class="px-8 pb-8 space-y-6 flex-1 max-w-6xl">
            
            <!-- PAGE TITLE HEADER -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-extrabold text-slate-900">Pemeriksaan Tugas Siswa</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Tinjau pengumpulan berkas/jawaban siswa dan berikan umpan balik evaluasi.</p>
                </div>
                <a href="{{ route('guru.penilaian.index') }}" class="text-xs font-bold text-blue-600 hover:underline">
                    &larr; Kembali ke Daftar Penilaian
                </a>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-xs font-medium shadow-xs">
                    {{ session('success') }}
                </div>
            @endif

            <!-- KARTU INFORMASI PENGUMPULAN SISWA -->
            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 space-y-4">
                <div class="flex flex-wrap items-center justify-between border-b border-gray-100 pb-4 gap-2">
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg">{{ $sub->tugas->judul ?? 'Tugas Harian' }}</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Siswa: <strong class="text-slate-800">{{ $sub->siswa->name ?? 'Siswa' }}</strong> | Dikumpul: {{ $sub->updated_at ? $sub->updated_at->format('d F Y, H:i WIB') : '-' }}</p>
                    </div>
                    
                    <!-- TOMBOL EVALUASI OTOMATIS DENGAN AI -->
                    <form action="{{ route('guru.penilaian.tugas.ai', $sub->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-sm transition flex items-center space-x-1.5">
                            <span>🤖 Generate Nilai & Umpan Balik AI</span>
                        </button>
                    </form>
                </div>

                <!-- TAMPILAN BERKAS & ONLINE TEXT DARI SISWA -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 space-y-2">
                        <p class="font-bold text-gray-700">📄 Berkas Tugas Diunggah:</p>
                        @if($sub->file_path)
                            <a href="{{ asset('storage/' . $sub->file_path) }}" target="_blank" class="text-blue-600 font-bold hover:underline inline-flex items-center space-x-1">
                                <span>📎 {{ basename($sub->file_path) }} (Unduh / Buka Berkas)</span>
                            </a>
                        @else
                            <p class="text-gray-400 italic">Siswa tidak mengunggah berkas.</p>
                        @endif
                    </div>

                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 space-y-2">
                        <p class="font-bold text-gray-700">📝 Catatan / Online Text Jawaban:</p>
                        <p class="text-gray-600 leading-relaxed">{{ $sub->jawaban ?? 'Tidak ada catatan teks.' }}</p>
                    </div>
                </div>
            </div>

            <!-- BLOK SECTION: NILAI & UMPAN BALIK AI -->
            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
                    
                    <!-- KOLOM KIRI: NILAI & UMPAN BALIK AI -->
                    <div class="space-y-3">
                        <h4 class="font-bold text-gray-500 uppercase text-[11px] tracking-wider border-b border-gray-100 pb-2">
                            NILAI & UMPAN BALIK AI
                        </h4>

                        @if($sub->ai_score !== null || $sub->ai_feedback)
                            <div class="space-y-1.5">
                                <p class="font-bold text-indigo-600 text-sm">
                                    Skor AI: {{ $sub->ai_score ?? '-' }}
                                </p>
                                <p class="text-xs text-gray-500 italic leading-relaxed">
                                    {{ $sub->ai_feedback ?? 'Belum ada umpan balik dari AI.' }}
                                </p>
                            </div>
                        @else
                            <p class="text-xs text-gray-400 italic">
                                Klik tombol "Generate Nilai & Umpan Balik AI" di atas untuk menganalisis jawaban siswa secara otomatis.
                            </p>
                        @endif
                    </div>

                    <!-- KOLOM KANAN: AKSI / REVISI GURU -->
                    <div class="space-y-3">
                        <h4 class="font-bold text-gray-500 uppercase text-[11px] tracking-wider border-b border-gray-100 pb-2">
                            AKSI / REVISI GURU
                        </h4>

                        <form action="{{ route('guru.penilaian.tugas.store', $sub->id) }}" method="POST" class="space-y-3">
                            @csrf

                            <div class="flex items-center space-x-3">
                                <input type="number" min="0" max="100" name="nilai" value="{{ old('nilai', $sub->nilai ?? $sub->ai_score ?? '') }}" required placeholder="0-100" class="w-24 text-sm font-bold border border-gray-200 rounded-xl p-2.5 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <span class="text-xs font-bold text-gray-600">
                                    Status: 
                                    @if($sub->nilai !== null || $sub->status === 'dinilai')
                                        <span class="text-green-600 font-extrabold">DINILAI</span>
                                    @else
                                        <span class="text-amber-600 font-extrabold">BELUM DINILAI</span>
                                    @endif
                                </span>
                            </div>

                            <div>
                                <textarea name="catatan_guru" rows="3" required placeholder="Catatan evaluasi & umpan balik untuk siswa..." class="w-full text-xs border border-gray-200 rounded-xl p-3 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none leading-relaxed">{{ old('catatan_guru', $sub->catatan_guru ?? $sub->ai_feedback ?? '') }}</textarea>
                            </div>

                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-6 py-2.5 rounded-xl shadow-sm transition">
                                Simpan Penilaian Final 🚀
                            </button>
                        </form>
                    </div>

                </div>
            </div>

        </div>
    </main>

</body>
</html>