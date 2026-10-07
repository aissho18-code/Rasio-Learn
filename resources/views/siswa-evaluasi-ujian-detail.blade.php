<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Detail Evaluasi & Hasil Ujian - Ratio Learn</title>

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
        
        <!-- HEADER TOP BAR -->
        <header class="px-8 py-4 flex items-center justify-end gap-5">
            <x-notification-bell />

            <a href="{{ route('profile.show') }}" class="flex items-center gap-3 bg-white px-3.5 py-1.5 rounded-full shadow-xs border border-slate-100 hover:border-blue-300 transition">
                <div class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs">👤</div>
                <div class="text-left leading-tight pr-1">
                    <div class="text-xs font-bold text-slate-800">{{ Auth::user()->name ?? 'Siti Aisyah' }}</div>
                    <div class="text-[10px] text-slate-400 font-medium">Siswa Kelas VII</div>
                </div>
            </a>
        </header>

        <!-- DASHBOARD CONTAINER -->
        <div class="px-8 pb-8 space-y-6 flex-1 max-w-4xl mx-auto w-full">
            
            <!-- PAGE TITLE HEADER -->
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Detail Evaluasi & Hasil Ujian</h1>
                <p class="text-xs text-slate-500 mt-0.5">Tinjau rekapitulasi jawaban benar/salah dan rekomendasi belajar Anda.</p>
            </div>

            <!-- KARTU RINGKASAN NILAI -->
            <div class="bg-white rounded-2xl border-t-8 border-t-blue-600 border border-gray-200 shadow-sm p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <a href="{{ route('siswa.evaluasi') }}" class="text-xs font-bold text-blue-600 hover:underline mb-2 block">&larr; Kembali ke Daftar Evaluasi</a>
                    <h3 class="font-bold text-gray-800 text-base">Ujian: {{ $sub->ujian->judul_ujian }}</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Tanggal Pengerjaan: {{ $sub->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div class="bg-blue-50 border border-blue-100 px-5 py-3 rounded-2xl text-center">
                    <span class="text-[11px] font-semibold text-blue-600 block">Nilai Akhir Anda</span>
                    <span class="text-2xl font-bold text-blue-700">{{ $sub->nilai }} / 100</span>
                </div>
            </div>

            <!-- REKAPITULASI JAWABAN (BENAR / SALAH & KUNCI JAWABAN) -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-6">
                <h4 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-3">Rincian Koreksi Jawaban</h4>

                <div class="space-y-4">
                    @foreach($sub->ujian->questions ?? [] as $index => $q)
                        @php
                            $studentAns = trim(strtolower((string)($sub->jawaban[$index] ?? '')));
                            $correctAns = trim(strtolower((string)($q['answer'] ?? '')));
                            $isCorrect = ($studentAns !== '' && $correctAns !== '' && ($studentAns === $correctAns || str_contains($studentAns, $correctAns) || str_contains($correctAns, $studentAns)));
                        @endphp

                        <div class="p-4 rounded-xl border {{ $isCorrect ? 'bg-green-50/50 border-green-200' : 'bg-red-50/50 border-red-200' }} space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-gray-800 text-xs">{{ $index + 1 }}. {{ $q['text'] }}</span>
                                @if($isCorrect)
                                    <span class="bg-green-100 text-green-700 font-bold px-2 py-0.5 rounded text-[10px]">✔ Benar</span>
                                @else
                                    <span class="bg-red-100 text-red-700 font-bold px-2 py-0.5 rounded text-[10px]">✖ Salah</span>
                                @endif
                            </div>

                            <div class="text-xs text-gray-700">
                                💬 Jawaban Anda: <span class="font-semibold">{{ $sub->jawaban[$index] ?? 'Tidak dijawab' }}</span>
                            </div>

                            @if(!$isCorrect)
                                <div class="text-xs text-green-700 bg-white p-2.5 rounded-lg border border-green-100">
                                    🔑 Kunci Jawaban yang Benar: <span class="font-bold">{{ $q['answer'] ?? '-' }}</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- LAPORAN REKOMENDASI BELAJAR-->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-3">
                <h4 class="font-bold text-blue-800 text-sm flex items-center space-x-1">
                    <span>Laporan Rekomendasi Belajar</span>
                </h4>
                <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100 text-xs text-gray-800 leading-relaxed">
                    {{ $sub->rekomendasi_belajar ?? 'Belum ada rekomendasi khusus yang diberikan.' }}
                </div>
            </div>

        </div>
    </main>

</body>
</html>