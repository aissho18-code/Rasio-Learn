<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Manajemen Ujian & Quiz - Ratio Learn</title>

    <!-- Google Font: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js untuk Dropdown -->
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
                <a href="{{ route('guru.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('guru.dashboard') || request()->routeIs('dashboard') ? 'bg-[#E0EDFF] text-[#2563EB] font-medium shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">🏠</span><span>Dashboard</span>
                </a>
                
     <a href="{{ route('guru.lkpd.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition-all duration-200 {{ request()->routeIs('guru.lkpd*') ? 'bg-[#E0EDFF] text-[#2563EB] font-medium shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
    <span class="text-base">📋</span>
    <span>Kelola LKPD</span>
</a>

                <a href="{{ route('guru.materi.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('guru.materi*') ? 'bg-[#E0EDFF] text-[#2563EB] font-medium shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">📖</span><span>Kelola Materi</span>
                </a>

                <a href="{{ route('guru.tugas.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('guru.tugas*') ? 'bg-[#E0EDFF] text-[#2563EB] font-medium shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">📋</span><span>Kelola Tugas</span>
                </a>

                <a href="{{ route('guru.ujian.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('guru.ujian*') ? 'bg-[#E0EDFF] text-[#2563EB] font-medium shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">📝</span><span>Kelola Ujian/Kuis</span>
                </a>

                <a href="{{ route('guru.penilaian.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('guru.penilaian*') ? 'bg-[#E0EDFF] text-[#2563EB] font-medium shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">📊</span><span>Penilaian & Evaluasi</span>
                </a>

                <a href="{{ route('guru.presensi.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('guru.presensi*') ? 'bg-[#E0EDFF] text-[#2563EB] font-medium shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">📅</span><span>Rekap Presensi Siswa</span>
                </a>

                <a href="{{ route('diskusi.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('diskusi*') ? 'bg-[#E0EDFF] text-[#2563EB] font-medium shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
                    <span class="text-base">💬</span><span>Forum Diskusi</span>
                </a>

                <a href="{{ route('guru.refleksi.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('guru.refleksi*') ? 'bg-[#E0EDFF] text-[#2563EB] font-medium shadow-xs' : 'text-slate-300 hover:bg-slate-800/50 hover:text-white font-medium' }}">
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
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-slate-400 hover:text-red-400 hover:bg-red-500/10 text-xs transition">
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
        <div class="px-8 pb-8 space-y-6 flex-1 max-w-6xl">
            
            <!-- PAGE TITLE HEADER -->
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Manajemen Ujian & Quiz</h1>
                <p class="text-xs text-slate-500 mt-0.5">Pilih paket ujian yang ingin dikelola atau buat paket ujian baru.</p>
            </div>

            @if(session('status') || session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-xs font-medium shadow-sm">
                    {{ session('status') ?? session('success') }}
                </div>
            @endif

            <!-- GRID DASHBOARD -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                
                <!-- KARTU 1: TOMBOL BUAT UJIAN BARU -->
                <a href="{{ route('guru.ujian.create') }}" class="bg-white rounded-2xl border-2 border-dashed border-blue-300 hover:border-blue-600 shadow-sm p-6 flex flex-col items-center justify-center text-center h-60 transition group cursor-pointer">
                    <div class="w-14 h-14 rounded-full bg-blue-50 group-hover:bg-blue-600 text-blue-600 group-hover:text-white flex items-center justify-center text-2xl font-bold transition mb-3 shadow-inner">
                        +
                    </div>
                    <h3 class="font-bold text-gray-800 text-sm group-hover:text-blue-600 transition">Buat Ujian Baru</h3>
                    <p class="text-[11px] text-gray-400 mt-1">Tambahkan paket soal pilihan ganda atau esai baru</p>
                </a>

                <!-- KARTU 2+: DAFTAR UJIAN YANG TELAH DIBUAT DARI DATABASE -->
                @forelse($exams as $exam)
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col justify-between h-60 relative hover:shadow-md transition" x-data="{ openMenu: false }">
                        
                        <!-- Header Kartu & Status Lock -->
                        <div>
                            <div class="flex items-start justify-between">
                                <span class="font-bold px-2.5 py-0.5 rounded-full text-[10px] uppercase {{ $exam->locked || $exam->status !== 'published' ? 'bg-amber-100 text-amber-800' : 'bg-green-100 text-green-700' }}">
                                    {{ $exam->locked ? 'Terkunci' : ucfirst($exam->status) }}
                                </span>
                                
                                <!-- Tombol Dropdown Tiga Titik -->
                                <div class="relative">
                                    <button @click="openMenu = !openMenu" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 transition focus:outline-none">
                                        ⋮
                                    </button>

                                    <!-- Menu Dropdown -->
                                    <div x-show="openMenu" @click.away="openMenu = false" class="absolute right-0 mt-1 w-40 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-10 text-xs" style="display: none;">
                                        <a href="{{ route('guru.ujian.edit', $exam->id) }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 font-medium">✏️ Edit Ujian</a>
                                        
                                        <form action="{{ route('guru.ujian.toggle-lock', $exam->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="w-full text-left px-4 py-2 font-medium {{ $exam->locked ? 'text-green-600 hover:bg-green-50' : 'text-amber-600 hover:bg-amber-50' }}">
                                                {{ $exam->locked ? '🔓 Buka Kunci' : '🔒 Kunci Ujian' }}
                                            </button>
                                        </form>

                                        <form action="{{ route('guru.ujian.destroy', $exam->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus ujian ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50 font-medium">🗑️ Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <h3 class="font-bold text-gray-800 text-sm mt-3 line-clamp-1">{{ $exam->title }}</h3>
                            <p class="text-xs text-gray-500 mt-1 line-clamp-2 leading-relaxed">{{ $exam->description ?? 'Tidak ada deskripsi.' }}</p>
                            <p class="mt-2 text-[10px] font-semibold text-blue-700">{{ ['cbt' => 'CBT', 'essay' => 'Esai', 'mixed' => 'Campuran', 'quiz_interactive' => 'Quiz Interaktif'][$exam->exam_model] ?? 'Ujian' }} · {{ $exam->duration_minutes }} menit · KKM {{ $exam->min_score }}</p>
                        </div>

                        <!-- Footer Kartu Informasi Kelas & Max Violation -->
                        <div class="border-t border-gray-100 pt-3 flex items-center justify-between text-[11px] text-gray-400 font-medium">
                            <span>{{ $exam->questions->count() }} dari {{ $exam->question_count }} soal · {{ $exam->kelas?->nama_kelas ?? 'Semua Kelas' }}</span>
                            <a href="{{ route('guru.ujian.questions.index', $exam->id) }}" class="font-bold text-blue-600 hover:underline">Kelola soal</a>
                        </div>

                    </div>
                @empty
                @endforelse

            </div>

            <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-6 py-4">
                    <h2 class="text-sm font-bold text-slate-800">Pengumpulan Ujian yang Perlu Ditinjau</h2>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($submissions as $submission)
                        <details class="px-6 py-4">
                            <summary class="flex cursor-pointer list-none flex-wrap items-center justify-between gap-3 text-xs">
                                <span class="font-bold text-slate-800">{{ $submission->student?->name }} · {{ $submission->exam?->title }} · Percobaan {{ $submission->attempt_number }}</span>
                                <span class="text-slate-500">
                                    {{ $submission->graded_at ? 'Nilai ' . $submission->score . '/100 · ' . ($submission->score >= ($submission->exam?->min_score ?? 0) ? 'Lulus' : 'Tidak lulus') : 'Menunggu penilaian esai' }}
                                    · {{ $submission->correct_count ?? '—' }} benar · {{ $submission->wrong_count ?? '—' }} salah
                                    · {{ $submission->duration_seconds ? gmdate('H:i:s', $submission->duration_seconds) : 'Waktu —' }}
                                    · {{ $submission->submitted_at?->diffForHumans() }}
                                </span>
                            </summary>
                            <div class="mt-3 space-y-3">
                                @forelse ($submission->exam?->questions ?? [] as $questionIndex => $question)
                                    @php $studentAnswer = $submission->answers[$question->id] ?? null; @endphp
                                    <article class="rounded-lg bg-slate-50 p-4">
                                        <p class="text-[10px] font-bold text-slate-500">Soal {{ $questionIndex + 1 }} · {{ $question->points }} poin</p>
                                        <p class="mt-1 whitespace-pre-line text-xs font-semibold text-slate-800">{{ $question->prompt }}</p>
                                        <p class="mt-2 whitespace-pre-wrap text-xs text-slate-600">Jawaban: {{ is_array($studentAnswer) ? implode(', ', $studentAnswer) : ($studentAnswer ?: 'Tidak dijawab') }}</p>
                                    </article>
                                @empty
                                    <p class="whitespace-pre-wrap rounded-lg bg-slate-50 p-4 text-xs leading-5 text-slate-700">{{ $submission->response }}</p>
                                @endforelse
                            </div>
                            @if ($submission->exam?->questions->isEmpty() || $submission->exam?->questions->contains('type', 'essay'))
                            <form method="POST" action="{{ route('guru.ujian.grade', $submission) }}" class="mt-4 space-y-3">
                                @csrf
                                @php $essayQuestions = $submission->exam?->questions->where('type', 'essay') ?? collect(); @endphp
                                @if ($essayQuestions->isNotEmpty())
                                    <div class="grid gap-3 sm:grid-cols-2">
                                        @foreach ($essayQuestions as $essayQuestion)
                                            <div>
                                                <label for="essay-score-{{ $submission->id }}-{{ $essayQuestion->id }}" class="mb-1 block text-[10px] font-bold text-slate-600">Nilai esai · {{ $essayQuestion->points }} poin maks.</label>
                                                <input id="essay-score-{{ $submission->id }}-{{ $essayQuestion->id }}" name="essay_scores[{{ $essayQuestion->id }}]" type="number" min="0" max="{{ $essayQuestion->points }}" step="0.01" required value="{{ old('essay_scores.' . $essayQuestion->id, $submission->essay_scores[$essayQuestion->id] ?? '') }}" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs">
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="max-w-[160px]">
                                        <label for="score-{{ $submission->id }}" class="mb-1 block text-[10px] font-bold text-slate-600">Nilai Final</label>
                                        <input id="score-{{ $submission->id }}" name="score" type="number" min="0" max="100" required value="{{ old('score', $submission->score) }}" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs">
                                    </div>
                                @endif
                                <div>
                                    <label for="feedback-{{ $submission->id }}" class="mb-1 block text-[10px] font-bold text-slate-600">Feedback Guru</label>
                                    <textarea id="feedback-{{ $submission->id }}" name="feedback" rows="2" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs">{{ old('feedback', $submission->feedback) }}</textarea>
                                </div>
                                <button type="submit" class="rounded-lg bg-blue-700 px-4 py-2 text-xs font-bold text-white hover:bg-blue-800">Simpan Nilai</button>
                            </form>
                            @else
                                <p class="mt-4 text-xs font-semibold text-emerald-700">Nilai soal objektif dihitung otomatis: {{ $submission->score ?? '—' }} / 100.</p>
                            @endif
                        </details>
                    @empty
                        <p class="px-6 py-8 text-center text-xs text-slate-400">Belum ada pengumpulan ujian.</p>
                    @endforelse
                </div>
            </section>

        </div>
    </main>

</body>
</html><body class="bg-[#F0F5FF] ...
