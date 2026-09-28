<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Dashboard Guru - Ratio Learn</title>

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

    @include('layouts.sidebar-guru')

    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col h-full overflow-y-auto">
        
        <!-- HEADER TOP BAR -->
        <x-dashboard-header />

        <!-- DASHBOARD CONTAINER -->
        <div class="px-8 pb-8 space-y-6 flex-1 max-w-7xl">
            
            <!-- PAGE TITLE & FILTER KELAS -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl font-extrabold text-slate-900">Dashboard Guru</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola kelas, buat pengumuman, dan tinjau hasil penilaian siswa secara terpusat.</p>
                </div>

                <!-- FORM FILTER KELAS -->
                <form method="GET" action="{{ route('guru.dashboard') }}" class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-600">Filter Kelas:</span>
                    <select name="kelas_id" onchange="this.form.submit()" class="bg-white border border-slate-200 text-slate-800 text-xs font-bold px-3 py-2 rounded-xl focus:ring-2 focus:ring-blue-300 outline-none cursor-pointer">
                        <option value="">Semua Kelas</option>
                        @foreach ($kelasList as $k)
                            <option value="{{ $k->id }}" @selected($selectedKelasId == $k->id)>
                                {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <!-- NOTIFIKASI SUKSES -->
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-xs font-medium shadow-xs">
                    {{ session('success') }}
                </div>
            @endif

            <!-- STATISTIK RINGKAS -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs space-y-1">
                    <span class="text-2xl">🏫</span>
                    <div class="text-2xl font-extrabold text-slate-900">{{ $totalKelas }}</div>
                    <div class="text-xs font-semibold text-slate-400">Total Kelas</div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs space-y-1">
                    <span class="text-2xl">👨‍🎓</span>
                    <div class="text-2xl font-extrabold text-slate-900">{{ $totalSiswa }}</div>
                    <div class="text-xs font-semibold text-slate-400">Siswa Terdaftar</div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs space-y-1">
                    <span class="text-2xl">🎮</span>
                    <div class="text-2xl font-extrabold text-slate-900">{{ $totalAktivitas }}</div>
                    <div class="text-xs font-semibold text-slate-400">Aktivitas & LKPD</div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs space-y-1">
                    <span class="text-2xl">📥</span>
                    <div class="text-2xl font-extrabold text-amber-600">{{ $totalPendingSubmissions }}</div>
                    <div class="text-xs font-semibold text-slate-400">Tugas Perlu Dinilai</div>
                </div>
            </div>

            <section class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 space-y-4" aria-label="Status siswa per kelas">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">Status Siswa</h2>
                        <p class="text-xs text-slate-400">Status siswa yang terdaftar pada kelas yang Anda ampu.</p>
                    </div>
                    <div class="text-xs font-semibold text-slate-500">
                        <span class="text-green-700">● {{ $totalAktifSiswa }} Aktif</span>
                        <span class="mx-1">|</span>
                        <span>○ {{ $totalSiswa - $totalAktifSiswa }} Tidak aktif</span>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    @forelse($kelasMonitoring as $kelas)
                        @php($kelasAktif = $kelas->siswa->filter(fn ($siswa) => $siswa->isOnline())->count())
                        <div class="rounded-xl border border-slate-200 overflow-hidden" data-class-monitoring="{{ $kelas->id }}">
                            <div class="bg-slate-50 px-4 py-3 flex items-center justify-between gap-3">
                                <h3 class="text-xs font-bold text-slate-800">{{ $kelas->nama_kelas }}</h3>
                                <div class="text-[10px] text-slate-500" data-class-summary>
                                    {{ $kelas->siswa->count() }} Siswa | <span class="text-green-700">● {{ $kelasAktif }} Aktif</span> | ○ {{ $kelas->siswa->count() - $kelasAktif }} Tidak aktif
                                </div>
                            </div>
                            <div class="divide-y divide-slate-100">
                                @forelse($kelas->siswa as $siswa)
                                    <div class="px-4 py-3 flex items-center justify-between gap-3" data-student-id="{{ $siswa->id }}">
                                        <span class="text-xs font-semibold text-slate-700">{{ $siswa->name }}</span>
                                        <div class="text-right">
                                            <span data-student-status class="block text-[11px] font-bold {{ $siswa->isOnline() ? 'text-green-700' : 'text-slate-500' }}">{{ $siswa->isOnline() ? '● Aktif' : '○ Tidak aktif' }}</span>
                                            <span data-student-last-active class="block text-[10px] text-slate-400">{{ $siswa->lastActiveLabel() }}</span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="px-4 py-5 text-center text-xs text-slate-400">Belum ada siswa terdaftar di kelas ini.</div>
                                @endforelse
                            </div>
                        </div>
                    @empty
                        <div class="md:col-span-2 rounded-xl border border-dashed border-slate-200 p-6 text-center text-xs text-slate-400">
                            {{ $selectedKelasId ? 'Tidak ada siswa terdaftar di kelas ini.' : 'Anda belum memiliki kelas yang diampu.' }}
                        </div>
                    @endforelse
                </div>
            </section>

            <!-- BAGIAN PENGUMUMAN KELAS -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                            <span>📢</span> Pengumuman Kelas
                        </h2>
                        <p class="text-xs text-slate-400">Daftar informasi dan instruksi penting yang ditujukan kepada siswa.</p>
                    </div>

                    <button type="button" onclick="openAnnouncementModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-xs transition cursor-pointer">
                        + Buat Pengumuman
                    </button>
                </div>

                <!-- LIST PENGUMUMAN -->
                <!-- LIST PENGUMUMAN -->
                <div class="space-y-3">
                    @forelse($pengumuman as $p)
                        <div class="p-4 rounded-xl border border-slate-100 bg-slate-50/50 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="space-y-1 min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-slate-800">{{ $p->judul }}</span>
                                    <span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-0.5 rounded-full">
                                        {{ $p->kelas->nama_kelas ?? 'Semua Kelas' }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-600 leading-relaxed">{{ $p->isi }}</p>
                                <span class="text-[10px] text-slate-400 block pt-1">
                                    {{ optional($p->diterbitkan_at ?? $p->created_at)->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }} WIB
                                </span>
                            </div>

                            <!-- TOMBOL OPSI (EDIT & HAPUS) -->
                            <div class="flex items-center gap-2 shrink-0">
                                <a href="{{ route('guru.pengumuman.edit', $p->id) }}" class="text-xs font-bold text-blue-600 hover:bg-blue-100/70 bg-white px-3 py-1.5 rounded-lg border border-blue-200 transition">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('guru.pengumuman.destroy', $p->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-bold text-red-600 hover:bg-red-50 bg-white px-3 py-1.5 rounded-lg border border-red-200 transition cursor-pointer">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-xs text-slate-400 italic">
                            Belum ada pengumuman yang dipublikasikan.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </main>

    <!-- MODAL BUAT PENGUMUMAN -->
    <div id="announcement-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-800">Buat Pengumuman Baru</h3>
                <button type="button" onclick="closeAnnouncementModal()" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>

            <form method="POST" action="{{ route('guru.pengumuman.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Target Kelas</label>
                    <select name="kelas_id" class="w-full text-xs font-medium border border-slate-200 rounded-xl p-2.5 outline-none focus:ring-2 focus:ring-blue-300">
                        <option value="">Semua Kelas</option>
                        @foreach ($kelasList as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Judul Pengumuman</label>
                    <input type="text" name="judul" required placeholder="Contoh: Jadwal Ujian Tengah Semester" class="w-full text-xs font-medium border border-slate-200 rounded-xl p-2.5 outline-none focus:ring-2 focus:ring-blue-300">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Isi Pengumuman</label>
                    <textarea name="isi" rows="4" required placeholder="Tuliskan isi informasi untuk siswa..." class="w-full text-xs font-medium border border-slate-200 rounded-xl p-2.5 outline-none focus:ring-2 focus:ring-blue-300"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeAnnouncementModal()" class="px-4 py-2 text-xs font-bold text-slate-500 hover:bg-slate-100 rounded-xl">Batal</button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl">Publikasikan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const guruMonitoringUrl = @json(route('guru.monitoring.status'));
        const selectedMonitoringClass = @json($selectedKelasId);

        async function refreshGuruMonitoring() {
            try {
                const url = new URL(guruMonitoringUrl, window.location.origin);
                if (selectedMonitoringClass) url.searchParams.set('kelas_id', selectedMonitoringClass);
                const response = await fetch(url, { headers: { Accept: 'application/json' } });
                if (!response.ok) return;
                const data = await response.json();

                data.classes.forEach((kelas) => {
                    const panel = document.querySelector(`[data-class-monitoring="${kelas.id}"]`);
                    if (!panel) return;
                    const summary = panel.querySelector('[data-class-summary]');
                    summary.innerHTML = `${kelas.total} Siswa | <span class="text-green-700">● ${kelas.active} Aktif</span> | ○ ${kelas.total - kelas.active} Tidak aktif`;

                    kelas.students.forEach((student) => {
                        const row = panel.querySelector(`[data-student-id="${student.id}"]`);
                        if (!row) return;
                        const status = row.querySelector('[data-student-status]');
                        status.textContent = student.is_active ? '● Aktif' : '○ Tidak aktif';
                        status.className = `block text-[11px] font-bold ${student.is_active ? 'text-green-700' : 'text-slate-500'}`;
                        row.querySelector('[data-student-last-active]').textContent = student.last_active;
                    });
                });
            } catch (error) {
                console.error('Gagal memperbarui status siswa.', error);
            }
        }

        refreshGuruMonitoring();
        setInterval(refreshGuruMonitoring, 30000);

        function openAnnouncementModal() {
            document.getElementById('announcement-modal').classList.remove('hidden');
        }
        function closeAnnouncementModal() {
            document.getElementById('announcement-modal').classList.add('hidden');
        }
    </script>
</body>
</html>