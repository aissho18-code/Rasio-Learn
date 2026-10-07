<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>LKPD - Ratio Learn</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { font-family: 'Poppins', sans-serif; -webkit-font-smoothing: antialiased; }
    </style>
</head>
<body class="bg-[#F0F5FE] text-slate-800 h-screen w-screen overflow-hidden flex select-none">

    @include('layouts.sidebar-siswa-compact')

    <main class="flex-1 flex flex-col h-full overflow-y-auto">
        <x-dashboard-header />

        <div class="px-8 py-8 space-y-6 flex-1 max-w-7xl">
            @if(session('status'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-5 py-3 text-xs font-semibold text-green-700 shadow-xs" role="status">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-xs flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <span>📚</span> Aktivitas Pembelajaran & LKPD
                    </h1>
                    <p class="text-xs text-slate-500 mt-1">Unduh lembar kerja peserta didik (LKPD) dan serahkan jawaban tugas Anda di sini.</p>
                </div>
                <button id="sync-activities" type="button" aria-label="Sinkronkan aktivitas dan LKPD" title="Sinkronkan aktivitas dan LKPD" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 transition hover:border-blue-200 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-wait disabled:opacity-60">
                    <svg id="sync-icon" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M20 7v5h-5" />
                        <path d="M20 12a8 8 0 1 1-2.34-5.66L20 9" />
                    </svg>
                </button>
            </div>

            <div id="new-activity-notice" class="hidden bg-blue-600 text-white p-4 rounded-2xl shadow-md flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-xl">🔔</span>
                    <span class="text-xs font-bold">Guru baru saja menerbitkan Aktivitas/LKPD baru!</span>
                </div>
                <button id="dismiss-new-activity" type="button" class="text-xs bg-white text-blue-700 font-bold px-3 py-1.5 rounded-xl">Lihat</button>
            </div>

            <div id="aktivitas-list" class="space-y-4" aria-live="polite">
                <div id="aktivitas-empty" class="bg-white rounded-2xl p-8 text-center text-slate-400 text-xs border border-gray-100">
                    Memuat aktivitas dan LKPD...
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const list = document.getElementById('aktivitas-list');
            const emptyState = document.getElementById('aktivitas-empty');
            const syncButton = document.getElementById('sync-activities');
            const syncIcon = document.getElementById('sync-icon');
            const syncIndicator = document.getElementById('sync-indicator');
            const newActivityNotice = document.getElementById('new-activity-notice');
            let previousIds = new Set();
            let hasLoaded = false;
            let isSyncing = false;

            const makeElement = (tag, className, text) => {
                const element = document.createElement(tag);
                element.className = className;
                if (text !== undefined) element.textContent = text;
                return element;
            };

            const renderItem = (item) => {
                const card = makeElement('article', 'bg-white rounded-2xl border border-gray-100 shadow-xs p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 transition hover:border-blue-200');
                const details = makeElement('div', 'min-w-0');
                details.append(makeElement('h3', 'font-bold text-slate-900 text-sm', item.judul));
                details.append(makeElement('p', 'text-xs text-slate-500 mt-1', item.tujuan || 'Tidak ada deskripsi.'));

                const metadata = makeElement('div', 'flex flex-wrap items-center gap-2 mt-2');
                metadata.append(makeElement('span', 'text-[10px] bg-blue-50 text-blue-600 font-bold px-2.5 py-0.5 rounded-full', 'Guru: ' + item.guru_name));
                metadata.append(makeElement('span', 'text-[10px] bg-slate-100 text-slate-600 font-bold px-2.5 py-0.5 rounded-full uppercase', item.type === 'lkpd' ? 'LKPD' : 'Tipe: ' + item.respons_type));
                if (item.type === 'lkpd' && item.is_submitted) {
                    metadata.append(makeElement('span', 'text-[10px] bg-emerald-100 text-emerald-700 font-bold px-2.5 py-0.5 rounded-full', '✓ Sudah dikerjakan'));
                }
                metadata.append(makeElement('span', 'text-[10px] text-slate-400', item.created_at_formatted));
                details.append(metadata);
                card.append(details);

                const actions = makeElement('div', 'flex flex-wrap gap-2 shrink-0');
                if (item.has_lkpd && item.download_url) {
                    const download = makeElement('a', 'bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-4 py-2.5 rounded-xl transition');
                    download.href = item.download_url;
                    download.textContent = 'Unduh LKPD';
                    actions.append(download);
                }

                const open = makeElement('a', 'bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-xs transition');
                open.href = item.show_url;
                open.textContent = item.type === 'lkpd'
                    ? (item.is_submitted ? 'Lihat / ubah jawaban' : 'Kerjakan LKPD')
                    : 'Kerjakan Aktivitas';
                actions.append(open);
                card.append(actions);

                return card;
            };

            const fetchAktivitas = async () => {
                if (isSyncing) return;
                isSyncing = true;
                syncButton.disabled = true;
                syncIcon.classList.add('animate-spin');
                syncIndicator.classList.remove('hidden');

                try {
                    const response = await fetch('{{ route("siswa.aktivitas.api") }}', {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        cache: 'no-store',
                    });
                    if (!response.ok) throw new Error('Gagal memuat aktivitas.');

                    const result = await response.json();
                    if (!result.success || !Array.isArray(result.data)) throw new Error('Respons aktivitas tidak valid.');

                    const items = result.data;
                    const currentIds = new Set(items.map((item) => item.id));
                    if (hasLoaded && items.some((item) => !previousIds.has(item.id))) {
                        newActivityNotice.classList.remove('hidden');
                    }

                    list.replaceChildren(...items.map(renderItem));
                    if (items.length === 0) {
                        emptyState.textContent = 'Belum ada aktivitas atau LKPD yang diterbitkan untuk kelas Anda.';
                        list.append(emptyState);
                    }

                    previousIds = currentIds;
                    hasLoaded = true;
                } catch (error) {
                    console.error('Realtime sync error:', error);
                    if (!hasLoaded) emptyState.textContent = 'Aktivitas dan LKPD belum dapat dimuat. Coba sinkronkan kembali.';
                } finally {
                    isSyncing = false;
                    syncButton.disabled = false;
                    syncIcon.classList.remove('animate-spin');
                    syncIndicator.classList.add('hidden');
                }
            };

            syncButton.addEventListener('click', fetchAktivitas);
            document.getElementById('dismiss-new-activity').addEventListener('click', () => {
                newActivityNotice.classList.add('hidden');
            });

            fetchAktivitas();
            window.setInterval(fetchAktivitas, 4000);
        });
    </script>
</body>
</html>
