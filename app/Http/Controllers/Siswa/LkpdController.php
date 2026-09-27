@extends('layouts.siswa')

@php
    $title = $lkpd->judul;
    $subtitle = 'Pengerjaan Lembar Kerja Peserta Didik';
@endphp

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="mb-5 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">{{ $lkpd->judul }}</h1>
            <p class="mt-1 text-sm text-slate-500">Batas Waktu: {{ $lkpd->deadline?->format('d M Y H:i') }}</p>
        </div>
        <a href="{{ route('siswa.lkpd.index') }}" class="rounded-xl bg-slate-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-slate-700">
            ← Kembali
        </a>
    </div>

    @if (session('status'))
        <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-5 py-3 text-sm font-semibold text-green-700">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('siswa.lkpd.submit', $lkpd) }}" class="space-y-6">
        @csrf
        @foreach ($lkpd->questions as $index => $question)
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <h3 class="font-bold text-blue-700 mb-2">Soal No. {{ $index + 1 }}</h3>
                <p class="text-slate-800 text-sm mb-4">{{ $question->pertanyaan }}</p>

                @if ($question->gambar_path)
                    <img src="{{ asset('storage/' . $question->gambar_path) }}" alt="Gambar Soal" class="mb-4 max-h-64 rounded-xl object-contain">
                @endif

                <label class="block text-xs font-bold text-slate-600 mb-1">Jawaban Anda:</label>
                <textarea name="jawaban[{{ $question->id }}]" rows="4" required class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm" placeholder="Tuliskan jawaban Anda di sini...">{{ old("jawaban.{$question->id}", $submission->jawaban[$question->id] ?? '') }}</textarea>
            </div>
        @endforeach

        <button type="submit" class="w-full rounded-xl bg-blue-600 py-3.5 text-sm font-bold text-white hover:bg-blue-700">
            Kirim Jawaban LKPD
        </button>
    </form>
</div>
@endsection