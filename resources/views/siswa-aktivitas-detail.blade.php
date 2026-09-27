<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $aktivitas->judul }} - Siswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-slate-50 p-6">
    <div class="max-w-4xl mx-auto space-y-6">
        @if(session('success'))
            <div class="p-4 bg-emerald-100 text-emerald-800 rounded-xl font-bold text-sm">{{ session('success') }}</div>
        @endif

        <div class="bg-white p-6 rounded-2xl border shadow-sm">
            <span class="text-xs bg-blue-100 text-blue-700 font-bold px-3 py-1 rounded-full">LKPD Interaktif</span>
            <h1 class="text-2xl font-bold text-slate-800 mt-2">{{ $aktivitas->judul }}</h1>
            <p class="text-xs text-slate-500 mt-1">{{ $aktivitas->tujuan }}</p>
        </div>

        <form method="POST" action="{{ route('siswa.aktivitas.submit', $aktivitas->id) }}" class="space-y-6">
            @csrf

            @foreach($aktivitas->blocks as $block)
                @php $config = $block->konfigurasi ?? []; @endphp
                <div class="bg-white p-6 rounded-2xl border shadow-sm space-y-3">
                    <h3 class="font-bold text-sm text-slate-800 border-b pb-2">{{ $block->judul }}</h3>

                    @if($block->tipe === 'text')
                        <p class="text-sm text-slate-600">{!! nl2br(e($config['content'] ?? '')) !!}</p>
                    @endif

                    @if($block->tipe === 'dropdown')
                        <div class="space-y-2">
                            <label class="block text-xs font-semibold text-slate-700">{{ $config['question_text'] ?? '' }}</label>
                            @php $options = array_map('trim', explode(',', $config['options_text'] ?? '')); @endphp
                            <select name="jawaban[block_{{ $block->id }}]" class="w-full p-2 text-xs border rounded-xl">
                                <option value="">Pilih Jawaban</option>
                                @foreach($options as $opt)
                                    <option value="{{ $opt }}" @selected(isset($submission->jawaban['block_'.$block->id]) && $submission->jawaban['block_'.$block->id] == $opt)>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    @if($block->tipe === 'reflection')
                        <div class="space-y-2">
                            <label class="block text-xs font-semibold text-purple-900">Tuliskan refleksi Anda:</label>
                            <textarea name="jawaban[block_{{ $block->id }}]" rows="3" class="w-full p-2 text-xs border rounded-xl" placeholder="Ketikkan refleksi...">{{ $submission->jawaban['block_'.$block->id] ?? '' }}</textarea>
                        </div>
                    @endif
                </div>
            @endforeach

            <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-lg">Kirim Jawaban LKPD</button>
        </form>
    </div>
</body>
</html>