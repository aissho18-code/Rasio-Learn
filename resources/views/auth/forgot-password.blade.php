<!DOCTYPE html>
<html lang="id" class="h-full overflow-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Lupa Password - Ratio Learn</title>

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
<body class="bg-[#F3F7FE] text-slate-800 min-h-screen flex items-center justify-center p-4 relative select-none">

    <!-- CARD CONTAINER -->
    <div class="w-full max-w-md bg-white rounded-[28px] shadow-[0_20px_50px_rgba(37,99,235,0.07)] border border-slate-100 p-8 space-y-5 relative z-10">
        
        <!-- LOGO BRANDING -->
        <div class="flex flex-col items-center text-center">
            <img src="{{ asset('images/design-login.png') }}" alt="Ratio Learn Logo" class="h-10 w-auto object-contain mb-2">
            <h1 class="text-xl font-extrabold text-slate-900">Lupa Password?</h1>
            <p class="text-xs text-slate-400 mt-1">Masukkan email terdaftar akun Anda untuk melanjutkan pembuatan password baru.</p>
        </div>

        <!-- ALERT ERROR -->
        @if ($errors->any())
            <div class="p-3 bg-red-50 border-l-4 border-red-500 rounded-r-xl text-[11px] text-red-600 font-medium space-y-0.5">
                @foreach ($errors->all() as $error)
                    <p>⚠️ {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- FORM VERIFIKASI EMAIL -->
        <form method="POST" action="{{ route('password.direct.verify') }}" class="space-y-4">
            @csrf

            <div class="space-y-1.5">
                <label for="email" class="text-[11px] font-bold text-slate-700 tracking-wide">Email Pengguna</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </span>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                           placeholder="Masukkan email terdaftar..."
                           class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-300 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                </div>
            </div>

            <button type="submit" 
                    class="w-full py-3 bg-[#2563EB] hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md shadow-blue-500/20 transition flex items-center justify-center gap-2">
                <span>Lanjutkan</span>
                <span>→</span>
            </button>
        </form>

        <div class="text-center pt-2">
            <a href="{{ route('login') }}" class="text-xs font-bold text-blue-600 hover:underline">
                ← Kembali ke Login
            </a>
        </div>

    </div>

</body>
</html>