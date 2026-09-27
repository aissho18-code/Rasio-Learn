@extends('layouts.siswa')

@php
    $title = 'Daftar Tugas Pembelajaran - Ratio Learn';
@endphp

@section('content')
<div class="space-y-6">
    <!-- PAGE TITLE HEADER -->
    <div>
        <h1 class="text-xl font-extrabold text-slate-900">Daftar Tugas Pembelajaran</h1>
        <p class="text-xs text-slate-500 mt-0.5">Pilih tugas yang ingin Anda kerjakan dan kumpulkan sebelum batas waktu.</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-xs font-medium shadow-xs">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-xs font-medium shadow-xs">
            {{ session('error') }}
        </div>
    @endif

    <!-- KONTEN DAFTAR TUGAS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($tugasList ?? [] as $tugas)
            @php
                $sub = $submissions[$tugas->id] ?? null;
                $sudahDikerjakan = $sub !== null;
            @endphp

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col justify-between space-y-4 hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <span class="bg-indigo-50 text-indigo-700 font-bold px-3 py-1 rounded-lg text-[10px]">
                        📅 {{ $tugas->pekan ?? 'Tugas Harian' }}
                    </span>

                    @if($sudahDikerjakan)
                        <span class="bg-green-50 text-green-700 font-bold px-3 py-1 rounded-full text-[10px]">
                            ✔ Terkumpul
                        </span>
                    @else
                        <span class="bg-blue-50 text-blue-700 font-bold px-3 py-1 rounded-full text-[10px]">
                            Tersedia
                        </span>
                    @endif
                </div>

                <div class="space-y-1.5">
                    <h4 class="font-bold text-gray-800 text-base line-clamp-2">{{ $tugas->judul }}</h4>
                    <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed">{{ $tugas->deskripsi ?? 'Klik untuk melihat rincian instruksi tugas.' }}</p>
                    <p class="text-[11px] text-gray-400 pt-1 font-medium">
                        ⏰ Due: {{ $tugas->tenggat_waktu ? \Carbon\Carbon::parse($tugas->tenggat_waktu)->format('D, d M Y, H:i') : '-' }}
                    </p>
                </div>

                <div class="border-t border-gray-100 pt-4 flex items-center justify-end">
                    <a href="{{ route('siswa.tugas.show', $tugas->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow-sm transition cursor-pointer">
                        Buka Tugas 🚀
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-3 bg-white rounded-2xl border border-dashed border-gray-300 p-12 text-center text-gray-400 text-xs italic">
                Belum ada tugas yang tersedia saat ini.
            </div>
        @endforelse
    </div>
</div>
@endsection