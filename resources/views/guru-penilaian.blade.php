<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Penilaian & Evaluasi Pembelajaran - Ratio Learn</title>

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
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('dashboard') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">🏠</span><span>Dashboard</span>
                </a>
                
                <!-- KELOLA MATERI (ACTIVE) -->
                <a href="{{ route('guru.lkpd.index') }}" 
                    class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('guru.lkpd.*') ? 'bg-blue-100 text-blue-700' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <span class="text-lg">📋</span>
                        <span>Kelola LKPD</span>
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

            <div class="flex items-center gap-3 bg-white px-3.5 py-1.5 rounded-full shadow-xs border border-slate-100">
                <div class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs">👤</div>
                <div class="text-left leading-tight pr-1">
                    <div class="text-xs font-bold text-slate-800">{{ Auth::user()->name ?? 'Guru Pengajar' }}</div>
                    <div class="text-[10px] text-slate-400 font-medium capitalize">{{ Auth::user()->role ?? 'Guru' }}</div>
                </div>
            </div>
        </header>

        <!-- DASHBOARD CONTAINER -->
        <div class="px-8 pb-8 space-y-8 flex-1 max-w-7xl">
            
            <!-- PAGE TITLE HEADER -->
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Penilaian & Evaluasi Pembelajaran</h1>
                <p class="text-xs text-slate-500 mt-0.5">Periksa pengumpulan tugas harian dan hasil pengerjaan ujian siswa.</p>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-xs font-medium shadow-xs">
                    {{ session('success') }}
                </div>
            @endif

            <!-- TABEL 1: HASIL PENGERJAAN UJIAN & QUIZ MASUK -->
            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-800 text-base flex items-center space-x-2">
                            <span>📋</span>
                            <span>Hasil Pengerjaan Ujian & Quiz Masuk</span>
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">Daftar siswa yang telah menyelesaikan paket soal ujian rasio.</p>
                    </div>
                    <span class="bg-blue-50 text-blue-600 font-bold text-xs px-3 py-1 rounded-full">
                        {{ count($ujianSubmissions ?? []) }} Pengajuan
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-50/50 text-gray-400 font-bold uppercase border-b border-gray-100 text-[10px] tracking-wider">
                                <th class="py-3.5 px-6">Nama Siswa</th>
                                <th class="py-3.5 px-6">Ujian / Quiz</th>
                                <th class="py-3.5 px-6 text-center">Nilai</th>
                                <th class="py-3.5 px-6 text-center">Status</th>
                                <th class="py-3.5 px-6 text-right">Aksi Pemeriksaan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @forelse($ujianSubmissions ?? [] as $us)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="py-4 px-6 font-bold text-gray-800 flex items-center space-x-2">
                                        <span class="text-gray-400">👤</span>
                                        <span>{{ $us->siswa->name ?? 'Siswa' }}</span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <p class="font-semibold text-blue-600">{{ $us->ujian->judul_ujian ?? 'Ujian' }}</p>
                                        <p class="text-[10px] text-gray-400">Dikumpul: {{ $us->created_at ? $us->created_at->format('d M Y, H:i') : '-' }}</p>
                                    </td>
                                    <td class="py-4 px-6 text-center font-bold text-sm">
                                        {{ $us->nilai !== null ? $us->nilai : '-' }}
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        @if($us->nilai !== null)
                                            <span class="bg-green-100 text-green-700 font-bold px-2.5 py-1 rounded-full text-[10px]">Dinilai</span>
                                        @else
                                            <span class="bg-amber-100 text-amber-700 font-bold px-2.5 py-1 rounded-full text-[10px]">Belum Dinilai</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <a href="{{ route('guru.penilaian.ujian.detail', $us->id) }}" class="inline-flex items-center space-x-1 bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-xl text-xs shadow-sm transition">
                                            <span>Periksa & Nilai</span>
                                            <span>🔍</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-400 italic">Belum ada pengajuan pengerjaan ujian.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TABEL 2: HASIL PENGUMPULAN TUGAS MASUK -->
            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-800 text-base flex items-center space-x-2">
                            <span>📁</span>
                            <span>Hasil Pengumpulan Tugas Masuk</span>
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">Daftar tugas harian materi rasio yang dikirimkan oleh siswa.</p>
                    </div>
                    <span class="bg-blue-50 text-blue-600 font-bold text-xs px-3 py-1 rounded-full">
                        {{ count($submissions ?? []) }} Pengajuan
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-50/50 text-gray-400 font-bold uppercase border-b border-gray-100 text-[10px] tracking-wider">
                                <th class="py-3.5 px-6">Nama Siswa</th>
                                <th class="py-3.5 px-6">Judul Tugas</th>
                                <th class="py-3.5 px-6 text-center">Nilai</th>
                                <th class="py-3.5 px-6 text-center">Status</th>
                                <th class="py-3.5 px-6 text-right">Aksi Pemeriksaan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @forelse($submissions ?? [] as $s)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="py-4 px-6 font-bold text-gray-800 flex items-center space-x-2">
                                        <span class="text-gray-400">👤</span>
                                        <span>{{ $s->siswa->name ?? 'Siswa' }}</span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <p class="font-semibold text-blue-600">{{ $s->tugas->judul ?? 'Tugas Harian' }}</p>
                                        <p class="text-[10px] text-gray-400">Dikumpul: {{ $s->updated_at ? $s->updated_at->format('d M Y, H:i') : '-' }}</p>
                                    </td>
                                    <td class="py-4 px-6 text-center font-bold text-sm">
                                        {{ $s->nilai !== null ? $s->nilai : '-' }}
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        @if($s->status === 'dinilai' || $s->nilai !== null)
                                            <span class="bg-green-100 text-green-700 font-bold px-2.5 py-1 rounded-full text-[10px]">Dinilai</span>
                                        @elseif($s->status === 'dinilai_ai')
                                            <span class="bg-purple-100 text-purple-700 font-bold px-2.5 py-1 rounded-full text-[10px]">Dinilai AI</span>
                                        @else
                                            <span class="bg-amber-100 text-amber-700 font-bold px-2.5 py-1 rounded-full text-[10px]">Belum Dinilai</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <a href="{{ route('guru.penilaian.tugas.detail', $s->id) }}" class="inline-flex items-center space-x-1 bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-xl text-xs shadow-sm transition">
                                            <span>Periksa & Nilai</span>
                                            <span>🔍</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-400 italic">Belum ada pengumpulan tugas dari siswa.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

</body>
</html>