<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Ruang Belajar - {{ $materi->judul }} - Ratio Learn</title>

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
        <x-dashboard-header />

        <!-- DASHBOARD CONTAINER -->
        <div class="px-8 pb-8 space-y-6 flex-1 max-w-5xl">
            
            <!-- PAGE TITLE HEADER & TOMBOL KEMBALI -->
            <div>
                <a href="{{ route('siswa.materi.index') }}" class="text-xs font-bold text-blue-600 hover:underline mb-1 inline-block">&larr; Kembali ke Daftar Materi</a>
                <h1 class="text-xl font-extrabold text-slate-900">Ruang Belajar - {{ $materi->judul }}</h1>
                <p class="text-xs text-slate-500 mt-0.5">Pelajari materi rasio dan kerjakan tugas latihan di bawah ini.</p>
            </div>

            <!-- KONTAINER UTAMA -->
            <div class="space-y-6">
                
                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-xs font-medium shadow-xs">
                        ✨ {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-xs font-medium shadow-xs">
                        ⚠️ {{ session('error') }}
                    </div>
                @endif

                <!-- KONTEN MATERI -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-xs p-6 space-y-4">
                    <div class="flex items-center space-x-2">
                        <span class="bg-blue-50 text-blue-700 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">
                            Matematika: Rasio
                        </span>
                        <span class="text-xs text-gray-400">| Kelas: {{ $materi->kelas->nama_kelas ?? 'Umum' }}</span>
                    </div>

                    <h3 class="text-lg font-bold text-gray-800">{{ $materi->judul }}</h3>
                    
                    <div class="prose max-w-none text-xs text-gray-600 leading-relaxed border-t border-gray-100 pt-4">
                        {!! $materi->konten !!}
                    </div>

                    <!-- TOMBOL UNDUH DOKUMEN MATERI (PDF/Word/PPT) -->
                    @if($materi->file_path)
                        <div class="pt-4 border-t border-gray-100">
                            <a href="{{ asset('storage/' . $materi->file_path) }}" target="_blank" class="inline-flex items-center space-x-2 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs px-4 py-2.5 rounded-xl transition border border-blue-200">
                                <span>📥 Unduh / Lihat Dokumen Materi (PDF/Word/PPT)</span>
                            </a>
                        </div>
                    @endif
                </div>

                <!-- BAGIAN TUGAS / LATIHAN -->
                @if($materi->tugas && count($materi->tugas) > 0)
                    @foreach($materi->tugas as $tugas)
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-xs p-6 space-y-4 border-l-4 border-l-blue-600">
                            <div>
                                <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full uppercase tracking-wider">Tugas Pembelajaran</span>
                                <h4 class="font-bold text-gray-800 text-base mt-2">📝 {{ $tugas->judul }}</h4>
                                <p class="text-xs text-gray-600 mt-1">{{ $tugas->instruksi }}</p>
                            </div>

                            @php
                                $existingSubmission = \App\Models\Submission::where('siswa_id', auth()->id())
                                    ->where('tugas_id', $tugas->id)
                                    ->first();
                            @endphp

                            @if($existingSubmission)
                                <div class="bg-gray-50 border border-gray-200/60 p-4 rounded-xl space-y-2 text-xs">
                                    <span class="font-bold text-gray-500 block">Jawaban Anda yang Terkirim:</span>
                                    <p class="text-gray-800 bg-white p-3 rounded-lg border border-gray-200/50">{{ $existingSubmission->jawaban }}</p>
                                    
                                    <div class="pt-2 flex flex-wrap items-center gap-2 border-t border-gray-200/60">
                                        <span>Status: <strong class="text-indigo-600 uppercase">{{ $existingSubmission->status }}</strong></span>
                                        @if($existingSubmission->nilai !== null)
                                            <span>&bull; <strong class="text-green-600">Nilai: {{ $existingSubmission->nilai }}</strong></span>
                                            <div class="w-full mt-1 text-gray-500 italic bg-blue-50/50 p-2.5 rounded-lg border border-blue-100">
                                                Catatan Guru: {{ $existingSubmission->catatan_guru ?? 'Belum ada catatan.' }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <!-- Form Pengiriman Jawaban -->
                                <form action="{{ route('siswa.tugas.submit', $tugas->id) }}" method="POST" class="space-y-3 pt-2 border-t border-gray-100">
                                    @csrf
                                    <div>
                                        <label class="block text-[11px] font-semibold text-gray-700 mb-1">Tulis Jawaban Anda:</label>
                                        <textarea name="jawaban" rows="4" class="w-full text-xs border border-gray-200 rounded-xl p-3 bg-gray-50/50 focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none" placeholder="Ketik jawaban tugas di sini..." required></textarea>
                                    </div>
                                    <div class="flex justify-end">
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-6 py-2.5 rounded-xl shadow-xs transition">
                                            Kirim Jawaban Tugas 🚀
                                        </button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    @endforeach
                @endif

                <div class="pt-2">
                    <a href="{{ route('dashboard') }}" class="text-blue-600 hover:underline text-xs font-semibold">&larr; Kembali ke Dashboard</a>
                </div>

            </div>

        </div>
    </main>

</body>
</html>