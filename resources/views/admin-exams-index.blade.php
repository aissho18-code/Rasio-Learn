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
            <p class="text-2xl font-extrabold text-green-600 mt-1">{{ $exams->where('locked', false)->count() }}</p>
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
                            {{ $exam->locked ? '🔒 TERKUNCI' : '✅ AKTIF' }}
                        </span>
                        <span class="text-[10px] text-slate-400 font-medium">
                            Oleh: {{ $exam->creator?->name ?? 'Admin' }}
                        </span>
                    </div>

                    <h2 class="mt-3 text-sm font-extrabold text-slate-800 line-clamp-1">{{ $exam->title }}</h2>
                    <p class="mt-1 text-xs text-slate-500 line-clamp-2 leading-relaxed">{{ $exam->description ?? 'Tidak ada deskripsi.' }}</p>
                </div>

                <div class="pt-4 border-t border-slate-100 space-y-3">
                    <div class="flex items-center justify-between text-[11px] text-slate-400 font-medium">
                        <span>Kelas: {{ $exam->kelas?->nama_kelas ?? 'Semua Kelas' }}</span>
                        <span>Max Pelanggaran: {{ $exam->max_violations ?? 3 }}</span>
                    </div>

                    <div class="flex items-center justify-between gap-2 pt-1">
                        <a href="{{ route('admin.exams.edit', $exam->id) }}" class="rounded-lg px-3 py-1.5 text-xs font-bold text-blue-600 bg-blue-50 border border-blue-100 hover:bg-blue-100 transition">
                            Edit
                        </a>

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
</div>
@endsection