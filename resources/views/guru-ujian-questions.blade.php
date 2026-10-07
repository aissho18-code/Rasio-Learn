@extends(($isAdmin ?? false) ? 'layouts.admin' : 'layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <a href="{{ route(($isAdmin ?? false) ? 'admin.exams.index' : 'guru.ujian.index') }}" class="text-xs font-semibold text-blue-700 hover:underline">← Kelola Ujian</a>
            <h1 class="mt-2 text-xl font-extrabold text-slate-900">Kelola Soal</h1>
            <p class="mt-1 text-sm font-semibold text-slate-700">Ujian: {{ $exam->title }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route(($isAdmin ?? false) ? 'admin.exams.edit' : 'guru.ujian.edit', $exam->id) }}" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">Edit Ujian</a>
            <a href="{{ route(($isAdmin ?? false) ? 'admin.exams.questions.create' : 'guru.ujian.questions.create', $exam->id) }}" class="rounded-lg bg-blue-700 px-4 py-2 text-xs font-bold text-white hover:bg-blue-800">+ Tambah Soal</a>
        </div>
    </div>

    @if (session('status'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-800">{{ session('status') }}</div>
    @endif

    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <p class="text-[10px] font-bold uppercase text-slate-400">Model</p>
            <p class="mt-1 text-sm font-bold text-slate-800">{{ ['cbt' => 'CBT', 'essay' => 'Esai', 'mixed' => 'Campuran', 'quiz_interactive' => 'Quiz Interaktif'][$exam->exam_model] ?? $exam->exam_model }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <p class="text-[10px] font-bold uppercase text-slate-400">Durasi</p>
            <p class="mt-1 text-sm font-bold text-slate-800">{{ $exam->duration_minutes }} menit</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <p class="text-[10px] font-bold uppercase text-slate-400">Soal</p>
            <p class="mt-1 text-sm font-bold text-slate-800">{{ $exam->questions->count() }} dari {{ $exam->question_count }} soal</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <p class="text-[10px] font-bold uppercase text-slate-400">KKM · Status</p>
            <p class="mt-1 text-sm font-bold text-slate-800">{{ $exam->min_score }} · {{ ucfirst($exam->status) }}</p>
        </div>
    </section>

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[680px] text-left">
                <thead class="bg-slate-50 text-[10px] uppercase text-slate-500">
                    <tr>
                        <th class="px-5 py-3">No</th>
                        <th class="px-5 py-3">Soal</th>
                        <th class="px-5 py-3">Tipe</th>
                        <th class="px-5 py-3">Bobot</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse ($exam->questions as $index => $question)
                        <tr>
                            <td class="px-5 py-4 font-semibold text-slate-500">{{ $index + 1 }}</td>
                            <td class="max-w-xl px-5 py-4 font-medium leading-5 text-slate-800">{{ $question->prompt }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ [
                                'multiple_choice' => 'Pilihan Ganda',
                                'multiple_response' => 'Pilihan Ganda Kompleks',
                                'true_false' => 'Benar/Salah',
                                'short_answer' => 'Isian Singkat',
                                'essay' => 'Esai',
                            ][$question->type] ?? $question->type }}</td>
                            <td class="px-5 py-4 font-semibold text-slate-700">{{ rtrim(rtrim(number_format((float) $question->points, 2, ',', '.'), '0'), ',') }}</td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route(($isAdmin ?? false) ? 'admin.exams.questions.edit' : 'guru.ujian.questions.edit', [$exam->id, $question->id]) }}" class="font-bold text-blue-700 hover:underline">Edit</a>
                                    <form method="POST" action="{{ route(($isAdmin ?? false) ? 'admin.exams.questions.destroy' : 'guru.ujian.questions.destroy', [$exam->id, $question->id]) }}" onsubmit="return confirm('Hapus soal ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="font-bold text-red-600 hover:underline">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-12 text-center text-slate-400">Belum ada soal. Tambahkan soal sebelum ujian dipublikasikan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex items-center justify-between border-t border-slate-100 px-5 py-4">
            <p class="text-xs text-slate-400">
                Semua soal tersimpan otomatis setelah menekan "Simpan Soal".
            </p>
            <a href="{{ route('guru.ujian.index') }}"
               class="rounded-lg bg-blue-700 px-5 py-2.5 text-xs font-bold text-white hover:bg-blue-800">
                ✓ Selesai
            </a>
        </div>
    </section>
</div>
@endsection