<!DOCTYPE html>
<html lang="id" class="h-full overflow-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Ratio Learn - Belajar Rasio Jadi Seru!</title>

    <!-- Google Font: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }

        html, body {
            font-family: 'Poppins', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            height: 100% !important;
            max-height: 100dvh !important;
            overflow: hidden !important;
            touch-action: none;
        }

        /* --- MENGHILANGKAN IKON PASSWORD BAWAAN BROWSER --- */
        input::-ms-reveal,
        input::-ms-clear {
            display: none;
        }
        input[type="password"]::-webkit-credentials-auto-fill-button,
        input[type="password"]::-webkit-password-toggle-button {
            display: none !important;
            visibility: hidden;
            pointer-events: none;
        }
    </style>
</head>
<body class="antialiased bg-[#F3F7FE] text-slate-800 h-screen h-[100dvh] w-screen overflow-hidden flex items-center justify-center p-4 md:p-8 relative select-none">

    <!-- ORNAMEN DEKORASI BACKGROUND -->
    <div class="absolute -top-24 -right-24 w-96 h-96 bg-blue-200/35 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-indigo-200/25 rounded-full blur-3xl pointer-events-none"></div>
    
    <!-- Dot Pattern Pojok Kanan Atas -->
    <div class="absolute top-8 right-12 opacity-35 pointer-events-none hidden md:block">
        <svg width="80" height="80" fill="none" viewBox="0 0 80 80">
            <pattern id="dots" x="0" y="0" width="16" height="16" patternUnits="userSpaceOnUse">
                <circle cx="2.5" cy="2.5" r="2.5" class="text-blue-300" fill="currentColor"/>
            </pattern>
            <rect width="80" height="80" fill="url(#dots)" />
        </svg>
    </div>

    <!-- MAIN CONTAINER TERKUNCI -->
        <div class="w-full max-w-5xl z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center max-h-[100dvh]"
            x-data="{ showPassword: false }">
        
        <!-- KOLOM KIRI: HERO BRANDING & ILUSTRASI -->
        <div class="lg:col-span-6 flex flex-col justify-center px-2 md:px-4">
            
            <!-- 1. LOGO PRESISI DESAIN -->
            <div class="flex items-center mb-1">
                <img src="{{ asset('images/design-login.png') }}" 
                     alt="Ratio Learn Logo" 
                     class="w-[220px] md:w-[260px] lg:w-[280px] h-auto object-contain">
            </div>

            <!-- 2. JUDUL & SUBJUDUL -->
            <div class="space-y-1.5 mb-5">
                <h1 class="text-2xl md:text-[32px] font-extrabold text-[#1E293B] leading-[1.15]">
                    Selamat datang di <br>
                    <span class="text-[#2563EB]">Ratio Learn!</span>
                </h1>
                <p class="text-xs md:text-[13px] text-slate-500 leading-relaxed max-w-[360px] pt-1">
                    Media pembelajaran interaktif untuk memahami konsep rasio dengan cara yang lebih mudah dan menyenangkan.
                </p>
            </div>

            <!-- 3. ILUSTRASI SISWA BAWAH -->
            <div class="w-full max-w-[320px] md:max-w-[360px] mx-auto lg:mx-0">
                <img src="{{ asset('images/ilustrasi-siswa.png') }}" 
                     alt="Ilustrasi Ratio Learn" 
                     class="w-full h-auto object-contain drop-shadow-sm">
            </div>

        </div>

        <!-- KOLOM KANAN: CARD FORM LOGIN -->
        <div class="lg:col-span-6">
            <div class="bg-white rounded-[28px] shadow-[0_20px_50px_rgba(37,99,235,0.07)] border border-slate-100/80 p-6 md:p-8 space-y-5 relative">
                
                <div>
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">Login ke Akunmu</h2>
                    <p class="text-[11px] font-medium text-slate-400 mt-0.5">Portal ditentukan otomatis dari role yang tersimpan pada akun.</p>
                </div>

                <!-- ALERT ERROR VALIDASI -->
                @if ($errors->any())
                    <div class="p-3 bg-red-50 border-l-4 border-red-500 rounded-r-xl text-[11px] text-red-600 font-medium space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <p>⚠️ {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <!-- FORM LOGIN -->
                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <!-- Field Input Email / Username -->
                    <div class="space-y-1.5">
                        <label class="text-[11px] font-bold text-slate-700 tracking-wide">Email / Username</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </span>
                            <input type="text" 
                                   name="email" 
                                   value="{{ old('email') }}"
                                   required 
                                   autofocus 
                                   placeholder="Masukkan email atau username"
                                   class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-300 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                        </div>
                    </div>

                    <!-- Field Input Password -->
                    <div class="space-y-1.5">
                        <label class="text-[11px] font-bold text-slate-700 tracking-wide">Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </span>
                            <input :type="showPassword ? 'text' : 'password'" 
                                   name="password" 
                                   required 
                                   placeholder="Masukkan password"
                                   class="w-full pl-10 pr-10 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-300 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                            
                            <!-- Toggle Buka/Tutup Password -->
                            <button type="button" 
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!showPassword"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="showPassword" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.025 10.025 0 014.122-.963c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-8.918-8.918l8.918 8.918"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Row Ingat Saya & Lupa Password -->
                    <div class="flex items-center justify-between text-[11px] pt-1">
                        <label class="flex items-center space-x-2 cursor-pointer select-none">
                            <input type="checkbox" name="remember" class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                            <span class="text-slate-600 font-medium">Ingat saya</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-[#2563EB] font-bold hover:underline">
                                Lupa password?
                            </a>
                        @endif
                    </div>

                    <!-- Tombol Submit -->
                    <button type="submit" 
                            class="w-full py-3 bg-[#2563EB] hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md shadow-blue-500/20 transition flex items-center justify-center gap-2">
                        <span>Masuk</span>
                        <span>→</span>
                    </button>
                </form>

            </div>
        </div>

    </div>

</body>
</html>