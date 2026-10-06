@extends('layouts.app')

@php
    $routePrefix = $isAdmin ? 'admin' : 'guru';
@endphp

@section('content')
<div class="mx-auto max-w-4xl space-y-6 pb-10">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <a href="{{ route($routePrefix . '.materi.index') }}" class="text-sm font-semibold text-blue-700 hover:text-blue-800">← Kembali ke Kelola Materi</a>
            <h1 class="mt-2 text-2xl font-extrabold text-slate-900">Edit Materi</h1>
            <p class="mt-1 text-sm text-slate-500">Perbarui informasi dan publikasi materi.</p>
        </div>
        <span class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600">{{ $materi->status === 'aktif' ? 'Published' : ($materi->status === 'terkunci' ? 'Terkunci' : 'Draft') }}</span>
    </div>

    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route($routePrefix . '.materi.update', $materi->id) }}" enctype="multipart/form-data" class="space-y-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf
        @method('PUT')

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label for="kelas_id" class="mb-2 block text-sm font-semibold text-slate-700">Kelas</label>
                <select id="kelas_id" name="kelas_id" required class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    @foreach ($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" @selected(old('kelas_id', $materi->kelas_id) == $kelas->id)>{{ $kelas->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="pekan" class="mb-2 block text-sm font-semibold text-slate-700">Pekan / Pertemuan</label>
                <input id="pekan" type="text" name="pekan" value="{{ old('pekan', $materi->pekan) }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
        </div>

        <div>
            <label for="judul" class="mb-2 block text-sm font-semibold text-slate-700">Judul Materi</label>
            <input id="judul" type="text" name="judul" value="{{ old('judul', $materi->judul) }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
        </div>

        <div>
            <label for="konten" class="mb-2 block text-sm font-semibold text-slate-700">Konten</label>
            <textarea id="konten" name="konten" rows="10" class="w-full rounded-xl border border-slate-300 px-3 py-3 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">{{ old('konten', $materi->konten) }}</textarea>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label for="status" class="mb-2 block text-sm font-semibold text-slate-700">Status Materi</label>
                <select id="status" name="status" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <option value="draft" @selected(old('status', $materi->status) === 'draft')>Draft</option>
                    <option value="aktif" @selected(old('status', $materi->status) === 'aktif')>Published</option>
                    <option value="terkunci" @selected(old('status', $materi->status) === 'terkunci')>Terkunci</option>
                </select>
            </div>
            <div>
                <label for="file_materi" class="mb-2 block text-sm font-semibold text-slate-700">Ganti File Materi</label>
                <input id="file_materi" type="file" name="file_materi" accept=".pdf,.doc,.docx,.ppt,.pptx" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-600">
                @if ($materi->file_path)
                    <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($materi->file_path) }}" target="_blank" rel="noopener" class="mt-2 inline-block text-xs font-semibold text-blue-700 hover:underline">Lihat file saat ini</a>
                @endif
            </div>
        </div>

        <div class="flex flex-wrap justify-end gap-3 border-t border-slate-100 pt-5">
            <a href="{{ route($routePrefix . '.materi.index') }}" class="inline-flex items-center rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Batal</a>
            <button type="submit" class="inline-flex items-center rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection