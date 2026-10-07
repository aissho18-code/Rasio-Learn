@extends('layouts.siswa')

@php
    $title = $exam->title;
    $subtitle = 'Ujian dan kuis';
    $submission = $submission ?? $exam->submissions->first();
    $questions = $questions ?? collect();
@endphp

@section('content')
<div class="mx-auto max-w-5xl space-y-5">
    <a href="{{ route('siswa.ujian.index') }}" class="text-xs font-semibold text-blue-700 hover:underline">← Kembali ke daftar ujian</a>

    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">{{ session('success') }}</div>
    @endif

    @if ($isResultOnly ?? false)
        @if ($canSeeScore ?? false)
            <section class="rounded-xl border border-emerald-200 bg-emerald-50 p-5">
                <h2 class="text-sm font-bold text-emerald-900">Hasil Penilaian · Percobaan {{ $submission->attempt_number }}</h2>
                <p class="mt-2 text-2xl font-extrabold text-emerald-800">{{ $submission->score }}<span class="text-sm"> / 100</span></p>
                <p class="mt-1 text-xs font-semibold {{ $submission->score >= $exam->min_score ? 'text-emerald-800' : 'text-red-700' }}">{{ $submission->score >= $exam->min_score ? 'Lulus' : 'Belum mencapai KKM' }} · KKM {{ $exam->min_score }}</p>
                <p class="mt-2 text-xs text-slate-600">Benar {{ $submission->correct_count ?? '—' }} · Salah {{ $submission->wrong_count ?? '—' }} · Waktu {{ $submission->duration_seconds ? gmdate('H:i:s', $submission->duration_seconds) : '—' }}</p>
                @if ($submission->feedback)
                    <p class="mt-3 whitespace-pre-line border-t border-emerald-200 pt-3 text-xs leading-5 text-emerald-900">{{ $submission->feedback }}</p>
                @endif
            </section>
        @elseif (!$submission->graded_at && $exam->questions->contains('type', 'essay'))
            <section class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-xs font-semibold text-amber-900">Jawaban esai sedang menunggu penilaian guru.</section>
        @else
            <section class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-xs text-slate-600">Ujian selesai. Nilai tidak ditampilkan sesuai pengaturan ujian.</section>
        @endif
    @endif

    <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase text-blue-700">{{ $exam->kelas?->nama_kelas ?? 'Semua kelas' }}</p>
                <h1 class="mt-1 text-2xl font-extrabold text-slate-900">{{ $exam->title }}</h1>
                <p class="mt-3 whitespace-pre-line text-sm leading-6 text-slate-600">{{ $exam->description ?: 'Tidak ada petunjuk tambahan.' }}</p>
            </div>
            <div class="text-right text-xs text-slate-500">
                <p>{{ ['cbt' => 'CBT', 'essay' => 'Esai', 'mixed' => 'Campuran', 'quiz_interactive' => 'Quiz Interaktif'][$exam->exam_model] ?? 'Ujian' }}</p>
                <p class="mt-1">Durasi {{ $exam->duration_minutes }} menit · KKM {{ $exam->min_score }}</p>
                @unless ($isResultOnly ?? false)
                    <p class="mt-2 font-bold text-red-700">Sisa waktu <span id="exam-timer">--:--</span></p>
                @endunless
            </div>
        </div>
    </section>

    @if ($isResultOnly ?? false)
        @if ($canSeeExplanations ?? false)
            <section class="space-y-3">
                <h2 class="text-sm font-bold text-slate-800">Pembahasan</h2>
                @foreach ($exam->questions as $index => $question)
                    <article class="rounded-xl border border-slate-200 bg-white p-4">
                        <p class="text-xs font-bold text-slate-500">Soal {{ $index + 1 }} · {{ $question->points }} poin</p>
                        <p class="mt-2 whitespace-pre-line text-sm text-slate-800">{{ $question->prompt }}</p>
                        <p class="mt-3 text-xs text-slate-600">Jawaban Anda: {{ is_array($submission->answers[$question->id] ?? null) ? implode(', ', $submission->answers[$question->id]) : ($submission->answers[$question->id] ?? 'Tidak dijawab') }}</p>
                        <p class="mt-1 text-xs font-semibold text-emerald-800">Jawaban benar: {{ implode(', ', $question->correct_answer ?? []) ?: 'Dinilai manual' }}</p>
                        @if ($question->explanation)<p class="mt-2 whitespace-pre-line text-xs leading-5 text-slate-600">{{ $question->explanation }}</p>@endif
                    </article>
                @endforeach
            </section>
        @endif
    @else
        <form id="exam-form" method="POST" action="{{ route('siswa.ujian.submit', $exam->id) }}" data-save-url="{{ route('siswa.ujian.answers', $exam->id) }}" data-remaining="{{ $remainingSeconds }}" data-result-url="{{ route('siswa.ujian.result', $submission) }}" class="space-y-4">
            @csrf
            <input type="hidden" name="auto_submit" id="auto-submit" value="0">

            @if ($questions->isEmpty())
                <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <label for="response" class="block text-sm font-bold text-slate-800">Jawaban esai</label>
                    <textarea id="response" name="response" rows="12" required maxlength="50000" class="mt-3 w-full rounded-lg border border-slate-300 p-4 text-sm leading-6 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">{{ old('response', $submission->response) }}</textarea>
                </section>
            @else
                <div class="grid gap-4 lg:grid-cols-[220px_1fr]">
                    <aside class="h-fit rounded-xl border border-slate-200 bg-white p-4">
                        <h2 class="text-xs font-bold text-slate-800">Nomor Soal</h2>
                        <div class="mt-3 grid grid-cols-5 gap-2 sm:grid-cols-6 lg:grid-cols-4">
                            @foreach ($questions as $index => $question)
                                <button type="button" data-question-nav="{{ $index }}" class="relative aspect-square rounded-md border border-slate-200 text-xs font-bold text-slate-500 hover:border-blue-500">
                                    {{ $index + 1 }}
                                    <span data-answered-dot class="absolute bottom-1 right-1 hidden h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                </button>
                            @endforeach
                        </div>
                        <p id="save-status" aria-live="polite" class="mt-4 text-[10px] text-slate-400">Jawaban tersimpan otomatis</p>
                    </aside>

                    <div class="space-y-4">
                        @foreach ($questions as $index => $question)
                            @php $savedAnswer = old('answers.' . $question->id, $submission->answers[$question->id] ?? null); @endphp
                            <section data-question-panel="{{ $index }}" class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7" @if ($index > 0) hidden @endif>
                                <div class="flex items-center justify-between gap-4">
                                    <span class="text-xs font-bold text-blue-700">Soal {{ $index + 1 }} dari {{ $questions->count() }}</span>
                                    <span class="text-xs text-slate-500">{{ $question->points }} poin</span>
                                </div>
                                <h2 class="mt-4 whitespace-pre-line text-base font-semibold leading-7 text-slate-900">{{ $question->prompt }}</h2>

                                @if (in_array($question->type, ['multiple_choice', 'true_false']))
                                    <div class="mt-5 space-y-2">
                                        @foreach ($question->display_options ?? $question->options ?? [] as $option)
                                            <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-slate-200 p-3 text-sm hover:border-blue-400">
                                                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option }}" @checked($savedAnswer === $option) class="mt-0.5 text-blue-600">
                                                <span>{{ $option }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @elseif ($question->type === 'multiple_response')
                                    <div class="mt-5 space-y-2">
                                        @foreach ($question->display_options ?? $question->options ?? [] as $option)
                                            <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-slate-200 p-3 text-sm hover:border-blue-400">
                                                <input type="checkbox" name="answers[{{ $question->id }}][]" value="{{ $option }}" @checked(is_array($savedAnswer) && in_array($option, $savedAnswer, true)) class="mt-0.5 rounded text-blue-600">
                                                <span>{{ $option }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @else
                                    <label for="answer-{{ $question->id }}" class="mt-5 block text-xs font-semibold text-slate-600">{{ $question->type === 'essay' ? 'Jawaban esai' : 'Jawaban singkat' }}</label>
                                    <textarea id="answer-{{ $question->id }}" name="answers[{{ $question->id }}]" rows="{{ $question->type === 'essay' ? 8 : 3 }}" maxlength="5000" class="mt-2 w-full rounded-lg border border-slate-300 p-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">{{ is_string($savedAnswer) ? $savedAnswer : '' }}</textarea>
                                                                @endif

                                <div class="mt-6 rounded-xl border border-dashed border-blue-200 bg-blue-50/50 p-4">
                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-bold text-slate-800">📷 Foto Coret-coretan</p>
                                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                                Jika mengerjakan di kertas, kamu dapat memfoto coretan untuk soal ini.
                                            </p>
                                        </div>

                                        <button
                                            type="button"
                                            class="open-camera rounded-lg bg-blue-700 px-4 py-2 text-xs font-bold text-white hover:bg-blue-800"
                                            data-question-id="{{ $question->id }}"
                                        >
                                            📷 Foto Coret-coretan
                                        </button>
                                    </div>

                                    <div
                                        class="camera-box mt-4 hidden"
                                        data-camera-question="{{ $question->id }}"
                                    >
                                        <video
                                            class="camera-video w-full rounded-xl bg-black"
                                            autoplay
                                            playsinline
                                        ></video>

                                        <canvas class="camera-canvas hidden"></canvas>

                                        <div class="mt-3 flex flex-wrap gap-2">
                                            <button
                                                type="button"
                                                class="take-photo rounded-lg bg-emerald-600 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-700"
                                            >
                                                Ambil Foto
                                            </button>

                                            <button
                                                type="button"
                                                class="close-camera rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-bold text-slate-700"
                                            >
                                                Tutup Kamera
                                            </button>
                                        </div>

                                        <div class="photo-preview mt-4 hidden">
                                            <p class="mb-2 text-xs font-bold text-slate-700">Preview foto</p>

                                            <img
                                                class="preview-image w-full rounded-xl border border-slate-200"
                                                alt="Preview foto coret-coretan"
                                            >

                                            <div class="mt-3 flex flex-wrap gap-2">
                                                <button
                                                    type="button"
                                                    class="retake-photo rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-bold text-slate-700"
                                                >
                                                    Ambil Ulang
                                                </button>

                                                <button
                                                    type="button"
                                                    class="save-photo rounded-lg bg-emerald-600 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-700"
                                                >
                                                    Simpan Foto
                                                </button>
                                            </div>

                                            <p class="photo-status mt-2 text-xs text-slate-500"></p>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        @endforeach

                        <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white p-4">
                            <button type="button" id="previous-question" class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-bold text-slate-700 disabled:opacity-40">Sebelumnya</button>
                            <div class="flex gap-2">
                                <button type="button" id="next-question" class="rounded-lg bg-slate-100 px-4 py-2 text-xs font-bold text-slate-700">Berikutnya</button>
                                <button type="submit" class="rounded-lg bg-blue-700 px-4 py-2 text-xs font-bold text-white hover:bg-blue-800">Selesaikan Ujian</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @if ($questions->isEmpty())
                <div class="flex justify-end rounded-xl border border-slate-200 bg-white p-4">
                    <button type="submit" class="rounded-lg bg-blue-700 px-5 py-2.5 text-xs font-bold text-white">Selesaikan Ujian</button>
                </div>
            @endif
        </form>
    @endif
</div>

@unless ($isResultOnly ?? false)
<script>
    const examForm = document.getElementById('exam-form');
    const examPanels = [...document.querySelectorAll('[data-question-panel]')];
    const questionNav = [...document.querySelectorAll('[data-question-nav]')];
    const saveStatus = document.getElementById('save-status');
    let currentQuestion = 0;
    let saveTimer;
    let finishing = false;

    function displayQuestion(index) {
        currentQuestion = Math.max(0, Math.min(index, examPanels.length - 1));
        examPanels.forEach((panel, panelIndex) => panel.hidden = panelIndex !== currentQuestion);
        questionNav.forEach((button, buttonIndex) => {
            button.classList.toggle('border-blue-700', buttonIndex === currentQuestion);
            button.classList.toggle('bg-blue-50', buttonIndex === currentQuestion);
        });
        const previous = document.getElementById('previous-question');
        const next = document.getElementById('next-question');
        if (previous) previous.disabled = currentQuestion === 0;
        if (next) next.hidden = currentQuestion === examPanels.length - 1;
    }

    function markAnswered() {
        examPanels.forEach((panel, index) => {
            const values = [...panel.querySelectorAll('input, textarea')];
            const answered = values.some((input) => input.type === 'checkbox' || input.type === 'radio' ? input.checked : input.value.trim() !== '');
            const dot = questionNav[index]?.querySelector('[data-answered-dot]');
            if (dot) dot.classList.toggle('hidden', !answered);
        });
    }

    async function saveAnswers() {
        if (!examForm.dataset.saveUrl) return true;
        if (saveStatus) saveStatus.textContent = 'Menyimpan...';
        try {
            const response = await fetch(examForm.dataset.saveUrl, {
                method: 'POST',
                headers: {'Accept': 'application/json'},
                body: new FormData(examForm),
                credentials: 'same-origin'
            });
            if (response.status === 409) {
                window.location.href = examForm.dataset.resultUrl;
                return false;
            }
            if (saveStatus) saveStatus.textContent = response.ok ? 'Tersimpan ' + new Date().toLocaleTimeString('id-ID') : 'Belum tersimpan';
            return response.ok;
        } catch (error) {
            if (saveStatus) saveStatus.textContent = 'Koneksi terputus';
            return false;
        }
    }

    if (examForm && examPanels.length) {
        displayQuestion(0);
        markAnswered();
        questionNav.forEach((button) => button.addEventListener('click', () => displayQuestion(Number(button.dataset.questionNav))));
        document.getElementById('previous-question')?.addEventListener('click', () => displayQuestion(currentQuestion - 1));
        document.getElementById('next-question')?.addEventListener('click', () => displayQuestion(currentQuestion + 1));
        examForm.addEventListener('input', () => {
            markAnswered();
            clearTimeout(saveTimer);
            saveTimer = setTimeout(saveAnswers, 500);
        });
        examForm.addEventListener('change', () => {
            markAnswered();
            clearTimeout(saveTimer);
            saveTimer = setTimeout(saveAnswers, 300);
        });
    }

    const timerElement = document.getElementById('exam-timer');
    let secondsLeft = Number(examForm?.dataset.remaining || 0);
    function updateTimer() {
        if (!timerElement) return;
        const minutes = Math.floor(secondsLeft / 60).toString().padStart(2, '0');
        const seconds = (secondsLeft % 60).toString().padStart(2, '0');
        timerElement.textContent = minutes + ':' + seconds;
        if (secondsLeft <= 0) {
            document.getElementById('auto-submit').value = '1';
            finishing = true;
            clearTimeout(saveTimer);
            HTMLFormElement.prototype.submit.call(examForm);
            return;
        }
        secondsLeft -= 1;
        setTimeout(updateTimer, 1000);
    }
    updateTimer();

    examForm?.addEventListener('submit', async (event) => {
        if (finishing) return;
        event.preventDefault();
        if (!confirm('Selesaikan dan kirim jawaban ujian?')) return;
        finishing = true;
        clearTimeout(saveTimer);
        await saveAnswers();
        HTMLFormElement.prototype.submit.call(examForm);
    });
     // Kamera foto coret-coretan
    let cameraStream = null;
    let capturedPhotoBlob = null;

    document.querySelectorAll('.open-camera').forEach((button) => {
        button.addEventListener('click', async () => {
            const questionId = button.dataset.questionId;
            const box = document.querySelector(
                `[data-camera-question="${questionId}"]`
            );

            if (!box) return;

            const video = box.querySelector('.camera-video');
            const preview = box.querySelector('.photo-preview');

            try {
                cameraStream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: { ideal: 'environment' }
                    },
                    audio: false
                });

                video.srcObject = cameraStream;
                box.classList.remove('hidden');
                preview.classList.add('hidden');
            } catch (error) {
                alert('Kamera tidak dapat dibuka. Pastikan izin kamera diberikan pada browser.');
            }
        });
    });

    document.querySelectorAll('.take-photo').forEach((button) => {
        button.addEventListener('click', () => {
            const box = button.closest('.camera-box');
            const video = box.querySelector('.camera-video');
            const canvas = box.querySelector('.camera-canvas');
            const preview = box.querySelector('.photo-preview');
            const image = box.querySelector('.preview-image');

            if (!video.videoWidth || !video.videoHeight) {
                alert('Kamera belum siap. Tunggu sebentar lalu coba lagi.');
                return;
            }

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            const context = canvas.getContext('2d');
            context.drawImage(
                video,
                0,
                0,
                canvas.width,
                canvas.height
            );

            canvas.toBlob((blob) => {
                if (!blob) return;

                capturedPhotoBlob = blob;
                image.src = URL.createObjectURL(blob);
                preview.classList.remove('hidden');

                if (cameraStream) {
                    cameraStream.getTracks().forEach((track) => track.stop());
                    cameraStream = null;
                }
            }, 'image/jpeg', 0.85);
        });
    });

    document.querySelectorAll('.close-camera').forEach((button) => {
        button.addEventListener('click', () => {
            const box = button.closest('.camera-box');

            if (cameraStream) {
                cameraStream.getTracks().forEach((track) => track.stop());
                cameraStream = null;
            }

            box.classList.add('hidden');
        });
    });

    document.querySelectorAll('.retake-photo').forEach((button) => {
        button.addEventListener('click', async () => {
            const box = button.closest('.camera-box');
            const video = box.querySelector('.camera-video');
            const preview = box.querySelector('.photo-preview');

            try {
                cameraStream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: { ideal: 'environment' }
                    },
                    audio: false
                });

                video.srcObject = cameraStream;
                preview.classList.add('hidden');
                capturedPhotoBlob = null;
            } catch (error) {
                alert('Kamera tidak dapat dibuka.');
            }
        });
    });  
        document.querySelectorAll('.save-photo').forEach((button) => {
        button.addEventListener('click', async () => {
            const box = button.closest('.camera-box');
            const questionId = box.dataset.cameraQuestion;
            const status = box.querySelector('.photo-status');

            if (!capturedPhotoBlob) {
                status.textContent = 'Belum ada foto yang diambil.';
                return;
            }

            status.textContent = 'Menyimpan foto...';
            button.disabled = true;

            const formData = new FormData();
            formData.append('_token', document.querySelector('input[name="_token"]').value);
            formData.append('question_id', questionId);
            formData.append('photo', capturedPhotoBlob, 'coretan.jpg');

            try {
                const response = await fetch(
                    "{{ route('siswa.ujian.photo', $exam->id) }}",
                    {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json'
                        },
                        body: formData,
                        credentials: 'same-origin'
                    }
                );

                if (response.status === 409) {
                    window.location.href = examForm.dataset.resultUrl;
                    return;
                }

                const data = await response.json();

                if (!response.ok || !data.saved) {
                    throw new Error(data.message || 'Foto gagal disimpan.');
                }

                status.textContent = '✓ Foto berhasil disimpan.';
            } catch (error) {
                status.textContent = 'Foto gagal disimpan. Coba lagi.';
            } finally {
                button.disabled = false;
            }
        });
    }); 
</script>
@endunless
@endsection