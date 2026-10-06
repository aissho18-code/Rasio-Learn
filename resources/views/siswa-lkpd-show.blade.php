@extends('layouts.siswa')

@php
    $title = 'Kerjakan LKPD - ' . $lkpd->judul;
@endphp

@section('content')
    <div class="mx-auto max-w-4xl space-y-5">
        <a href="{{ route('siswa.aktivitas.index') }}" class="text-xs font-semibold text-blue-700 hover:underline">← Kembali ke Aktivitas</a>

        @if($errors->has('assessment'))
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700" role="alert">
                {{ $errors->first('assessment') }}
            </div>
        @endif

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="space-y-2">
                <h1 class="text-xl font-extrabold text-slate-900">{{ $lkpd->judul }}</h1>
                @if($lkpd->deskripsi)
                    <p class="text-sm text-slate-600">{{ $lkpd->deskripsi }}</p>
                @endif
                @if($lkpd->instruksi)
                    <p class="text-xs text-slate-500">Petunjuk: {{ $lkpd->instruksi }}</p>
                @endif
                @if($lkpd->deadline)
                    <p class="text-xs text-slate-500">Batas pengumpulan: {{ $lkpd->deadline->format('d M Y, H:i') }}</p>
                @endif
            </div>

            @if($lkpd->modul_path)
                <a href="{{ Storage::disk('public')->url($lkpd->modul_path) }}" target="_blank" rel="noopener" class="mt-4 inline-flex text-xs font-semibold text-blue-600 hover:underline">Unduh Modul LKPD</a>
            @endif
        </section>

        @if($submission?->hasil_penilaian)
            <section class="space-y-4">
                <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5">
                    <h2 class="text-base font-extrabold text-blue-900">Hasil LKPD</h2>
                    <p class="mt-1 text-sm text-blue-800">Nilai: <strong>{{ number_format((float) $submission->nilai, 2) }} / 100</strong></p>
                    <p class="mt-1 text-xs text-blue-700">Jawaban dan pembahasan setiap soal dapat dilihat di bawah.</p>
                </div>

                @foreach($lkpd->questions as $question)
                    @php $result = $submission->hasil_penilaian[(string) $question->id] ?? null; @endphp
                    @if($result)
                        <article class="rounded-2xl border {{ $result['is_correct'] ? 'border-emerald-200 bg-emerald-50' : 'border-amber-200 bg-amber-50' }} p-5">
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <h3 class="text-sm font-bold text-slate-800">{{ $loop->iteration }}. {{ $question->pertanyaan }}</h3>
                                <span class="rounded-full px-3 py-1 text-xs font-extrabold {{ $result['is_correct'] ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $result['is_correct'] ? 'Benar' : 'Perlu diperbaiki' }}
                                </span>
                            </div>
                            <p class="mt-3 whitespace-pre-line text-sm text-slate-700"><strong>Jawaban Anda:</strong> {{ $submission->jawaban[$question->id] ?? '—' }}</p>
                            <p class="mt-2 whitespace-pre-line text-sm text-slate-700"><strong>Feedback:</strong> {{ $result['feedback'] }}</p>
                            @if($question->pembahasan)
                                <div class="mt-3 border-t border-slate-200/80 pt-3">
                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-600">Pembahasan Guru</p>
                                    <p class="mt-1 whitespace-pre-line text-sm text-slate-700">{{ $question->pembahasan }}</p>
                                </div>
                            @endif
                        </article>
                    @endif
                @endforeach
            </section>
        @endif

        <form action="{{ route('siswa.lkpd.submit', $lkpd) }}" method="POST" class="space-y-4">
            @csrf
            @forelse($lkpd->questions as $question)
                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <label for="jawaban-{{ $question->id }}" class="block text-sm font-bold text-slate-700">
                        {{ $loop->iteration }}. {{ $question->pertanyaan }}
                    </label>
                    @if($question->gambar_path)
                        <img src="{{ route('siswa.lkpd.question-image', [$lkpd, $question]) }}" alt="Ilustrasi soal {{ $loop->iteration }}" class="mb-3 mt-3 max-h-64 rounded-lg" onerror="this.hidden = true; this.nextElementSibling.hidden = false;">
                        <p class="mt-2 hidden text-xs text-amber-700">Gambar soal tidak dapat ditampilkan.</p>
                    @endif
                    <textarea id="jawaban-{{ $question->id }}" name="jawaban[{{ $question->id }}]" rows="4" required placeholder="Tuliskan jawaban Anda di sini..." class="mt-3 w-full rounded-xl border border-gray-200 bg-gray-50 p-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('jawaban.' . $question->id, $submission?->jawaban[$question->id] ?? '') }}</textarea>
                    @error('jawaban.' . $question->id)
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </section>
            @empty
                <p class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-xs text-amber-800">Belum ada pertanyaan pada LKPD ini.</p>
            @endforelse

            @if($lkpd->questions->isNotEmpty())
                <div class="flex justify-end">
                    <button type="submit" class="rounded-xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700">Kirim Jawaban</button>
                </div>
            @endif
        </form>
    </div>
@endsection