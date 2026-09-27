<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa - Belum Masuk Kelas</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { font-family: 'Poppins', sans-serif; -webkit-font-smoothing: antialiased; }</style>
</head>
<body class="bg-[#F0F5FE] text-slate-800 h-screen w-screen overflow-hidden flex items-center justify-center select-none">
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-8 max-w-md w-full text-center space-y-4">
        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto text-xl font-bold">🏫</div>
        <h2 class="text-base font-extrabold text-gray-900">Belum Tergabung ke Kelas</h2>
        
        @if(session('no_kelas'))
            <div class="bg-amber-50 border border-amber-200 text-amber-800 text-xs p-3 rounded-xl leading-relaxed">
                {{ session('no_kelas') }}
            </div>
        @else
            <p class="text-xs text-slate-500 leading-relaxed">
                Anda masih belum masuk ke dalam kelas. Mintalah guru atau admin untuk memasukkan Anda ke dalam kelas.
            </p>
        @endif

        <div class="pt-4 border-t border-gray-100">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs py-3 rounded-xl transition shadow-xs">
                    Keluar / Logout
                </button>
            </form>
        </div>
    </div>
</body>
</html>