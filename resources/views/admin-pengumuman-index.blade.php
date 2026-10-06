@extends('layouts.admin')

@section('title', 'Pengumuman - Portal Admin')

@section('content')
    <div class="mx-auto max-w-6xl space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-900">Pengumuman</h1>
                <p class="mt-1 text-xs text-slate-500">Kelola informasi dan sasaran penerimanya.</p>
            </div>
            <a href="{{ route('admin.pengumuman.create') }}" class="rounded-xl bg-blue-600 px-4 py-3 text-xs font-bold text-white transition hover:bg-blue-700">
                + Buat Pengumuman
            </a>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-xs font-medium text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="space-y-3">
            @forelse ($pengumuman as $item)
                <article class="flex flex-col justify-between gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm md:flex-row md:items-center">
                    <div class="min-w-0 flex-1 space-y-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-sm font-bold text-slate-900">{{ $item->judul }}</h2>
                            <span class="rounded-full bg-blue-100 px-2.5 py-1 text-[10px] font-bold text-blue-700">
                                {{ ['guru' => 'Guru saja', 'siswa' => 'Siswa saja', 'semua' => 'Guru dan siswa'][$item->target_audience ?? 'siswa'] ?? 'Siswa saja' }}
                            </span>
                        </div>
                        <p class="whitespace-pre-line break-words text-xs leading-relaxed text-slate-600">{{ $item->isi }}</p>
                        <p class="text-[10px] text-slate-400">
                            {{ optional($item->diterbitkan_at ?? $item->created_at)->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }} WIB
                        </p>
                    </div>

                    <div class="flex shrink-0 items-center gap-2">
                        <a href="{{ route('admin.pengumuman.edit', $item->id) }}" class="rounded-lg border border-blue-200 px-3 py-2 text-xs font-bold text-blue-600 transition hover:bg-blue-50">Edit</a>
                        <form method="POST" action="{{ route('admin.pengumuman.destroy', $item->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-lg border border-red-200 px-3 py-2 text-xs font-bold text-red-600 transition hover:bg-red-50">Hapus</button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-slate-200 bg-white px-6 py-12 text-center text-xs text-slate-400">
                    Belum ada pengumuman.
                </div>
            @endforelse
        </div>
    </div>
@endsection