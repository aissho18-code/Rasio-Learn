<!DOCTYPE html>
<html lang="id" class="h-full overflow-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Buat Password Baru - Ratio Learn</title>

    <!-- Google Font: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Poppins', sans-serif; -webkit-font-smoothing: antialiased; }
    </style>
</head>
<body class="bg-[#F3F7FE] text-slate-800 min-h-screen flex items-center justify-center p-4 relative select-none"
      x-data="{ showPass: false, showPassConf: false }">

    <!-- CARD CONTAINER -->
    <div class="w-full max-w-md bg-white rounded-[28px] shadow-[0_20px_50px_rgba(37,99,235,0.07)] border border-slate-100 p-8 space-y-5 relative z-10">
        
        <div class="flex flex-col items-center text-center">
            <img src="{{ asset('images/design-login.png') }}" alt="Ratio Learn Logo" class="h-10 w-auto object-contain mb-2">
            <h1 class="text-xl font-extrabold text-slate-900">Buat Password Baru</h1>
            <p class="text-xs text-slate-400 mt-1">Mengubah password untuk akun:</p>
            <span class="mt-1.5 px-3 py-1 bg-blue-50 border border-blue-100 text-blue-700 text-xs font-bold rounded-full">
                {{ $email }}
            </span>
        </div>

        <!-- ALERT ERROR -->
        @if ($errors->any())
            <div class="p-3 bg-red-50 border-l-4 border-red-500 rounded-r-xl text-[11px] text-red-600 font-medium space-y-0.5">
                @foreach ($errors->all() as $error)
                    <p>⚠️ {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- FORM UPDATE PASSWORD -->
        <form method="POST" action="{{ route('password.direct.update') }}" class="space-y-4">
            @csrf

            <!-- Password Baru -->
            <div class="space-y-1.5">
                <label for="password" class="text-[11px] font-bold text-slate-700 tracking-wide">Password Baru</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </span>
                    <input :type="showPass ? 'text' : 'password'" id="password" name="password" required autofocus
                           placeholder="Minimal 6 karakter"
                           class="w-full pl-10 pr-10 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-300 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                    <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600">
                        <span x-text="showPass ? '🙈' : '👁️'" class="text-xs"></span>
                    </button>
                </div>
            </div>

            <!-- Konfirmasi Password -->
            <div class="space-y-1.5">
                <label for="password_confirmation" class="text-[11px] font-bold text-slate-700 tracking-wide">Ulangi Password Baru</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </span>
                    <input :type="showPassConf ? 'text' : 'password'" id="password_confirmation" name="password_confirmation" required
                           placeholder="Ketik ulang password baru..."
                           class="w-full pl-10 pr-10 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-300 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                    <button type="button" @click="showPassConf = !showPassConf" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600">
                        <span x-text="showPassConf ? '🙈' : '👁️'" class="text-xs"></span>
                    </button>
                </div>
            </div>

            <button type="submit" 
                    class="w-full py-3 bg-[#2563EB] hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md shadow-blue-500/20 transition flex items-center justify-center gap-2">
                <span>Simpan Password Baru</span>
                <span>✓</span>
            </button>
        </form>

        <div class="text-center pt-2">
            <a href="{{ route('password.direct.request') }}" class="text-xs font-bold text-slate-400 hover:text-slate-600 underline">
                Gunakan Email Lain
            </a>
        </div>

    </div>

</body>
</html>