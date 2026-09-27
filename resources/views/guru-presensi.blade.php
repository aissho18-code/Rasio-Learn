<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Rekapitulasi Kehadiran Siswa - Portal Guru</title>

    <!-- Google Font: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { font-family: 'Poppins', sans-serif; -webkit-font-smoothing: antialiased; }
    </style>
</head>
<body class="bg-[#F0F5FE] text-slate-800 h-screen w-screen overflow-hidden flex select-none">

    @include('layouts.sidebar-guru')

    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col h-full overflow-y-auto">
        
        <!-- HEADER TOP BAR -->
        <x-dashboard-header />

        <!-- CONTAINER KONTEN UTAMA -->
        <div class="px-8 pb-8 space-y-6 flex-1 max-w-7xl">
            
            <!-- HEADER HALAMAN -->
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Rekapitulasi Kehadiran Siswa</h1>
                <p class="text-xs text-slate-500 mt-0.5">Pantau dan unduh laporan kehadiran siswa secara real-time untuk materi Matematika - Rasio.</p>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-xs font-medium shadow-xs">
                    {{ session('success') }}
                </div>
            @endif

            <!-- TABEL REKAPITULASI KEHADIRAN -->
            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gray-50/70 border-b border-gray-200/80 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-800 text-sm">Daftar Kehadiran Siswa (Real-Time)</h3>
                        <p class="text-[11px] text-gray-500">Rekapitulasi status hadir, izin, sakit, atau alfa siswa.</p>
                    </div>
                    
                    <a href="{{ route('guru.presensi.pdf') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition shadow-sm inline-flex items-center space-x-1.5">
                        <span>📥 Unduh Laporan PDF</span>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-100/70 text-gray-600 font-semibold uppercase text-[10px] tracking-wider border-b border-gray-200">
                                <th class="py-3 px-6">Tanggal / Waktu</th>
                                <th class="py-3 px-6">Nama Siswa</th>
                                <th class="py-3 px-6 text-center">Status Kehadiran</th>
                                <th class="py-3 px-6">Keterangan / Alasan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($absensis ?? [] as $abs)
                                <tr class="hover:bg-blue-50/30 transition">
                                    <td class="py-4 px-6 text-gray-500 font-medium">
                                        {{ $abs->created_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="py-4 px-6 font-bold text-gray-800">
                                        👤 {{ $abs->siswa->name ?? 'Siswa' }}
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        @if($abs->status == 'Hadir')
                                            <span class="bg-green-100 text-green-700 font-bold px-3 py-1 rounded-full text-[10px] uppercase">Hadir</span>
                                        @elseif($abs->status == 'Izin')
                                            <span class="bg-blue-100 text-blue-700 font-bold px-3 py-1 rounded-full text-[10px] uppercase">Izin</span>
                                        @elseif($abs->status == 'Sakit')
                                            <span class="bg-amber-100 text-amber-700 font-bold px-3 py-1 rounded-full text-[10px] uppercase">Sakit</span>
                                        @else
                                            <span class="bg-red-100 text-red-700 font-bold px-3 py-1 rounded-full text-[10px] uppercase">{{ $abs->status }}</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-gray-600">
                                        {{ $abs->keterangan ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-12 text-center text-gray-400 text-xs italic">
                                        Belum ada data presensi siswa yang tercatat di sistem.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

</body>
</html>