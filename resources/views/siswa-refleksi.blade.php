<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Refleksi Pembelajaran - Ratio Learn</title>

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

    <!-- SIDEBAR SISWA -->
    @include('layouts.sidebar-siswa-compact')

    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col h-full overflow-y-auto">
        
        <header class="px-8 py-4 flex items-center justify-end gap-5">
            <x-notification-bell />

            <a href="{{ route('profile.show') }}" class="flex items-center gap-3 bg-white px-3.5 py-1.5 rounded-full shadow-xs border border-slate-100 hover:border-blue-300 transition">
                <div class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs">👤</div>
                <div class="text-left leading-tight pr-1">
                    <div class="text-xs font-bold text-slate-800">{{ Auth::user()->name ?? 'Siti Aisyah' }}</div>
                    <div class="text-[10px] text-slate-400 font-medium capitalize">{{ Auth::user()->role ?? 'Siswa' }}</div>
                </div>
            </a>
        </header>

        <div class="px-8 pb-8 space-y-6 flex-1 max-w-7xl">
            
            <div>
                <h1 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
                    💭 Refleksi Pembelajaran Siswa
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">Ungkapkan rekam pemahaman, kendala, dan pengalaman belajar Matematika kamu secara berkala.</p>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-xs font-medium shadow-xs">
                    {{ session('success') }}
                </div>
            @endif

            <!-- KARTU REFLEKSI YANG DIPUBLIKASIKAN GURU -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($refleksis as $ref)
                    @php
                        $sub = $submissions[$ref->id] ?? null;
                        $sudahMengisi = !is_null($sub);
                    @endphp

                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4 flex flex-col justify-between hover:shadow-md transition">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="bg-blue-50 text-blue-700 font-bold px-3 py-1 rounded-lg text-[10px]">
                                    📌 Pertemuan Ke-{{ $ref->pertemuan }}
                                </span>
                                
                                @if($sudahMengisi)
                                    <span class="bg-green-100 text-green-700 font-bold px-2.5 py-1 rounded-full text-[10px]">
                                        ✅ Sudah Diisi
                                    </span>
                                @else
                                    <span class="bg-amber-100 text-amber-700 font-bold px-2.5 py-1 rounded-full text-[10px]">
                                        ⏳ Belum Diisi
                                    </span>
                                @endif
                            </div>

                            <h3 class="font-bold text-gray-800 text-sm">{{ $ref->judul_refleksi }}</h3>
                            <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed">
                                {{ $ref->deskripsi ?? 'Isilah refleksi pembelajaran untuk materi ini secara jujur.' }}
                            </p>

                            @if($sudahMengisi)
                                <div class="bg-blue-50/50 p-3 rounded-xl border border-blue-100 space-y-1">
                                    <span class="text-[10px] font-bold text-blue-700 uppercase tracking-wider block">Jawaban Kamu:</span>
                                    <p class="text-xs text-gray-700 line-clamp-2 italic">
                                        "{{ $sub->answers['q1'] ?? 'Sudah diisi.' }}"
                                    </p>
                                </div>
                            @endif
                        </div>

                        <div class="border-t border-gray-100 pt-3 flex items-center justify-end space-x-2">
                            @if($sudahMengisi)
                                <a href="{{ route('siswa.refleksi.show', $ref->id) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs px-3.5 py-2 rounded-xl transition">
                                    👁️ Lihat Detail
                                </a>
                                <a href="{{ route('siswa.refleksi.show', $ref->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-3.5 py-2 rounded-xl transition">
                                    ✏️ Edit
                                </a>
                            @else
                                <a href="{{ route('siswa.refleksi.show', $ref->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2 rounded-xl transition shadow-sm w-full text-center">
                                    🚀 Isi Refleksi Pertemuan {{ $ref->pertemuan }}
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <!-- JIKA GURU BELUM MEMASUKKAN REFLEKSI -->
                    <div class="col-span-3 bg-white rounded-2xl border border-dashed border-gray-300 p-12 text-center text-gray-400 text-xs italic space-y-2">
                        <div class="text-3xl">📭</div>
                        <div class="font-bold text-gray-600">Belum Ada Refleksi Pembelajaran</div>
                        <p>Guru belum mempublikasikan instrumen refleksi untuk saat ini. Silakan periksa kembali nanti!</p>
                    </div>
                @endforelse
            </div>

        </div>
    </main>

</body>
</html>