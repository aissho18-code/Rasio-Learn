@extends('layouts.app')

@php
    $isEdit = isset($aktivitas) && $aktivitas->exists;
    $title = $isEdit ? 'Edit Aktivitas' : 'Buat Aktivitas Baru';
    $subtitle = $isEdit ? 'Perbarui aktivitas pembelajaran untuk siswa.' : 'Buat aktivitas pembelajaran baru untuk siswa.';
@endphp

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="mb-6 flex items-center justify-between gap-3">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900">{{ $title }}</h1>
            <p class="mt-1 text-sm text-slate-500">{{ $subtitle }}</p>
        </div>

        <a href="{{ route('guru.aktivitas.index') }}" class="rounded-xl bg-slate-100 px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
            ← Kembali
        </a>
    </div>

    <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ $isEdit ? route('guru.aktivitas.update', $aktivitas) : route('guru.aktivitas.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div>
                <label class="mb-1 block text-sm font-bold text-slate-700">Judul Aktivitas</label>
                <input type="text" name="judul" value="{{ old('judul', $aktivitas->judul ?? '') }}" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" placeholder="Contoh: Praktikum Rasio dan Proporsi">
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-bold text-slate-700">Kelas</label>
                    <select name="kelas_id" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                        <option value="">Pilih kelas</option>
                        @foreach (($kelasList ?? []) as $kelas)
                            <option value="{{ $kelas->id }}" @selected(old('kelas_id', $aktivitas->kelas_id ?? null) == $kelas->id)>
                                {{ $kelas->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-bold text-slate-700">Status</label>
                    <select name="status" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                        <option value="draft" @selected(old('status', $aktivitas->status ?? 'draft') === 'draft')>Draft</option>
                        <option value="published" @selected(old('status', $aktivitas->status ?? 'draft') === 'published')>Published</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-bold text-slate-700">Tujuan Pembelajaran</label>
                <textarea name="tujuan" rows="3" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" placeholder="Tuliskan tujuan pembelajaran yang ingin dicapai siswa.">{{ old('tujuan', $aktivitas->tujuan ?? '') }}</textarea>
            </div>

            <div>
                <label class="mb-1 block text-sm font-bold text-slate-700">Petunjuk Pengerjaan</label>
                <textarea name="petunjuk" rows="3" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" placeholder="Tuliskan petunjuk pengerjaan untuk siswa.">{{ old('petunjuk', $aktivitas->petunjuk ?? '') }}</textarea>
            </div>

            <div>
                <label class="mb-1 block text-sm font-bold text-slate-700">Jenis Jawaban</label>
                <select name="respons_type" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                    <option value="text" @selected(old('respons_type', $aktivitas->tipe_penyerahan ?? 'text') === 'text')>Jawaban Teks</option>
                    <option value="file" @selected(old('respons_type', $aktivitas->tipe_penyerahan ?? 'text') === 'file')>Upload File</option>
                    <option value="both" @selected(old('respons_type', $aktivitas->tipe_penyerahan ?? 'text') === 'both')>Teks + Upload File</option>
                    <option value="interaktif" @selected(old('respons_type', $aktivitas->tipe_penyerahan ?? 'text') === 'interaktif')>Interaktif</option>
                </select>
            </div>

            <div>
                <label class="mb-1 block text-sm font-bold text-slate-700">Lampiran LKPD (opsional)</label>
                <input type="file" name="lkpd" accept=".pdf,.doc,.docx,.png,.jpg,.jpeg" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-blue-600 file:px-3 file:py-2 file:text-xs file:font-bold file:text-white hover:file:bg-blue-700">
                @if (!empty($aktivitas->lkpd_path))
                    <p class="mt-2 text-xs text-slate-500">File saat ini: {{ basename($aktivitas->lkpd_path) }}</p>
                @endif
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
                <a href="{{ route('guru.aktivitas.index') }}" class="rounded-xl bg-slate-100 px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-200">
                    Batal
                </a>
                <button type="submit" class="rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700">
                    {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Aktivitas' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
