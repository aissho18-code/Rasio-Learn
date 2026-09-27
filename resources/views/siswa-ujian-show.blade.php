@extends('layouts.siswa')

@php
    $title = $exam->title;
    $subtitle = 'Ujian dan kuis';
    $submission = $exam->submissions->first();
@endphp

@section('content')
<div class="mx-auto max-w-4xl space-y-5">
    <a href="{{ route('siswa.ujian.index') }}" class="text-xs font-semibold text-blue-700 hover:underline">← Kembali ke daftar ujian</a>

    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">{{ session('success') }}</div>
    @endif

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase text-blue-700">{{ $exam->kelas?->nama_kelas ?? 'Semua kelas' }}</p>
                <h1 class="mt-1 text-2xl font-extrabold text-slate-900">{{ $exam->title }}</h1>
                <p class="mt-3 whitespace-pre-line text-sm leading-6 text-slate-600">{{ $exam->description ?: 'Tidak ada petunjuk tambahan.' }}</p>
            </div>
            <div class="text-right text-xs text-slate-500">
                <p>Mulai: {{ $exam->starts_at?->format('d M Y H:i') ?? 'Bebas' }}</p>
                <p class="mt-1">Berakhir: {{ $exam->ends_at?->format('d M Y H:i') ?? 'Tanpa batas waktu' }}</p>
            </div>
        </div>
    </section>

    <form method="POST" action="{{ route('siswa.ujian.submit', $exam->id) }}" class="space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf
        <div>
            <label for="response" class="block text-sm font-bold text-slate-800">Jawaban ujian</label>
            <p class="mt-1 text-xs text-slate-500">Tuliskan jawaban Anda. Pastikan telah diperiksa sebelum dikirim.</p>
            <textarea id="response" name="response" rows="12" required maxlength="50000" class="mt-3 w-full rounded-xl border border-slate-300 p-4 text-sm leading-6 text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">{{ old('response', $submission?->response) }}</textarea>
            @error('response')
                <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div class="flex items-center justify-between border-t border-slate-100 pt-4">
            <p class="text-xs text-slate-500">{{ $submission ? 'Pengumpulan tersimpan. Anda masih dapat memperbarui sebelum waktu berakhir.' : 'Pengumpulan akan diteruskan kepada guru untuk ditinjau.' }}</p>
            <button type="submit" class="rounded-lg bg-blue-700 px-5 py-2.5 text-xs font-bold text-white hover:bg-blue-800">{{ $submission ? 'Perbarui Jawaban' : 'Kirim Jawaban' }}</button>
        </div>
    </form>
</div>
@endsection