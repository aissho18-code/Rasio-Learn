<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $title ?? 'Dashboard Admin - Ratio Learn')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            -webkit-font-smoothing: antialiased;
        }
    </style>
</head>

<body class="h-screen overflow-hidden bg-[#F0F5FE] text-slate-800">
    <div class="flex h-screen w-screen overflow-hidden">
        <!-- Sidebar Admin -->
        @include('layouts.sidebar-admin')

        <!-- Konten Utama -->
        <div class="flex min-w-0 flex-1 flex-col overflow-hidden">
            <!-- Header Atas -->
            <header class="flex shrink-0 items-center justify-end gap-5 px-8 py-4">
                <x-notification-bell />

                <a href="{{ route('profile.show') }}" 
                   class="flex items-center gap-3 rounded-full border border-slate-100 bg-white px-3 py-1.5 shadow-xs transition hover:border-blue-300">
                    @if (auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" class="h-9 w-9 rounded-full object-cover">
                    @else
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-900 text-xs font-bold text-white">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif

                    <div class="pr-1 text-left">
                        <div class="text-xs font-bold text-slate-800">
                            {{ auth()->user()->name }}
                        </div>
                        <div class="text-[10px] text-slate-400 font-medium">
                            Administrator
                        </div>
                    </div>
                </a>
            </header>

            <!-- Tempat Mengisi Konten Halaman -->
            <main class="min-h-0 flex-1 overflow-y-auto px-8 pb-8">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>