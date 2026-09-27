@extends('layouts.app')

@php
    $title = 'Kelola LKPD';
    $subtitle = 'Buat, edit, dan pantau lembar kerja peserta didik.';
@endphp

@section('content')
<div class="mx-auto max-w-7xl">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900">Kelola LKPD</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola lembar kerja peserta didik untuk kelas yang Anda ampu.</p>
        </div>

        <a href="{{ route('guru.lkpd.create') }}" class="rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700">
            + Buat LKPD Baru
        </a>
    </div>

    @if (session('status'))
        <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-5 py-3 text-sm font-semibold text-green-700">
            {{ session('status') }}
        </div>
    @endif

    <section class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-5">
            <h2 class="text-lg font-extrabold text-blue-700">Daftar Lembar Kerja Peserta Didik</h2>
        </div>

        <div class="overflow-x-auto p-5">
            <table class="w-full min-w-[900px] border-collapse text-left">
                <thead>
                    <tr class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                        <th class="border border-slate-200 px-4 py-4">Judul Tugas / LKPD</th>
                        <th class="border border-slate-200 px-4 py-4">Kelas</th>
                        <th class="border border-slate-200 px-4 py-4">Batas Waktu</th>
                        <th class="border border-slate-200 px-4 py-4">Total Pengumpulan</th>
                        <th class="border border-slate-200 px-4 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($lkpds as $lkpd)
                        <tr class="text-sm text-slate-600 transition hover:bg-blue-50/40">
                            <td class="border border-slate-200 px-4 py-4">
                                <p class="font-bold text-slate-800">{{ $lkpd->judul }}</p>
                                @if ($lkpd->deskripsi)
                                    <p class="mt-1 line-clamp-1 text-xs text-slate-400">{{ $lkpd->deskripsi }}</p>
                                @endif
                            </td>
                            <td class="border border-slate-200 px-4 py-4">{{ $lkpd->kelas?->nama_kelas ?? '-' }}</td>
                            <td class="border border-slate-200 px-4 py-4">{{ $lkpd->deadline?->format('d M Y H:i') }}</td>
                            <td class="border border-slate-200 px-4 py-4">
                                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">
                                    {{ $lkpd->submissions_count }} Siswa
                                </span>
                            </td>
                            <td class="border border-slate-200 px-4 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('guru.lkpd.edit', $lkpd) }}" title="Edit LKPD" class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-400 text-white transition hover:bg-amber-500">
                                        ✎
                                    </a>
                                    <form method="POST" action="{{ route('guru.lkpd.destroy', $lkpd) }}" onsubmit="return confirm('Yakin ingin menghapus LKPD ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus LKPD" class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-500 text-white transition hover:bg-red-600">
                                            🗑
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="border border-slate-200 px-4 py-14 text-center text-sm text-slate-400">
                                Belum ada LKPD yang dibuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection