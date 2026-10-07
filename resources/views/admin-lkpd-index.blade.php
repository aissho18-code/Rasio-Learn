@extends('layouts.app')

@section('title', 'Kelola LKPD')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Kelola LKPD</h1>
            <p class="text-sm text-slate-500 mt-1">
                Kelola seluruh LKPD yang dibuat oleh Guru.
            </p>
        </div>

        <a href="{{ route('admin.lkpd.create') }}"
           class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
            + Tambah LKPD
        </a>
    </div>

    {{-- Pesan sukses --}}
    @if(session('success'))
        <div class="mb-5 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tabel LKPD --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-4 text-left font-semibold text-slate-600">No</th>
                        <th class="px-5 py-4 text-left font-semibold text-slate-600">Judul LKPD</th>
                        <th class="px-5 py-4 text-left font-semibold text-slate-600">Guru</th>
                        <th class="px-5 py-4 text-left font-semibold text-slate-600">Kelas</th>
                        <th class="px-5 py-4 text-left font-semibold text-slate-600">Deadline</th>
                        <th class="px-5 py-4 text-left font-semibold text-slate-600">Pengumpulan</th>
                        <th class="px-5 py-4 text-center font-semibold text-slate-600">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($lkpds as $index => $lkpd)
                        <tr class="hover:bg-slate-50">

                            <td class="px-5 py-4 text-slate-500">
                                {{ $index + 1 }}
                            </td>

                            <td class="px-5 py-4">
                                <div class="font-semibold text-slate-800">
                                    {{ $lkpd->judul }}
                                </div>

                                @if($lkpd->deskripsi)
                                    <div class="text-xs text-slate-500 mt-1 line-clamp-2">
                                        {{ $lkpd->deskripsi }}
                                    </div>
                                @endif
                            </td>

                            <td class="px-5 py-4 text-slate-600">
                                {{ $lkpd->guru->name ?? '-' }}
                            </td>

                            <td class="px-5 py-4 text-slate-600">
                                {{ $lkpd->kelas->nama_kelas ?? '-' }}
                            </td>

                            <td class="px-5 py-4 text-slate-600">
                                {{ $lkpd->deadline ? \Carbon\Carbon::parse($lkpd->deadline)->format('d/m/Y H:i') : '-' }}
                            </td>

                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                    {{ $lkpd->submissions_count ?? 0 }} siswa
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-2">

                                    <a href="{{ route('admin.lkpd.edit', $lkpd) }}"
                                       class="rounded-lg bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-700 hover:bg-amber-100">
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.lkpd.destroy', $lkpd) }}"
                                          method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus LKPD ini?');">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="rounded-lg bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-100">
                                            Hapus
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-slate-500">
                                Belum ada LKPD.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

</div>
@endsection