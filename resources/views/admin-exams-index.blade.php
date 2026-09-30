@extends('layouts.admin')

@php
    $title = 'Manajemen Ujian Admin - Ratio Learn';
@endphp

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <!-- FLASH MESSAGE -->
    @if (session('status'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-5 py-3 text-xs font-semibold text-green-700">
            {{ session('status') }}
        </div>
    @endif

    <!-- HEADER HALAMAN -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">🧪 Manajemen Ujian / Quiz</h1>
            <p class="mt-0.5 text-xs text-slate-500">Kelola seluruh paket ujian, atur kelas, dan pantau status ujian.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.exams.export-logs') }}" class="rounded-xl bg-slate-800 hover:bg-slate-900 px-4 py-2.5 text-xs font-bold text-white shadow-xs transition">
                📥 Ekspor Log
            </a>
            <a href="{{ route('admin.exams.create') }}" class="rounded-xl bg-blue-600 hover:bg-blue-700 px-4 py-2.5 text-xs font-bold text-white shadow-xs transition">
                + Buat Ujian Baru
            </a>
        </div>
    </div>

    <!-- FILTER KELAS VIA GET -->
    <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-xs">
        <form method="GET" action="{{ route('admin.exams.index') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xs font-extrabold text-slate-800">Filter Berdasarkan Kelas</h2>
                <p class="text-[11px] text-slate-400">Pilih kelas untuk menyaring daftar ujian.</p>
            </div>

            <div class="flex items-center gap-2">
                <select name="kelas_id" onchange="this.form.submit()" class="min-w-[200px] text-xs font-semibold border border-slate-200 rounded-xl p-2.5 bg-slate-50 outline-none focus:ring-2 focus:ring-blue-300">
                    <option value="0">Semua Kelas</option>
                    @foreach ($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" @selected((int) ($selectedClassId ?? 0) === (int) $kelas->id)>
                            {{ $kelas->nama_kelas }}
                        </option>
                    @endforeach
                </select>

                @if (($selectedClassId ?? 0) > 0)
                    <a href="{{ route('admin.exams.index') }}" class="rounded-xl border border-slate-200 px-3 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- RINGKASAN STATISTIK -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-xs">
            <p class="text-[11px] font-semibold text-slate-400">Total Ujian</p>
            <p class="text-2xl font-extrabold text-slate-800 mt-1">{{ $exams->count() }}</p>
        </div>
        <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-xs">
            <p class="text-[11px] font-semibold text-slate-400">Ujian Aktif</p>
            <p class="text-2xl font-extrabold text-green-600 mt-1">{{ $exams->where('status', 'published')->where('locked', false)->count() }}</p>
        </div>
        <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-xs">
            <p class="text-[11px] font-semibold text-slate-400">Ujian Terkunci</p>
            <p class="text-2xl font-extrabold text-red-600 mt-1">{{ $exams->where('locked', true)->count() }}</p>
        </div>
    </div>

    <!-- KARTU UJIAN -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @forelse ($exams as $exam)
            <div class="flex flex-col rounded-2xl border border-slate-100 bg-white p-6 shadow-xs relative justify-between min-h-[250px]">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="rounded-full px-2.5 py-0.5 text-[10px] font-bold {{ $exam->locked ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                            {{ $exam->locked ? '🔒 TERKUNCI' : strtoupper($exam->status ?? 'published') }}
                        </span>
                        <span class="text-[10px] text-slate-400 font-medium">
                            Oleh: {{ $exam->creator?->name ?? 'Admin' }}
                        </span>
                    </div>

                    <h2 class="mt-3 text-sm font-extrabold text-slate-800 line-clamp-1">{{ $exam->title }}</h2>
                    <p class="mt-1 text-xs text-slate-500 line-clamp-2 leading-relaxed">{{ $exam->description ?? 'Tidak ada deskripsi.' }}</p>
                    <p class="mt-2 text-[10px] font-semibold text-blue-700">{{ ['cbt' => 'CBT', 'essay' => 'Esai', 'mixed' => 'Campuran', 'quiz_interactive' => 'Quiz Interaktif'][$exam->exam_model] ?? 'Ujian' }} · {{ $exam->duration_minutes }} menit · {{ $exam->questions->count() }}/{{ $exam->question_count }} soal · KKM {{ $exam->min_score }}</p>
                </div>

                <div class="pt-4 border-t border-slate-100 space-y-3">
                    <div class="flex items-center justify-between text-[11px] text-slate-400 font-medium">
                        <span>Kelas: {{ $exam->kelas?->nama_kelas ?? 'Semua Kelas' }}</span>
                        <span>Max Pelanggaran: {{ $exam->max_violations ?? 3 }}</span>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                        <a href="{{ route('admin.exams.edit', $exam->id) }}" class="rounded-lg px-3 py-1.5 text-xs font-bold text-blue-700 bg-blue-50 border border-blue-100 hover:bg-blue-100 transition">Kelola Ujian</a>

                        <div class="flex items-center gap-2">
                            <form method="POST" action="{{ route('admin.exams.toggle-lock', $exam->id) }}">
                                @csrf
                                <button type="submit" class="rounded-lg px-3 py-1.5 text-xs font-bold {{ $exam->locked ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }} transition cursor-pointer">
                                    {{ $exam->locked ? 'Buka Kunci' : 'Kunci' }}
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.exams.destroy', $exam->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ujian ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-lg px-3 py-1.5 text-xs font-bold text-red-600 bg-red-50 border border-red-100 hover:bg-red-100 transition cursor-pointer">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-xs text-slate-400 bg-white rounded-2xl border border-slate-100 italic">
                Tidak ada ujian yang ditemukan untuk kriteria kelas ini.
            </div>
        @endforelse
    </div>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="text-sm font-bold text-slate-800">Pengumpulan dan Hasil Siswa</h2>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse ($submissions as $submission)
                <details class="px-5 py-4">
                    <summary class="flex cursor-pointer list-none flex-wrap items-center justify-between gap-3 text-xs">
                        <span class="font-bold text-slate-800">{{ $submission->student?->name }} · {{ $submission->exam?->title }} · Percobaan {{ $submission->attempt_number }}</span>
                        <span class="text-slate-500">
                            {{ $submission->graded_at ? 'Nilai ' . $submission->score . '/100 · ' . ($submission->score >= ($submission->exam?->min_score ?? 0) ? 'Lulus' : 'Tidak lulus') : 'Menunggu penilaian' }}
                            · {{ $submission->correct_count ?? '—' }} benar · {{ $submission->wrong_count ?? '—' }} salah
                            · {{ $submission->duration_seconds ? gmdate('H:i:s', $submission->duration_seconds) : 'Waktu —' }}
                        </span>
                    </summary>
                    <div class="mt-3 space-y-3">
                        @forelse ($submission->exam?->questions ?? [] as $index => $question)
                            @php $answer = $submission->answers[$question->id] ?? null; @endphp
                            <article class="rounded-lg bg-slate-50 p-4">
                                <p class="text-[10px] font-bold text-slate-500">Soal {{ $index + 1 }} · {{ $question->points }} poin</p>
                                <p class="mt-1 whitespace-pre-line text-xs font-semibold text-slate-800">{{ $question->prompt }}</p>
                                <p class="mt-2 whitespace-pre-wrap text-xs text-slate-600">Jawaban: {{ is_array($answer) ? implode(', ', $answer) : ($answer ?: 'Tidak dijawab') }}</p>
                            </article>
                        @empty
                            <p class="whitespace-pre-wrap rounded-lg bg-slate-50 p-4 text-xs text-slate-700">{{ $submission->response }}</p>
                        @endforelse
                    </div>

                    @php $essayQuestions = $submission->exam?->questions->where('type', 'essay') ?? collect(); @endphp
                    @if ($essayQuestions->isNotEmpty() || $submission->exam?->questions->isEmpty())
                        <form method="POST" action="{{ route('admin.exams.grade', $submission) }}" class="mt-4 space-y-3">
                            @csrf
                            @if ($essayQuestions->isNotEmpty())
                                <div class="grid gap-3 sm:grid-cols-2">
                                    @foreach ($essayQuestions as $question)
                                        <div>
                                            <label for="admin-score-{{ $submission->id }}-{{ $question->id }}" class="mb-1 block text-[10px] font-bold text-slate-600">Nilai esai · maks. {{ $question->points }}</label>
                                            <input id="admin-score-{{ $submission->id }}-{{ $question->id }}" type="number" name="essay_scores[{{ $question->id }}]" min="0" max="{{ $question->points }}" step="0.01" required value="{{ $submission->essay_scores[$question->id] ?? '' }}" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs">
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="max-w-[160px]">
                                    <label for="admin-total-score-{{ $submission->id }}" class="mb-1 block text-[10px] font-bold text-slate-600">Nilai Final</label>
                                    <input id="admin-total-score-{{ $submission->id }}" type="number" name="score" min="0" max="100" required value="{{ $submission->score }}" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs">
                                </div>
                            @endif
                            <div>
                                <label for="admin-feedback-{{ $submission->id }}" class="mb-1 block text-[10px] font-bold text-slate-600">Feedback</label>
                                <textarea id="admin-feedback-{{ $submission->id }}" name="feedback" rows="2" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs">{{ $submission->feedback }}</textarea>
                            </div>
                            <button class="rounded-lg bg-blue-700 px-4 py-2 text-xs font-bold text-white hover:bg-blue-800">Simpan Penilaian</button>
                        </form>
                    @else
                        <p class="mt-3 text-xs font-semibold text-emerald-700">Nilai objektif dihitung otomatis: {{ $submission->score ?? '—' }}/100 · {{ $submission->correct_count ?? '—' }} benar · {{ $submission->wrong_count ?? '—' }} salah · {{ $submission->duration_seconds ? gmdate('H:i:s', $submission->duration_seconds) : 'Waktu —' }}</p>
                    @endif
                </details>
            @empty
                <p class="px-5 py-8 text-center text-xs text-slate-400">Belum ada submission siswa.</p>
            @endforelse
        </div>
    </section>
</div>
@endsection