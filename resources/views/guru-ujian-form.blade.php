<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $exam->exists ? 'Edit Paket Ujian' : 'Buat Paket Ujian Baru' }} - Ratio Learn</title>

    <!-- Google Font: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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
                <a href="{{ route('guru.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('guru.dashboard') || request()->routeIs('dashboard') ? 'bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">🏠</span><span>Dashboard</span>
                </a>
                
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
        
        <!-- HEADER TOP BAR REUSABLE -->
        <x-dashboard-header />

        <!-- DASHBOARD CONTAINER -->
        <div class="px-8 pb-8 space-y-6 flex-1 max-w-5xl">
            
            <!-- PAGE TITLE HEADER -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-extrabold text-slate-900">{{ $exam->exists ? 'Edit Paket Ujian' : 'Buat Paket Ujian Baru' }}</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Atur model, jadwal, penilaian, dan akses ujian.</p>
                </div>
                <a href="{{ route('guru.ujian.index') }}" class="text-xs font-bold text-blue-600 hover:underline">
                    ← Kembali ke Daftar Ujian
                </a>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded-xl text-xs space-y-1">
                    @foreach ($errors->all() as $error)
                        <p>• {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <!-- Kontainer Formulir -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-5">
                <form action="{{ $exam->exists ? route('guru.ujian.update', $exam->id) : route('guru.ujian.store') }}" method="POST" class="space-y-4">
                    @csrf
                    @if($exam->exists)
                        @method('PUT')
                    @endif
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Judul Ujian</label>
                        <input type="text" name="title" required value="{{ old('title', $exam->title) }}" placeholder="Judul Ujian / Quiz..." class="w-full text-xs font-medium border border-slate-200 rounded-xl p-3 outline-none focus:ring-2 focus:ring-blue-300">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi / Petunjuk</label>
                        <textarea name="description" rows="3" placeholder="Petunjuk pengerjaan ujian..." class="w-full text-xs font-medium border border-slate-200 rounded-xl p-3 outline-none focus:ring-2 focus:ring-blue-300">{{ old('description', $exam->description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kelas Target</label>
                            <select name="kelas_id" required class="w-full text-xs font-medium border border-slate-200 rounded-xl p-3 outline-none focus:ring-2 focus:ring-blue-300">
                                <option value="">Pilih Kelas</option>
                                @foreach ($kelasGuru as $k)
                                    <option value="{{ $k->id }}" @selected(old('kelas_id', $exam->kelas_id) == $k->id)>
                                        {{ $k->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Batas Maksimal Pelanggaran (Proctoring)</label>
                            <input type="number" name="max_violations" min="1" max="20" required value="{{ old('max_violations', $exam->max_violations ?? 3) }}" class="w-full text-xs font-medium border border-slate-200 rounded-xl p-3 outline-none focus:ring-2 focus:ring-blue-300">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Model Ujian</label>
                            <select name="exam_model" required class="w-full text-xs font-medium border border-slate-200 rounded-xl p-3 outline-none focus:ring-2 focus:ring-blue-300">
                                @foreach (['cbt' => 'CBT', 'essay' => 'Esai', 'mixed' => 'Campuran', 'quiz_interactive' => 'Quiz Interaktif'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('exam_model', $exam->exam_model ?? 'cbt') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Status</label>
                            <select name="status" required class="w-full text-xs font-medium border border-slate-200 rounded-xl p-3 outline-none focus:ring-2 focus:ring-blue-300">
                                @foreach (['draft' => 'Draft', 'published' => 'Dipublikasikan', 'closed' => 'Ditutup'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status', $exam->status ?? 'draft') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Durasi (menit)</label>
                            <input type="number" name="duration_minutes" min="1" max="600" required value="{{ old('duration_minutes', $exam->duration_minutes ?? 60) }}" class="w-full text-xs font-medium border border-slate-200 rounded-xl p-3 outline-none focus:ring-2 focus:ring-blue-300">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Jumlah Soal yang Direncanakan</label>
                            <input type="number" name="question_count" min="1" max="500" required value="{{ old('question_count', $exam->question_count ?: 20) }}" class="w-full text-xs font-medium border border-slate-200 rounded-xl p-3 outline-none focus:ring-2 focus:ring-blue-300">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nilai Minimal / KKM</label>
                            <input type="number" name="min_score" min="0" max="100" required value="{{ old('min_score', $exam->min_score ?? 75) }}" class="w-full text-xs font-medium border border-slate-200 rounded-xl p-3 outline-none focus:ring-2 focus:ring-blue-300">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Maksimal Percobaan</label>
                            <input type="number" name="max_attempts" min="1" max="10" required value="{{ old('max_attempts', $exam->max_attempts ?? 1) }}" class="w-full text-xs font-medium border border-slate-200 rounded-xl p-3 outline-none focus:ring-2 focus:ring-blue-300">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 border-y border-slate-100 py-4">
                        @foreach ([
                            'shuffle_questions' => 'Acak urutan soal',
                            'shuffle_options' => 'Acak pilihan jawaban',
                            'show_score' => 'Tampilkan nilai setelah selesai',
                            'show_explanations' => 'Tampilkan pembahasan setelah selesai',
                        ] as $setting => $label)
                            <label class="flex items-center gap-2 text-xs font-medium text-slate-700">
                                <input type="hidden" name="{{ $setting }}" value="0">
                                <input type="checkbox" name="{{ $setting }}" value="1" @checked(old($setting, $exam->{$setting} ?? false)) class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Waktu Mulai</label>
                            <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $exam->starts_at?->format('Y-m-d\TH:i')) }}" class="w-full text-xs font-medium border border-slate-200 rounded-xl p-3 outline-none focus:ring-2 focus:ring-blue-300">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Waktu Selesai</label>
                            <input type="datetime-local" name="ends_at" value="{{ old('ends_at', $exam->ends_at?->format('Y-m-d\TH:i')) }}" class="w-full text-xs font-medium border border-slate-200 rounded-xl p-3 outline-none focus:ring-2 focus:ring-blue-300">
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <a href="{{ route('guru.ujian.index') }}" class="px-4 py-2.5 text-xs font-bold text-slate-500 hover:bg-slate-100 rounded-xl transition">Batal</a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-6 py-2.5 rounded-xl shadow-xs transition cursor-pointer">
                            {{ $exam->exists ? 'Simpan & Kelola Soal' : 'Simpan & Kelola Soal' }}
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </main>

</body>
</html>