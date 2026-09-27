@extends('layouts.app')

@php
    $title = 'Kelola LKPD';
    $subtitle = 'Kelola LKPD dan pantau progres pengumpulan siswa.';
@endphp

@section('content')
<div class="mx-auto max-w-7xl">
    <div class="mb-6 flex items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">Kelola LKPD</h1>
        </div>

        <a href="{{ route('guru.lkpd.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700">
            <span>＋</span>
            <span>Buat LKPD Baru</span>
        </a>
    </div>

    @if (session('status'))
        <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-700">
            {{ session('status') }}
        </div>
    @endif

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 bg-slate-50/90 px-6 py-5">
            <h2 class="text-[15px] font-extrabold text-blue-700">Daftar Lembar Kerja Peserta Didik</h2>
        </div>

        <div class="overflow-x-auto p-0">
            <table class="w-full min-w-[900px] border-collapse text-left">
                <thead>
                    <tr class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                        <th class="border border-slate-200 px-4 py-4">Judul Tugas / LKPD</th>
                        <th class="border border-slate-200 px-4 py-4">Kelas</th>
                        <th class="border border-slate-200 px-4 py-4">Batas Waktu</th>
                        <th class="border border-slate-200 px-4 py-4">Total Pengumpulan</th>
                        <th class="border border-slate-200 px-4 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($lkpds as $lkpd)
                        <tr class="align-middle text-sm text-slate-700 hover:bg-blue-50/40">
                            <td class="border border-slate-200 px-4 py-4">
                                <div class="font-bold text-slate-800">{{ $lkpd->judul }}</div>
                                @if ($lkpd->deskripsi)
                                    <div class="mt-1 line-clamp-1 text-xs text-slate-400">{{ $lkpd->deskripsi }}</div>
                                @endif
                            </td>
                            <td class="border border-slate-200 px-4 py-4">{{ $lkpd->kelas?->nama_kelas ?? '-' }}</td>
                            <td class="border border-slate-200 px-4 py-4">{{ $lkpd->deadline?->format('d M Y H:i') }}</td>
                            <td class="border border-slate-200 px-4 py-4">
                                <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">
                                    {{ $lkpd->submissions_count }} Siswa
                                </span>
                            </td>
                            <td class="border border-slate-200 px-4 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('guru.lkpd.edit', $lkpd) }}" title="Edit LKPD" class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-400 text-white shadow-sm transition hover:bg-amber-500">
                                        ✎
                                    </a>
                                    <form method="POST" action="{{ route('guru.lkpd.destroy', $lkpd) }}" onsubmit="return confirm('Yakin ingin menghapus LKPD ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus LKPD" class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-500 text-white shadow-sm transition hover:bg-red-600">
                                            🗑
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="border border-slate-200 px-4 py-16 text-center text-sm text-slate-400">
                                Belum ada LKPD yang dibuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @foreach ($lkpds as $lkpd)
        @if ($lkpd->submissions->isNotEmpty())
            <section class="space-y-3 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-bold text-slate-800">Jawaban LKPD: {{ $lkpd->judul }}</h2>
                @foreach ($lkpd->submissions as $submission)
                    <details class="rounded-xl border border-slate-200 p-4">
                        <summary class="cursor-pointer text-xs font-semibold text-slate-700">
                            {{ $submission->siswa?->name ?? 'Siswa' }} · {{ $submission->submitted_at?->diffForHumans() }}
                        </summary>
                        <div class="mt-3 space-y-3 text-xs leading-5 text-slate-600">
                            @foreach ((array) $submission->jawaban as $questionId => $answer)
                                <div>
                                    <p class="font-semibold text-slate-800">{{ $lkpd->questions->firstWhere('id', $questionId)?->pertanyaan ?? 'Jawaban' }}</p>
                                    <p class="mt-1 whitespace-pre-wrap">{{ $answer }}</p>
                                </div>
                            @endforeach
                        </div>
                    </details>
                @endforeach
            </section>
        @endif
    @endforeach
</div>
@endsection