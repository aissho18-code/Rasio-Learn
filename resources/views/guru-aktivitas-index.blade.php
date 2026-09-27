@extends('layouts.app')

@php
    $title = 'Aktivitas Guru';
    $subtitle = 'Kelola aktivitas pembelajaran dan LKPD yang telah Anda terbitkan.';
@endphp

@section('content')
<div class="mx-auto max-w-7xl">
    <div class="mb-6 flex items-center justify-between gap-3">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900">Aktivitas Guru</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola aktivitas belajar, petunjuk pengerjaan, dan pengumpulan jawaban siswa.</p>
        </div>

        <a href="{{ route('guru.aktivitas.create') }}" class="rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700">
            + Buat Aktivitas Baru
        </a>
    </div>

    @if (session('status'))
        <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-5 py-3 text-sm font-semibold text-green-700">
            {{ session('status') }}
        </div>
    @endif

    <section class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-5">
            <h2 class="text-lg font-extrabold text-blue-700">Daftar Aktivitas</h2>
        </div>

        <div class="overflow-x-auto p-5">
            <table class="w-full min-w-[900px] border-collapse text-left">
                <thead>
                    <tr class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                        <th class="border border-slate-200 px-4 py-4">Judul Aktivitas</th>
                        <th class="border border-slate-200 px-4 py-4">Kelas</th>
                        <th class="border border-slate-200 px-4 py-4">Status</th>
                        <th class="border border-slate-200 px-4 py-4">Dibuat</th>
                        <th class="border border-slate-200 px-4 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($aktivitas as $item)
                        <tr class="text-sm text-slate-600 transition hover:bg-blue-50/40">
                            <td class="border border-slate-200 px-4 py-4">
                                <p class="font-bold text-slate-800">{{ $item->judul }}</p>
                                @if ($item->tujuan)
                                    <p class="mt-1 line-clamp-2 text-xs text-slate-400">{{ Str::limit($item->tujuan, 120) }}</p>
                                @endif
                            </td>
                            <td class="border border-slate-200 px-4 py-4">
                                {{ optional($item->kelas)->nama_kelas ?? 'Semua Kelas' }}
                            </td>
                            <td class="border border-slate-200 px-4 py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-bold {{ $item->status === 'published' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $item->status === 'published' ? 'Published' : 'Draft' }}
                                </span>
                            </td>
                            <td class="border border-slate-200 px-4 py-4">
                                {{ $item->created_at?->format('d M Y H:i') ?? '-' }}
                            </td>
                            <td class="border border-slate-200 px-4 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('guru.aktivitas.edit', $item) }}" title="Edit Aktivitas" class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-400 text-white transition hover:bg-amber-500">
                                        ✎
                                    </a>
                                    <a href="{{ route('guru.aktivitas.submissions', $item) }}" title="Lihat Pengumpulan" class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-500 text-white transition hover:bg-blue-600">
                                        📥
                                    </a>
                                    <form method="POST" action="{{ route('guru.aktivitas.destroy', $item) }}" onsubmit="return confirm('Yakin ingin menghapus aktivitas ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus Aktivitas" class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-500 text-white transition hover:bg-red-600">
                                            🗑
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="border border-slate-200 px-4 py-14 text-center text-sm text-slate-400">
                                Belum ada aktivitas yang dibuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
