<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Detail Evaluasi & Hasil Ujian - Ratio Learn</title>

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

    <!-- SIDEBAR SISWA -->
    <aside class="w-64 bg-[#0F1A34] text-white flex flex-col justify-between shrink-0 h-full relative z-20 border-r border-slate-800/50">
        <div class="flex flex-col h-full overflow-y-auto">
            
            <!-- LOGO HEADER -->
            <div class="p-6 flex flex-col items-center border-b border-slate-800/60">
                <img src="{{ asset('images/design-login.png') }}" alt="Ratio Learn Logo" class="h-12 w-auto object-contain">
                <span class="text-[10px] text-blue-300 font-medium tracking-wide mt-1.5">Belajar Rasio Jadi Seru!</span>
            </div>

            <!-- MENU SIDEBAR SISWA -->
            <nav class="px-4 py-6 space-y-1.5 flex-1">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('dashboard') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">🏠</span><span>Dashboard</span>
                </a>
                
                <a href="{{ route('siswa.aktivitas.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-semibold transition-all duration-200 {{ request()->routeIs('siswa.aktivitas*') ? 'bg-white text-slate-900 shadow-sm font-bold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                    <span class="text-base">📚</span>
                    <span>Aktivitas & LKPD</span>
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

                <!-- TAB REFLEKSI SISWA -->
                <a href="{{ route('siswa.refleksi.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('siswa.refleksi*') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">💭</span><span>Refleksi</span>
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
                    <div class="text-xs font-bold text-slate-800">{{ Auth::user()->name ?? 'Siti Aisyah' }}</div>
                    <div class="text-[10px] text-slate-400 font-medium">Siswa Kelas VII</div>
                </div>
            </div>
        </header>

        <!-- DASHBOARD CONTAINER -->
        <div class="px-8 pb-8 space-y-6 flex-1 max-w-4xl mx-auto w-full">
            
            <!-- PAGE TITLE HEADER -->
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Detail Evaluasi & Hasil Ujian</h1>
                <p class="text-xs text-slate-500 mt-0.5">Tinjau rekapitulasi jawaban benar/salah dan rekomendasi belajar Anda.</p>
            </div>

            <!-- KARTU RINGKASAN NILAI -->
            <div class="bg-white rounded-2xl border-t-8 border-t-blue-600 border border-gray-200 shadow-sm p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <a href="{{ route('siswa.evaluasi') }}" class="text-xs font-bold text-blue-600 hover:underline mb-2 block">&larr; Kembali ke Daftar Evaluasi</a>
                    <h3 class="font-bold text-gray-800 text-base">Ujian: {{ $sub->ujian->judul_ujian }}</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Tanggal Pengerjaan: {{ $sub->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div class="bg-blue-50 border border-blue-100 px-5 py-3 rounded-2xl text-center">
                    <span class="text-[11px] font-semibold text-blue-600 block">Nilai Akhir Anda</span>
                    <span class="text-2xl font-bold text-blue-700">{{ $sub->nilai }} / 100</span>
                </div>
            </div>

            <!-- REKAPITULASI JAWABAN (BENAR / SALAH & KUNCI JAWABAN) -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-6">
                <h4 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-3">Rincian Koreksi Jawaban</h4>

                <div class="space-y-4">
                    @foreach($sub->ujian->questions ?? [] as $index => $q)
                        @php
                            $studentAns = trim(strtolower((string)($sub->jawaban[$index] ?? '')));
                            $correctAns = trim(strtolower((string)($q['answer'] ?? '')));
                            $isCorrect = ($studentAns !== '' && $correctAns !== '' && ($studentAns === $correctAns || str_contains($studentAns, $correctAns) || str_contains($correctAns, $studentAns)));
                        @endphp

                        <div class="p-4 rounded-xl border {{ $isCorrect ? 'bg-green-50/50 border-green-200' : 'bg-red-50/50 border-red-200' }} space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-gray-800 text-xs">{{ $index + 1 }}. {{ $q['text'] }}</span>
                                @if($isCorrect)
                                    <span class="bg-green-100 text-green-700 font-bold px-2 py-0.5 rounded text-[10px]">✔ Benar</span>
                                @else
                                    <span class="bg-red-100 text-red-700 font-bold px-2 py-0.5 rounded text-[10px]">✖ Salah</span>
                                @endif
                            </div>

                            <div class="text-xs text-gray-700">
                                💬 Jawaban Anda: <span class="font-semibold">{{ $sub->jawaban[$index] ?? 'Tidak dijawab' }}</span>
                            </div>

                            @if(!$isCorrect)
                                <div class="text-xs text-green-700 bg-white p-2.5 rounded-lg border border-green-100">
                                    🔑 Kunci Jawaban yang Benar: <span class="font-bold">{{ $q['answer'] ?? '-' }}</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- LAPORAN REKOMENDASI BELAJAR-->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-3">
                <h4 class="font-bold text-blue-800 text-sm flex items-center space-x-1">
                    <span>Laporan Rekomendasi Belajar</span>
                </h4>
                <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100 text-xs text-gray-800 leading-relaxed">
                    {{ $sub->rekomendasi_belajar ?? 'Belum ada rekomendasi khusus yang diberikan.' }}
                </div>
            </div>

        </div>
    </main>

</body>
</html>