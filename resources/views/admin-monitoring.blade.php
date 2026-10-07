@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        {{-- HEADER --}}
        <div>
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-blue-600">Admin</p>
                    <h1 class="text-2xl font-extrabold text-slate-900">
                        Monitoring & Laporan
                    </h1>
                    <p class="text-sm text-slate-500 mt-1">
                        Pantau aktivitas dan perkembangan sistem pembelajaran secara keseluruhan.
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        onclick="window.location.reload()"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        ↻
                        <span>Perbarui</span>
                    </button>
                </div>
            </div>
        </div>


        {{-- 1. RINGKASAN SISTEM --}}
        <section>
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h2 class="text-base font-bold text-slate-900">
                        Ringkasan Sistem
                    </h2>
                    <p class="text-xs text-slate-500">
                        Gambaran umum data pembelajaran pada sistem.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-7 gap-3">

                @php
                    $summaryCards = [
                        ['label' => 'Siswa', 'value' => $totalSiswa ?? 0, 'icon' => '👨‍🎓'],
                        ['label' => 'Guru', 'value' => $totalGuru ?? 0, 'icon' => '👨‍🏫'],
                        ['label' => 'Kelas', 'value' => $totalKelas ?? 0, 'icon' => '🏫'],
                        ['label' => 'Materi', 'value' => $totalMateri ?? 0, 'icon' => '📚'],
                        ['label' => 'LKPD', 'value' => $totalLkpd ?? 0, 'icon' => '📝'],
                        ['label' => 'Tugas', 'value' => $totalTugas ?? 0, 'icon' => '📋'],
                        ['label' => 'Quiz/Ujian', 'value' => $totalUjian ?? 0, 'icon' => '🧾'],
                    ];
                @endphp

                @foreach ($summaryCards as $card)
                    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xl">{{ $card['icon'] }}</span>
                            <span class="text-xl font-extrabold text-slate-900">
                                {{ $card['value'] }}
                            </span>
                        </div>

                        <p class="text-xs font-medium text-slate-500 mt-3">
                            {{ $card['label'] }}
                        </p>
                    </div>
                @endforeach

            </div>
        </section>


        {{-- 2. AKTIVITAS PENGGUNA --}}
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-500">
                            Siswa Aktif
                        </p>
                        <p class="text-2xl font-extrabold text-slate-900 mt-1">
                            {{ $siswaAktif ?? 0 }}
                        </p>
                    </div>

                    <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center text-xl">
                        👨‍🎓
                    </div>
                </div>

                <p class="text-xs text-slate-400 mt-3">
                    Pengguna siswa yang melakukan aktivitas pembelajaran.
                </p>
            </div>


            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-500">
                            Guru Aktif
                        </p>
                        <p class="text-2xl font-extrabold text-slate-900 mt-1">
                            {{ $guruAktif ?? 0 }}
                        </p>
                    </div>

                    <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center text-xl">
                        👨‍🏫
                    </div>
                </div>

                <p class="text-xs text-slate-400 mt-3">
                    Guru yang melakukan aktivitas pada sistem.
                </p>
            </div>


            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-xs font-medium text-slate-500">
                            Aktivitas Terbaru
                        </p>
                        <p class="text-sm font-bold text-slate-900 mt-1">
                            Aktivitas pengguna
                        </p>
                    </div>
                </div>

                @if (!empty($aktivitasTerbaru) && count($aktivitasTerbaru))
                    <div class="space-y-2">
                        @foreach ($aktivitasTerbaru as $aktivitas)
                            <div class="text-xs border-b border-slate-100 pb-2 last:border-0">
                                <p class="font-medium text-slate-700">
                                    {{ $aktivitas->description ?? $aktivitas->activity ?? 'Aktivitas pengguna' }}
                                </p>

                                @if (!empty($aktivitas->created_at))
                                    <p class="text-slate-400 mt-0.5">
                                        {{ \Carbon\Carbon::parse($aktivitas->created_at)->format('d M Y H:i') }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-4 text-center">
                        <p class="text-xs text-slate-400">
                            Belum ada aktivitas terbaru.
                        </p>
                    </div>
                @endif
            </div>

        </section>


        {{-- 3. AKTIVITAS PEMBELAJARAN --}}
        <section>
            <div class="mb-3">
                <h2 class="text-base font-bold text-slate-900">
                    Aktivitas Pembelajaran
                </h2>
                <p class="text-xs text-slate-500">
                    Rekap aktivitas pembelajaran yang berlangsung di sistem.
                </p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                    <div class="text-2xl mb-3">📝</div>
                    <p class="text-xs text-slate-500">LKPD Dikerjakan</p>
                    <p class="text-2xl font-extrabold text-slate-900 mt-1">
                        {{ $lkpdDikerjakan ?? 0 }}
                    </p>
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                    <div class="text-2xl mb-3">📋</div>
                    <p class="text-xs text-slate-500">Tugas Dikumpulkan</p>
                    <p class="text-2xl font-extrabold text-slate-900 mt-1">
                        {{ $tugasDikumpulkan ?? 0 }}
                    </p>
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                    <div class="text-2xl mb-3">🧾</div>
                    <p class="text-xs text-slate-500">Quiz/Ujian Dikerjakan</p>
                    <p class="text-2xl font-extrabold text-slate-900 mt-1">
                        {{ $ujianDikerjakan ?? 0 }}
                    </p>
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                    <div class="text-2xl mb-3">📚</div>
                    <p class="text-xs text-slate-500">Materi Diakses</p>
                    <p class="text-2xl font-extrabold text-slate-900 mt-1">
                        {{ $materiDiakses ?? 0 }}
                    </p>
                </div>

            </div>
        </section>


        {{-- 4. STATISTIK & PERBANDINGAN --}}
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-4">

            {{-- Tingkat Penyelesaian --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="mb-5">
                    <h2 class="text-base font-bold text-slate-900">
                        Tingkat Penyelesaian Pembelajaran
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">
                        Perbandingan aktivitas yang telah diselesaikan.
                    </p>
                </div>

                @php
                    $progressItems = [
                        ['label' => 'LKPD', 'value' => $persentaseLkpd ?? 0],
                        ['label' => 'Tugas', 'value' => $persentaseTugas ?? 0],
                        ['label' => 'Quiz/Ujian', 'value' => $persentaseUjian ?? 0],
                    ];
                @endphp

                <div class="space-y-5">
                    @foreach ($progressItems as $item)
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs font-semibold text-slate-700">
                                    {{ $item['label'] }}
                                </span>

                                <span class="text-xs font-bold text-slate-700">
                                    {{ $item['value'] }}%
                                </span>
                            </div>

                            <div class="h-2.5 rounded-full bg-slate-100 overflow-hidden">
                                <div
                                    class="h-full rounded-full bg-blue-600"
                                    style="width: {{ min(100, max(0, $item['value'])) }}%">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>


            {{-- Perbandingan Kelas --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="mb-5">
                    <h2 class="text-base font-bold text-slate-900">
                        Aktivitas Berdasarkan Kelas
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">
                        Perbandingan aktivitas pembelajaran tiap kelas.
                    </p>
                </div>

                @if (!empty($aktivitasKelas) && count($aktivitasKelas))
                    <div class="space-y-4">
                        @foreach ($aktivitasKelas as $kelas)
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs font-semibold text-slate-700">
                                        {{ $kelas->nama_kelas ?? $kelas->kelas ?? '-' }}
                                    </span>

                                    <span class="text-xs text-slate-500">
                                        {{ $kelas->persentase ?? 0 }}%
                                    </span>
                                </div>

                                <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                                    <div
                                        class="h-full rounded-full bg-emerald-500"
                                        style="width: {{ min(100, max(0, $kelas->persentase ?? 0)) }}%">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-8 text-center">
                        <p class="text-sm text-slate-400">
                            Data aktivitas kelas belum tersedia.
                        </p>
                    </div>
                @endif
            </div>

        </section>


        {{-- 5. LAPORAN --}}
        <section class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <div class="mb-5">
                <h2 class="text-base font-bold text-slate-900">
                    Laporan
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    Pilih jenis laporan dan periode data yang ingin ditampilkan.
                </p>
            </div>

            <form method="GET" action="{{ url()->current() }}">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2">
                            Jenis Laporan
                        </label>

                        <select
                            name="jenis_laporan"
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
                            <option value="">Semua Laporan</option>
                            <option value="siswa">Aktivitas Siswa</option>
                            <option value="guru">Aktivitas Guru</option>
                            <option value="pembelajaran">Aktivitas Pembelajaran</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2">
                            Dari Tanggal
                        </label>

                        <input
                            type="date"
                            name="tanggal_mulai"
                            value="{{ request('tanggal_mulai') }}"
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2">
                            Sampai Tanggal
                        </label>

                        <input
                            type="date"
                            name="tanggal_selesai"
                            value="{{ request('tanggal_selesai') }}"
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none">
                    </div>

                </div>

                <div class="flex flex-wrap gap-2 mt-4">
                    <button
                        type="submit"
                        class="px-5 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">
                        Terapkan Filter
                    </button>

                    <a
                        href="{{ url()->current() }}"
                        class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Reset
                    </a>
                </div>
            </form>
        </section>


        {{-- CATATAN ADMIN --}}
        <div class="rounded-2xl border border-blue-100 bg-blue-50 px-5 py-4">
            <div class="flex gap-3">
                <div class="text-lg">ℹ️</div>

                <div>
                    <p class="text-sm font-semibold text-blue-900">
                        Informasi Monitoring
                    </p>

                    <p class="text-xs text-blue-700 mt-1 leading-relaxed">
                        Halaman ini digunakan untuk memantau dan merekap aktivitas
                        sistem. Pengelolaan data pembelajaran tetap dilakukan melalui
                        menu Kelola Pembelajaran.
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection