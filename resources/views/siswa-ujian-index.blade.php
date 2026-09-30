@extends('layouts.siswa')

@php
    $title = 'Daftar Ujian Siswa - Ratio Learn';
@endphp

@section('content')
<div class="space-y-6">
    @if (session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-3 text-xs font-semibold text-red-700 shadow-xs">
            {{ session('error') }}
        </div>
    @endif

    @if (session('status'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-5 py-3 text-xs font-semibold text-green-700 shadow-xs">
            {{ session('status') }}
        </div>
    @endif

    <!-- HEADER HALAMAN -->
    <div>
        <h1 class="text-xl font-extrabold text-slate-900">📝 Daftar Ujian & Quiz Siswa</h1>
        <p class="mt-0.5 text-xs text-slate-500">Pilih ujian yang tersedia untuk kelas Anda dan mulailah pengerjaan.</p>
    </div>

    <!-- DAFTAR UJIAN -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @forelse ($exams as $exam)
            @php
                $activeAttempt = $exam->submissions->first(fn ($attempt) => !$attempt->completed_at && !$attempt->submitted_at);
                $lastAttempt = $exam->submissions->first();
                $notStarted = $exam->starts_at && now()->lt($exam->starts_at);
                $ended = $exam->ends_at && now()->gt($exam->ends_at);
                $canStart = !$exam->locked && !$notStarted && ($activeAttempt || (!$ended && $exam->student_attempts < $exam->max_attempts));
            @endphp
            <div class="flex flex-col rounded-2xl border border-slate-100 bg-white p-6 shadow-xs relative justify-between min-h-[240px]">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="rounded-full px-2.5 py-0.5 text-[10px] font-bold {{ $exam->locked || $ended ? 'bg-slate-100 text-slate-600' : 'bg-green-100 text-green-700' }}">
                            {{ $exam->locked ? 'TERKUNCI' : ($ended ? 'WAKTU BERAKHIR' : ($notStarted ? 'BELUM DIMULAI' : ($activeAttempt ? 'SEDANG DIKERJAKAN' : 'DIPUBLIKASIKAN'))) }}
                        </span>
                        <span class="text-[10px] text-slate-400 font-medium">
                            Max Pelanggaran: {{ $exam->max_violations ?? 3 }}x
                        </span>
                    </div>

                    <h2 class="mt-3 text-sm font-extrabold text-slate-800 line-clamp-1">{{ $exam->title }}</h2>
                    <p class="mt-1 text-xs text-slate-500 line-clamp-2 leading-relaxed">{{ $exam->description ?? 'Tidak ada petunjuk khusus.' }}</p>
                    <p class="mt-2 text-[10px] font-semibold text-blue-700">{{ ['cbt' => 'CBT', 'essay' => 'Esai', 'mixed' => 'Campuran', 'quiz_interactive' => 'Quiz Interaktif'][$exam->exam_model] ?? 'Ujian' }} · {{ $exam->duration_minutes }} menit · {{ $exam->question_count }} soal · KKM {{ $exam->min_score }}</p>
                </div>

                <div class="pt-4 border-t border-slate-100 space-y-3">
                    <div class="flex items-center justify-between text-[11px] text-slate-400 font-medium">
                        <span>Kelas: {{ $exam->kelas?->nama_kelas ?? 'Semua Kelas' }}</span>
                        <span>Percobaan: {{ $exam->student_attempts }}/{{ $exam->max_attempts }}</span>
                    </div>
                    @if ($canStart)
                        <a href="{{ route('siswa.ujian.show', $exam->id) }}" class="block w-full text-center rounded-xl bg-blue-600 hover:bg-blue-700 px-4 py-2.5 text-xs font-bold text-white shadow-xs transition">
                            {{ $activeAttempt ? 'Lanjutkan Ujian' : 'Mulai Kerjakan Ujian' }} →
                        </a>
                    @elseif ($lastAttempt?->completed_at)
                        <a href="{{ route('siswa.ujian.result', $lastAttempt) }}" class="block w-full text-center rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50">Lihat Hasil Percobaan Terakhir</a>
                    @else
                        <span class="block w-full rounded-xl bg-slate-100 px-4 py-2.5 text-center text-xs font-semibold text-slate-400">{{ $notStarted ? 'Tersedia ' . $exam->starts_at->format('d/m H:i') : 'Tidak tersedia' }}</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-xs text-slate-400 bg-white rounded-2xl border border-slate-100 italic">
                Belum ada ujian yang tersedia untuk kelas Anda saat ini.
            </div>
        @endforelse
    </div>
</div>
@endsection