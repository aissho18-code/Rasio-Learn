<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>LKPD - Ratio Learn</title>

    <!-- Google Font: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js CDN untuk Real-time Polling Sync -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Poppins', sans-serif; -webkit-font-smoothing: antialiased; }
    </style>
</head>
<body class="bg-[#F0F5FE] text-slate-800 h-screen w-screen overflow-hidden flex select-none" 
      x-data="aktivitasRealtime()" 
      x-init="initPolling()">

    <!-- SIDEBAR SISWA -->
    @include('layouts.sidebar-siswa-compact')

    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col h-full overflow-y-auto">
        
        <!-- HEADER TOP BAR -->
        <x-dashboard-header />

        <!-- CONTAINER CONTENT -->
        <div class="px-8 py-8 space-y-6 flex-1 max-w-7xl">
            
            <!-- HEADER PAGE TITLE -->
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-xs flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <span>📚</span> Aktivitas Pembelajaran & LKPD
                    </h1>
                    <p class="text-xs text-slate-500 mt-1">Unduh lembar kerja peserta didik (LKPD) dan serahkan jawaban tugas Anda di sini.</p>
                </div>
                <button type="button" @click="fetchAktivitas()" :disabled="isSyncing" aria-label="Sinkronkan aktivitas dan LKPD" title="Sinkronkan aktivitas dan LKPD" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 transition hover:border-blue-200 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-wait disabled:opacity-60">
                    <svg :class="{'animate-spin': isSyncing}" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M20 7v5h-5" />
                        <path d="M20 12a8 8 0 1 1-2.34-5.66L20 9" />
                    </svg>
                </button>
            </div>

            <!-- NOTIFIKASI AKTIVITAS BARU -->
            <div x-show="hasNewActivity" x-transition class="bg-blue-600 text-white p-4 rounded-2xl shadow-md flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-xl">🔔</span>
                    <span class="text-xs font-bold">Guru baru saja menerbitkan Aktivitas/LKPD baru!</span>
                </div>
                <button @click="hasNewActivity = false" class="text-xs bg-white text-blue-700 font-bold px-3 py-1.5 rounded-xl">Lihat</button>
            </div>

            <!-- DAFTAR AKTIVITAS REAL-TIME -->
            <div class="space-y-4">
                <template x-for="a in listAktivitas" :key="a.id">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 transition hover:border-blue-200">
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm" x-text="a.judul"></h3>
                            <p class="text-xs text-slate-500 mt-1" x-text="a.tujuan || 'Tidak ada deskripsi.'"></p>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="text-[10px] bg-blue-50 text-blue-600 font-bold px-2.5 py-0.5 rounded-full" x-text="'Guru: ' + a.guru_name"></span>
                                <span class="text-[10px] bg-slate-100 text-slate-600 font-bold px-2.5 py-0.5 rounded-full uppercase" x-text="a.type === 'lkpd' ? 'LKPD' : 'Tipe: ' + a.respons_type"></span>
                                <span class="text-[10px] text-slate-400" x-text="a.created_at_formatted"></span>
                            </div>
                        </div>
                        <div class="flex gap-2 shrink-0">
                            <template x-if="a.has_lkpd">
                                <a :href="a.download_url" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-4 py-2.5 rounded-xl transition flex items-center gap-1.5">
                                    <span>📄</span> Download LKPD
                                </a>
                            </template>
                            <template x-if="a.type === 'lkpd' && a.is_submitted">
    <div class="flex items-center gap-2">
        <span class="bg-emerald-50 text-emerald-700 text-xs font-bold px-3 py-2.5 rounded-xl">
            ✓ Sudah dikerjakan
        </span>

        <a :href="a.show_url + '?review=1'" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-xs transition">
            Review
        </a>
    </div>
</template>

<template x-if="a.type !== 'lkpd' || !a.is_submitted">
    <a :href="a.show_url" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-xs transition flex items-center gap-1.5">
        <span x-text="a.type === 'lkpd' ? 'Kerjakan LKPD' : 'Kerjakan Aktivitas'"></span>
    </a>
</template>
                        </div>
                    </div>
                </template>

                <!-- PEMBERITAHUAN JIKA KOSONG -->
                <div x-show="listAktivitas.length === 0" class="bg-white rounded-2xl p-8 text-center text-slate-400 text-xs border border-gray-100">
                    Belum ada aktivitas yang diterbitkan untuk kelas Anda.
                </div>
            </div>

        </div>
    </main>

    <script>
        function aktivitasRealtime() {
            return {
                listAktivitas: [],
                isSyncing: false,
                hasNewActivity: false,
                previousCount: 0,
                
                initPolling() {
                    this.fetchAktivitas();
                    // Polling data otomatis setiap 4 detik untuk update real-time dari Guru
                    setInterval(() => {
                        this.fetchAktivitas();
                    }, 4000);
                },

                async fetchAktivitas() {
                    this.isSyncing = true;
                    try {
                        const res = await fetch('{{ route("siswa.aktivitas.api") }}');
                        const json = await res.json();
                        if (json.success) {
                            if (this.previousCount > 0 && json.data.length > this.previousCount) {
                                this.hasNewActivity = true;
                            }
                            this.listAktivitas = json.data;
                            this.previousCount = json.data.length;
                        }
                    } catch (e) {
                        console.error('Realtime sync error:', e);
                    } finally {
                        setTimeout(() => { this.isSyncing = false; }, 500);
                    }
                }
            }
        }
    </script>
</body>
</html>