@extends('layouts.admin')

@section('title', ($pengumuman->exists ? 'Edit Pengumuman' : 'Buat Pengumuman') . ' - Portal Admin')

@section('content')
    <div class="mx-auto max-w-3xl space-y-5">
        <a href="{{ route('admin.pengumuman.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">
            ← Kembali ke Pengumuman
        </a>

        <div>
            <h1 class="text-xl font-bold text-slate-900">{{ $pengumuman->exists ? 'Edit Pengumuman' : 'Buat Pengumuman Baru' }}</h1>
            <p class="mt-1 text-xs text-slate-500">Tentukan penerima sebelum pengumuman dipublikasikan.</p>
        </div>

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-xs text-red-700">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ $pengumuman->exists ? route('admin.pengumuman.update', $pengumuman->id) : route('admin.pengumuman.store') }}" class="space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @if ($pengumuman->exists)
                @method('PUT')
            @endif

            <div>
                <label for="target_audience" class="mb-1 block text-xs font-bold text-slate-700">Sasaran Pengumuman</label>
                <select id="target_audience" name="target_audience" required class="w-full rounded-xl border border-slate-200 bg-slate-50 p-3 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="guru" @selected(old('target_audience', $pengumuman->target_audience ?? 'siswa') === 'guru')>Guru saja</option>
                    <option value="siswa" @selected(old('target_audience', $pengumuman->target_audience ?? 'siswa') === 'siswa')>Siswa saja</option>
                    <option value="semua" @selected(old('target_audience', $pengumuman->target_audience ?? 'siswa') === 'semua')>Guru dan siswa</option>
                </select>
                @error('target_audience')
                    <p class="mt-1 text-[10px] font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="judul" class="mb-1 block text-xs font-bold text-slate-700">Judul Pengumuman</label>
                <input id="judul" name="judul" type="text" required maxlength="255" value="{{ old('judul', $pengumuman->judul) }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 p-3 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('judul')
                    <p class="mt-1 text-[10px] font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="isi" class="mb-1 block text-xs font-bold text-slate-700">Isi Pengumuman</label>
                <textarea id="isi" name="isi" rows="7" required maxlength="5000" class="w-full rounded-xl border border-slate-200 bg-slate-50 p-3 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('isi', $pengumuman->isi) }}</textarea>
                @error('isi')
                    <p class="mt-1 text-[10px] font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-3 border-t border-slate-100 pt-4">
                <a href="{{ route('admin.pengumuman.index') }}" class="rounded-xl bg-slate-100 px-4 py-3 text-xs font-bold text-slate-600 transition hover:bg-slate-200">Batal</a>
                <button type="submit" class="rounded-xl bg-blue-600 px-5 py-3 text-xs font-bold text-white transition hover:bg-blue-700">
                    {{ $pengumuman->exists ? 'Simpan Perubahan' : 'Publikasikan' }}
                </button>
            </div>
        </form>
    </div>
@endsection