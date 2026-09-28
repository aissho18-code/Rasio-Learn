<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Forum Diskusi - Ratio Learn</title>

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

    @if(auth()->user()->role === 'admin')
        @include('layouts.sidebar-admin')
    @elseif(auth()->user()->role === 'guru')
        @include('layouts.sidebar-guru')
    @else
        @include('layouts.sidebar-siswa-compact')
    @endif

    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col h-full overflow-y-auto">
        
        <!-- HEADER TOP BAR -->
        <x-dashboard-header />

        <!-- DASHBOARD CONTAINER -->
        <div class="px-8 pb-8 space-y-6 flex-1 max-w-5xl">
            
            <!-- PAGE TITLE HEADER -->
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">Forum Diskusi</h1>
                <p class="text-xs text-slate-500 mt-0.5">Diskusikan materi pembelajaran bersama guru dan sesama siswa secara interaktif.</p>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-xs font-medium">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-xs font-medium">
                    {{ session('error') }}
                </div>
            @endif

            <!-- FORM BUAT TOPIK DISKUSI BARU -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4">
                <h3 class="font-bold text-gray-800 text-sm">Buat Topik Diskusi Baru</h3>
                
                <form action="{{ route('diskusi.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 mb-1">Judul Topik / Pertanyaan</label>
                        <input type="text" name="judul" required placeholder="Contoh: Cara kerja rasio dalam kehidupan sehari-hari..." class="w-full text-xs border border-gray-200 rounded-xl p-3 bg-gray-50/50 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 mb-1">Isi Pesan / Pertanyaan</label>
                        <textarea name="pesan" rows="3" required placeholder="Tuliskan detail pertanyaan atau topik yang ingin didiskusikan..." class="w-full text-xs border border-gray-200 rounded-xl p-3 bg-gray-50/50 focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none"></textarea>
                    </div>

                    <div class="flex justify-end pt-1">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-6 py-2.5 rounded-xl shadow-sm transition cursor-pointer">
                            Kirim Topik &rarr;
                        </button>
                    </div>
                </form>
            </div>

            <!-- DAFTAR TOPIK DISKUSI -->
            <div class="space-y-6">
                <h3 class="font-bold text-gray-800 text-sm">Semua Topik Diskusi</h3>

                @forelse($diskusis as $diskusi)
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4">
                        <!-- Header Topik -->
                        <div class="flex justify-between items-start border-b border-gray-100 pb-3">
                            <div class="space-y-1">
                                <span class="bg-blue-50 text-blue-700 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">Diskusi Umum</span>
                                <h4 class="font-bold text-gray-800 text-base mt-1">{{ $diskusi->judul }}</h4>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="text-right">
                                    <span class="text-xs font-semibold text-gray-700 block">{{ $diskusi->user->name ?? 'Pengguna' }}</span>
                                    <span class="text-[10px] text-gray-400 block">{{ $diskusi->created_at->diffForHumans() }}</span>
                                </div>
                                
                                <!-- Tombol Hapus (Hanya muncul jika yang login Guru, Admin, atau pembuat topik) -->
                                @if(auth()->user()->role === 'guru' || auth()->user()->role === 'admin' || $diskusi->user_id === auth()->id())
                                    <form action="{{ route('diskusi.destroy', $diskusi->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus topik ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-bold p-2 bg-red-50 rounded-lg transition cursor-pointer" title="Hapus Topik">
                                            🗑️
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <!-- Isi Pesan Topik -->
                        <p class="text-xs text-gray-600 leading-relaxed bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                            {{ $diskusi->pesan }}
                        </p>

                        <!-- Tombol Reaction (Like & Dislike) -->
                        <div class="flex items-center space-x-4 pt-2">
                            @php
                                $likes = $diskusi->reactions->where('type', 'like')->count();
                                $dislikes = $diskusi->reactions->where('type', 'dislike')->count();
                                $userLike = $diskusi->reactions->where('user_id', auth()->id())->where('type', 'like')->first();
                                $userDislike = $diskusi->reactions->where('user_id', auth()->id())->where('type', 'dislike')->first();
                            @endphp

                            <!-- Tombol Like -->
                            <form action="{{ route('diskusi.reaction', $diskusi->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="type" value="like">
                                <button type="submit" class="flex items-center space-x-1 text-xs font-semibold px-3 py-1.5 rounded-xl border transition cursor-pointer {{ $userLike ? 'bg-blue-50 border-blue-200 text-blue-600' : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                                    <span>👍 Suka</span>
                                    <span class="bg-gray-100 px-1.5 py-0.5 rounded text-[10px]">{{ $likes }}</span>
                                </button>
                            </form>

                            <!-- Tombol Dislike -->
                            <form action="{{ route('diskusi.reaction', $diskusi->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="type" value="dislike">
                                <button type="submit" class="flex items-center space-x-1 text-xs font-semibold px-3 py-1.5 rounded-xl border transition cursor-pointer {{ $userDislike ? 'bg-red-50 border-red-200 text-red-600' : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                                    <span>👎 Tidak Suka</span>
                                    <span class="bg-gray-100 px-1.5 py-0.5 rounded text-[10px]">{{ $dislikes }}</span>
                                </button>
                            </form>
                        </div>

                        <!-- BAGIAN KOMENTAR / TANGGAPAN -->
                        <div class="border-t border-gray-100 pt-4 space-y-3">
                            <h5 class="font-bold text-gray-700 text-xs">Tanggapan ({{ $diskusi->komentars->count() }})</h5>

                            <!-- List Komentar -->
                            <div class="space-y-2">
                                @foreach($diskusi->komentars as $komentar)
                                    <div class="bg-gray-50 p-3 rounded-xl border border-gray-100 text-xs space-y-1">
                                        <div class="flex justify-between items-center">
                                            <span class="font-bold text-gray-800">{{ $komentar->user->name ?? 'Pengguna' }}</span>
                                            <span class="text-[10px] text-gray-400">{{ $komentar->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-gray-600">{{ $komentar->pesan }}</p>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Form Kirim Komentar / Tanggapan -->
                            <form action="{{ route('diskusi.komentar', $diskusi->id) }}" method="POST" class="flex gap-2 pt-2">
                                @csrf
                                <input type="text" name="pesan" required placeholder="Tulis tanggapan atau balasan..." class="flex-1 text-xs border border-gray-200 rounded-xl px-3 py-2 bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white font-bold text-xs px-4 py-2 rounded-xl transition whitespace-nowrap cursor-pointer">
                                    Kirim Balasan
                                </button>
                            </form>
                        </div>

                    </div>
                @empty
                    <div class="bg-white p-8 rounded-2xl border border-gray-200 text-center text-gray-400 text-xs italic">
                        Belum ada topik diskusi yang dibuat. Jadilah yang pertama membuat diskusi!
                    </div>
                @endforelse
            </div>

        </div>
    </main>

</body>
</html>