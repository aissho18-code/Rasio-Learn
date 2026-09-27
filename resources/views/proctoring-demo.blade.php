<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Simulasi Anti Kecurangan CBT - Proctoring Demo</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.tsx'])
    <style>body { font-family: 'Poppins', sans-serif; -webkit-font-smoothing: antialiased; }</style>
</head>
<body class="bg-slate-100 h-screen w-screen overflow-hidden select-none">
    <!-- ID ini harus persis sama dengan yang dipanggil di app.tsx -->
    <div id="proctoring-app" class="h-full w-full"></div>
</body>
</html>