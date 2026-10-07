<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Detail Refleksi - Ratio Learn</title>

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
                    <div class="text-xs font-bold text-slate-800">{{ Auth::user()->name ?? 'Siswa' }}</div>
                    <div class="text-[10px] text-slate-400 font-medium capitalize">{{ Auth::user()->role ?? 'Siswa' }}</div>
                </div>
            </a>
        </header>

        <div class="px-8 pb-8 space-y-6 flex-1 max-w-4xl">
            <div>
                <a href="{{ route('siswa.refleksi.index') }}" class="text-xs font-bold text-blue-600 hover:underline mb-1 inline-block">
                    &larr; Kembali ke Daftar Refleksi
                </a>

                <h1 class="text-xl font-extrabold text-slate-900">
                    Refleksi — Pertemuan {{ $refleksi->pertemuan }}
                </h1>

                <p class="text-xs text-slate-500 mt-1">
                    Ceritakan pemahaman dan pengalaman belajarmu pada pertemuan ini.
                </p>
            </div>

            @php
                $answers = $submission?->answers ?? [];
                $catatan = $submission?->catatan ?? '';
            @endphp

            <form action="{{ route('siswa.refleksi.store', $refleksi->id) }}" method="POST"
                  class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 space-y-5">
                @csrf

                <!-- Pertanyaan 1 -->
                <div class="space-y-2">
                    <label for="q1" class="block text-sm font-bold text-slate-800">
                        1. Apa hal baru yang paling kamu pahami?
                    </label>

                    <textarea
                        id="q1"
                        name="q1"
                        rows="4"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 placeholder-slate-400 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition"
                        placeholder="Tuliskan hal baru yang kamu pahami...">{{ old('q1', $answers['q1'] ?? '') }}</textarea>

                    @error('q1')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Pertanyaan 2 -->
                <div class="space-y-2">
                    <label for="q2" class="block text-sm font-bold text-slate-800">
                        2. Bagian mana yang masih membingungkan?
                    </label>

                    <textarea
                        id="q2"
                        name="q2"
                        rows="4"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 placeholder-slate-400 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition"
                        placeholder="Tuliskan bagian yang masih membingungkan...">{{ old('q2', $answers['q2'] ?? '') }}</textarea>

                    @error('q2')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Pertanyaan 3 -->
                <div class="space-y-2">
                    <label for="q3" class="block text-sm font-bold text-slate-800">
                        3. Apa yang kamu lakukan ketika mengalami kesulitan?
                    </label>

                    <textarea
                        id="q3"
                        name="q3"
                        rows="4"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 placeholder-slate-400 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition"
                        placeholder="Ceritakan apa yang kamu lakukan ketika mengalami kesulitan...">{{ old('q3', $answers['q3'] ?? '') }}</textarea>

                    @error('q3')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Pertanyaan 4 -->
                <div class="space-y-2">
                    <label for="q4" class="block text-sm font-bold text-slate-800">
                        4. Bagian mana yang ingin kamu pelajari kembali?
                    </label>

                    <textarea
                        id="q4"
                        name="q4"
                        rows="4"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 placeholder-slate-400 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition"
                        placeholder="Tuliskan bagian yang ingin kamu pelajari kembali...">{{ old('q4', $answers['q4'] ?? '') }}</textarea>

                    @error('q4')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Catatan -->
                <div class="space-y-2">
                    <label for="catatan" class="block text-sm font-bold text-slate-800">
                        Catatan Tambahan <span class="font-normal text-slate-400">(opsional)</span>
                    </label>

                    <textarea
                        id="catatan"
                        name="catatan"
                        rows="3"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 placeholder-slate-400 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition"
                        placeholder="Tambahkan catatan jika ada...">{{ old('catatan', $catatan) }}</textarea>

                    @error('catatan')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tombol -->
                <div class="pt-4 border-t border-gray-100 flex items-center gap-3">
                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow-sm transition">
                        💾 Simpan Refleksi
                    </button>

                    <a
                        href="{{ route('siswa.refleksi.index') }}"
                        class="text-xs font-semibold text-gray-500 hover:text-blue-600 hover:underline">
                        Kembali
                    </a>
                </div>
            </form>
        </div>
    </main>

</body>
</html>