<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kerjakan: {{ $aktivitas->judul }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-[#F0F5FE] text-slate-800 h-screen w-screen overflow-hidden flex select-none">
    <main class="flex-1 flex flex-col h-full overflow-y-auto items-center justify-center p-6">
        <div class="mb-4 flex w-full max-w-4xl justify-end">
            <x-notification-bell />
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8 max-w-2xl w-full space-y-6">
            <div>
                <a href="{{ route('siswa.aktivitas.index') }}" class="text-xs text-blue-600 font-semibold hover:underline">← Kembali ke Daftar Aktivitas</a>
                <h2 class="text-lg font-extrabold text-slate-900 mt-2">{{ $aktivitas->judul }}</h2>
                <p class="text-xs text-slate-500 mt-1">Petunjuk: {{ $aktivitas->petunjuk ?? 'Bacalah LKPD yang dilampirkan dan serahkan jawaban Anda.' }}</p>
            </div>

            @if(session('status'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-xs font-medium">
                    {{ session('status') }}
                </div>
            @endif

            <form action="{{ route('siswa.aktivitas.submit', $aktivitas) }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                @csrf
                @if(in_array($aktivitas->respons_type, ['text', 'both']))
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Jawaban Teks</label>
                        <textarea name="text_answer" rows="4" placeholder="Tuliskan jawaban Anda di sini..." class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('text_answer', optional($submission)->text_answer) }}</textarea>
                    </div>
                @endif

                @if(in_array($aktivitas->respons_type, ['file', 'both']))
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Unggah File Jawaban (PDF, Word, Gambar)</label>
                        <input type="file" name="file" accept=".pdf,.doc,.docx,.png,.jpg,.jpeg" class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50">
                        @if(optional($submission)->file_path)
                            <p class="text-[10px] text-green-600 mt-1 font-semibold">✓ Anda sudah mengunggah file. Mengunggah baru akan menggantikan file sebelumnya.</p>
                        @endif
                    </div>
                @endif

                <div class="pt-4 border-t border-gray-100 text-right">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-3 rounded-xl shadow-sm transition">Kirim Jawaban</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>