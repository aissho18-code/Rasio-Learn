@extends('layouts.admin')

@php $title = $exam->exists ? 'Edit Ujian - Ratio Learn' : 'Buat Ujian - Ratio Learn'; @endphp

@section('content')
<div class="mx-auto max-w-5xl space-y-5">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <a href="{{ route('admin.exams.index') }}" class="text-xs font-semibold text-blue-700 hover:underline">← Kelola Ujian</a>
            <h1 class="mt-2 text-xl font-extrabold text-slate-900">{{ $exam->exists ? 'Edit Ujian' : 'Buat Ujian Baru (Admin)' }}</h1>
            <p class="mt-1 text-xs text-slate-500">Kelola ujian seluruh kelas; ujian dipublikasikan akan tersedia untuk siswa sesuai kelas tujuan.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-xs text-red-700">
            @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <form method="POST" action="{{ $exam->exists ? route('admin.exams.update', $exam->id) : route('admin.exams.store') }}" class="space-y-5 rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
        @csrf
        @if ($exam->exists) @method('PUT') @endif

        <div>
            <label for="title" class="mb-1 block text-xs font-bold text-slate-700">Judul Ujian</label>
            <input id="title" name="title" required value="{{ old('title', $exam->title) }}" class="w-full rounded-lg border border-slate-300 p-3 text-xs focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
        </div>
        <div>
            <label for="description" class="mb-1 block text-xs font-bold text-slate-700">Deskripsi / Petunjuk</label>
            <textarea id="description" name="description" rows="3" class="w-full rounded-lg border border-slate-300 p-3 text-xs">{{ old('description', $exam->description) }}</textarea>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="kelas_id" class="mb-1 block text-xs font-bold text-slate-700">Kelas Tujuan</label>
                <select id="kelas_id" name="kelas_id" class="w-full rounded-lg border border-slate-300 p-3 text-xs">
                    <option value="">Semua Kelas</option>
                    @foreach ($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" @selected(old('kelas_id', $exam->kelas_id) == $kelas->id)>{{ $kelas->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="exam_model" class="mb-1 block text-xs font-bold text-slate-700">Model Ujian</label>
                <select id="exam_model" name="exam_model" required class="w-full rounded-lg border border-slate-300 p-3 text-xs">
                    @foreach (['cbt' => 'CBT', 'essay' => 'Esai', 'mixed' => 'Campuran', 'quiz_interactive' => 'Quiz Interaktif'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('exam_model', $exam->exam_model ?? 'cbt') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status" class="mb-1 block text-xs font-bold text-slate-700">Status</label>
                <select id="status" name="status" required class="w-full rounded-lg border border-slate-300 p-3 text-xs">
                    @foreach (['draft' => 'Draft', 'published' => 'Dipublikasikan', 'closed' => 'Ditutup'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $exam->status ?? 'draft') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="duration_minutes" class="mb-1 block text-xs font-bold text-slate-700">Durasi (menit)</label>
                <input id="duration_minutes" name="duration_minutes" type="number" min="1" max="600" required value="{{ old('duration_minutes', $exam->duration_minutes ?? 60) }}" class="w-full rounded-lg border border-slate-300 p-3 text-xs">
            </div>
            <div>
                <label for="question_count" class="mb-1 block text-xs font-bold text-slate-700">Jumlah Soal</label>
                <input id="question_count" name="question_count" type="number" min="1" max="500" required value="{{ old('question_count', $exam->question_count ?: ($exam->exists ? $exam->questions()->count() : 20)) }}" class="w-full rounded-lg border border-slate-300 p-3 text-xs">
            </div>
            <div>
                <label for="min_score" class="mb-1 block text-xs font-bold text-slate-700">KKM</label>
                <input id="min_score" name="min_score" type="number" min="0" max="100" required value="{{ old('min_score', $exam->min_score ?? 75) }}" class="w-full rounded-lg border border-slate-300 p-3 text-xs">
            </div>
            <div>
                <label for="max_attempts" class="mb-1 block text-xs font-bold text-slate-700">Maksimal Percobaan</label>
                <input id="max_attempts" name="max_attempts" type="number" min="1" max="10" required value="{{ old('max_attempts', $exam->max_attempts ?? 1) }}" class="w-full rounded-lg border border-slate-300 p-3 text-xs">
            </div>
            <div>
                <label for="max_violations" class="mb-1 block text-xs font-bold text-slate-700">Batas Maksimal Pelanggaran</label>
                <input id="max_violations" name="max_violations" type="number" min="1" max="20" required value="{{ old('max_violations', $exam->max_violations ?? 3) }}" class="w-full rounded-lg border border-slate-300 p-3 text-xs">
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="starts_at" class="mb-1 block text-xs font-bold text-slate-700">Waktu Mulai</label>
                <input id="starts_at" type="datetime-local" name="starts_at" value="{{ old('starts_at', $exam->starts_at?->format('Y-m-d\TH:i')) }}" class="w-full rounded-lg border border-slate-300 p-3 text-xs">
            </div>
            <div>
                <label for="ends_at" class="mb-1 block text-xs font-bold text-slate-700">Waktu Selesai</label>
                <input id="ends_at" type="datetime-local" name="ends_at" value="{{ old('ends_at', $exam->ends_at?->format('Y-m-d\TH:i')) }}" class="w-full rounded-lg border border-slate-300 p-3 text-xs">
            </div>
        </div>

        <div class="grid gap-3 border-y border-slate-100 py-4 sm:grid-cols-2">
            @foreach (['shuffle_questions' => 'Acak urutan soal', 'shuffle_options' => 'Acak pilihan jawaban', 'show_score' => 'Tampilkan nilai setelah selesai', 'show_explanations' => 'Tampilkan pembahasan setelah selesai'] as $setting => $label)
                <label class="flex items-center gap-2 text-xs font-medium text-slate-700">
                    <input type="hidden" name="{{ $setting }}" value="0">
                    <input type="checkbox" name="{{ $setting }}" value="1" @checked(old($setting, $exam->{$setting} ?? false)) class="rounded border-slate-300 text-blue-600">
                    {{ $label }}
                </label>
            @endforeach
        </div>

        <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
            <a href="{{ route('admin.exams.index') }}" class="rounded-lg px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100">Batal</a>
            <button class="rounded-lg bg-blue-700 px-5 py-2.5 text-xs font-bold text-white hover:bg-blue-800">Simpan & Kelola Soal</button>
        </div>
    </form>

    @if ($exam->exists)
        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                <div>
                    <h2 class="text-sm font-bold text-slate-800">Kelola Soal</h2>
                    <p class="mt-1 text-xs text-slate-500">{{ $exam->questions->count() }} dari {{ $exam->question_count }} soal</p>
                </div>
                <a href="{{ route('admin.exams.questions.create', $exam->id) }}" class="rounded-lg bg-blue-700 px-4 py-2 text-xs font-bold text-white hover:bg-blue-800">+ Tambah Soal</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($exam->questions as $index => $question)
                    <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4">
                        <div class="min-w-0 flex-1">
                            <p class="text-[10px] font-bold uppercase text-slate-400">Soal {{ $index + 1 }} · {{ [
                                'multiple_choice' => 'Pilihan Ganda',
                                'multiple_response' => 'Pilihan Ganda Kompleks',
                                'true_false' => 'Benar/Salah',
                                'short_answer' => 'Isian Singkat',
                                'essay' => 'Esai',
                            ][$question->type] ?? $question->type }} · {{ $question->points }} poin</p>
                            <p class="mt-1 line-clamp-2 text-xs font-semibold text-slate-800">{{ $question->prompt }}</p>
                        </div>
                        <div class="flex shrink-0 gap-3 text-xs">
                            <a href="{{ route('admin.exams.questions.edit', [$exam->id, $question->id]) }}" class="font-bold text-blue-700 hover:underline">Edit Soal</a>
                            <form method="POST" action="{{ route('admin.exams.questions.destroy', [$exam->id, $question->id]) }}" onsubmit="return confirm('Hapus soal ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="font-bold text-red-600 hover:underline">Hapus</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="px-5 py-8 text-center text-xs text-slate-400">Belum ada soal. Tambahkan soal sebelum mempublikasikan ujian.</p>
                @endforelse
            </div>
        </section>
    @endif
</div>
@endsection