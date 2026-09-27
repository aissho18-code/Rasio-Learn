<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Detail Pemeriksaan Ujian Siswa - Ratio Learn</title>

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
            <button class="relative p-2 bg-white rounded-full shadow-xs hover:bg-slate-50 transition border border-slate-100">
                <span class="text-base">🔔</span>
                <span class="absolute top-0 right-0 w-4 h-4 bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center">3</span>
            </button>

            <div class="flex items-center gap-3 bg-white px-3.5 py-1.5 rounded-full shadow-xs border border-slate-100">
                <div class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs">👤</div>
                <div class="text-left leading-tight pr-1">
                    <div class="text-xs font-bold text-slate-800">{{ Auth::user()->name ?? 'Guru Pengajar' }}</div>
                    <div class="text-[10px] text-slate-400 font-medium capitalize">{{ Auth::user()->role ?? 'Guru' }}</div>
                </div>
            </div>
        </header>

        <!-- DASHBOARD CONTAINER -->
        <div class="px-8 pb-8 space-y-6 flex-1 max-w-4xl">
            
            <!-- PAGE TITLE HEADER -->
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Detail Pemeriksaan Ujian Siswa</h1>
                <p class="text-xs text-slate-500 mt-0.5">Periksa jawaban tiap soal, gunakan AI Auto-Correct, dan berikan rekomendasi belajar.</p>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-xs font-medium shadow-xs">
                    {{ session('success') }}
                </div>
            @endif

            <!-- KARTU INFORMASI SISWA & UJIAN -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <a href="{{ route('guru.penilaian.index') }}" class="text-xs font-bold text-blue-600 hover:underline mb-2 block">&larr; Kembali ke Daftar Penilaian</a>
                    <h3 class="font-bold text-gray-800 text-base">👤 Siswa: {{ $sub->siswa->name }}</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Ujian: <span class="font-semibold text-gray-700">{{ $sub->ujian->judul_ujian }}</span></p>
                </div>
                <div class="text-right">
                    <span class="text-[11px] text-gray-400 block">Waktu Pengumpulan</span>
                    <span class="text-xs font-bold text-gray-700">{{ $sub->created_at->format('d M Y, H:i') }}</span>
                </div>
            </div>

            <!-- DAFTAR SOAL DAN JAWABAN SISWA -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-6">
                <h4 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-3">Rincian Jawaban Siswa per Soal</h4>

                <div class="space-y-4">
                    @foreach($sub->ujian->questions ?? [] as $index => $q)
                        <div class="bg-gray-50/70 p-4 rounded-xl border border-gray-200 space-y-2">
                            <p class="font-bold text-gray-800 text-xs">
                                {{ $index + 1 }}. {{ $q['text'] }}
                            </p>
                            
                            <!-- Jawaban Siswa -->
                            <div class="bg-white p-3 rounded-lg border border-blue-100 text-xs text-blue-900 font-medium">
                                💬 Jawaban Siswa: <span class="font-normal text-gray-800">{{ $sub->jawaban[$index] ?? 'Tidak dijawab' }}</span>
                            </div>

                            <!-- Kunci Jawaban (Referensi Guru) -->
                            <div class="text-[11px] text-gray-500 italic">
                                🔑 Kunci Jawaban (AI): <span class="font-semibold text-green-700">{{ $q['answer'] ?? '-' }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- FORM PENILAIAN & REKOMENDASI BELAJAR -->
            <div class="bg-white rounded-2xl border-t-8 border-t-blue-600 border border-gray-200 shadow-sm p-6">
                <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-4">
                    <h4 class="font-bold text-gray-800 text-sm">Formulir Penilaian & Rekomendasi Belajar</h4>
                    
                    <!-- Tombol AI Auto-Correct & Rekomendasi -->
                    <a href="{{ route('guru.penilaian.ujian.ai', $sub->id) }}" class="bg-purple-50 hover:bg-purple-100 text-purple-700 font-bold text-xs px-4 py-2 rounded-xl transition border border-purple-200 flex items-center space-x-1 shadow-sm">
                        <span>🤖 Jalankan AI Auto-Correct & Rekomendasi</span>
                    </a>
                </div>

                <form action="{{ route('guru.penilaian.ujian.store', $sub->id) }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <!-- Input Nilai Akhir -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Nilai Akhir (0 - 100):</label>
                        <input type="number" name="nilai" value="{{ $sub->nilai }}" min="0" max="100" required placeholder="Contoh: 85" class="w-full text-xs border border-gray-200 rounded-xl p-3 bg-gray-50/50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <!-- Rekomendasi Belajar Berbantuan AI (Editable) -->
                    <div>
                        <label class="block text-xs font-bold text-blue-800 mb-1">💡 Rekomendasi Belajar (Dihasilkan oleh AI & Dapat Diedit Manual):</label>
                        <textarea name="rekomendasi_belajar" rows="4" placeholder="Rekomendasi materi remedial atau pengayaan..." class="w-full text-xs border border-blue-200 rounded-xl p-3 bg-blue-50/30 focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none">{{ $sub->rekomendasi_belajar }}</textarea>
                    </div>

                    <!-- Tombol Submit -->
                    <div class="flex justify-end pt-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-6 py-3 rounded-xl shadow-sm transition">
                            Simpan Penilaian & Kirim ke Siswa 🚀
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </main>

</body>
</html>