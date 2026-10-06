@extends('layouts.app')

@php
    $title = 'Buat LKPD Baru';
    $subtitle = 'Buat LKPD baru dan susun soal sesuai kebutuhan kelas.';
@endphp

@section('content')
<div class="mx-auto w-full max-w-[1500px] min-h-[calc(100vh-7rem)]">
    <div class="mb-6 flex items-center justify-between gap-4">
        <h1 class="text-[36px] font-bold tracking-[-0.04em] text-slate-800">Buat LKPD Baru</h1>
        <a href="{{ route('guru.lkpd.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
            <span>←</span>
            <span>Kembali</span>
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('guru.lkpd.store') }}" enctype="multipart/form-data" x-data="lkpdBuilder()" class="space-y-5">
        @csrf

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.03fr_1.25fr] xl:auto-rows-fr">
            <section class="flex h-full flex-col overflow-hidden rounded-[18px] border border-slate-200 bg-white shadow-[0_4px_12px_rgba(15,23,42,0.05)]">
                <div class="flex items-center gap-3 bg-[#2f5be7] px-5 py-3.5 text-white">
                    <span class="text-lg">ⓘ</span>
                    <h2 class="text-base font-bold">Informasi LKPD</h2>
                </div>

                <div class="flex-1 space-y-5 p-5">
                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700">Kelas <span class="text-red-500">*</span></label>
                        <select name="kelas_id" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm text-slate-600 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($kelasList as $kelas)
                                <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700">Judul LKPD <span class="text-red-500">*</span></label>
                        <input type="text" name="judul" value="{{ old('judul') }}" required placeholder="Contoh: LKPD 1 - Hukum Newton" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700">Batas Waktu <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="date" name="deadline" value="{{ old('deadline') }}" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 pr-10 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                            <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-slate-400">📅</span>
                        </div>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700">Deskripsi Singkat</label>
                        <textarea name="deskripsi" rows="3" placeholder="Gambaran umum LKPD..." class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">{{ old('deskripsi') }}</textarea>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700">Instruksi Pengerjaan</label>
                        <textarea name="instruksi" rows="4" placeholder="Langkah atau petunjuk pengerjaan bagi siswa..." class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">{{ old('instruksi') }}</textarea>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700">Dokumen Modul Utuh (Opsional)</label>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5">
                            <input type="file" name="modul" accept=".pdf,.doc,.docx,.ppt,.pptx" class="w-full text-sm text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-[#2f5be7] file:px-3 file:py-2 file:text-xs file:font-bold file:text-white hover:file:bg-blue-700">
                        </div>
                        <p class="mt-2 text-xs text-slate-500">PDF/Word/PPT bisa ditambahkan bila diperlukan.</p>
                    </div>
                </div>
            </section>

            <section class="flex h-full flex-col overflow-hidden rounded-[18px] border border-slate-200 bg-white shadow-[0_4px_12px_rgba(15,23,42,0.05)]">
                <div class="flex items-center justify-between bg-[#2f5be7] px-5 py-3.5 text-white">
                    <div class="flex items-center gap-3">
                        <span class="text-lg">📝</span>
                        <h2 class="text-base font-bold">Daftar Item Soal LKPD</h2>
                    </div>
                    <span class="text-xs font-semibold text-blue-100">Soal akan dinilai otomatis menggunakan AI Gemini</span>
                </div>

                <div class="flex-1 space-y-5 overflow-y-auto p-5">
                    <template x-for="(question, index) in questions" :key="question.key">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="mb-4 flex items-center justify-between">
                                <h3 class="text-sm font-bold text-slate-700">Soal No. <span x-text="index + 1"></span></h3>
                                <button type="button" x-show="questions.length > 1" @click="removeQuestion(index)" class="rounded-lg bg-red-50 px-2.5 py-1.5 text-xs font-bold text-red-600 hover:bg-red-100">
                                    Hapus
                                </button>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="mb-2 block text-sm font-bold text-slate-700">Deskripsi / Pertanyaan Soal <span class="text-red-500">*</span></label>
                                    <textarea :name="`questions[${index}][pertanyaan]`" x-model="question.pertanyaan" rows="3" required placeholder="Tuliskan pertanyaan nomor 1 di sini..." class="w-full rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"></textarea>
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-bold text-slate-700">Gambar Pendukung Soal (Opsional)</label>
                                    <div class="rounded-xl border border-slate-200 bg-white px-3 py-2.5">
                                        <input type="file" :name="`questions[${index}][gambar]`" accept="image/*" class="w-full text-sm text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-[#2f5be7] file:px-3 file:py-2 file:text-xs file:font-bold file:text-white hover:file:bg-blue-700">
                                    </div>
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-bold text-emerald-700">Kunci Jawaban / Rubrik Penilaian AI <span class="text-red-500">*</span></label>
                                    <textarea :name="`questions[${index}][rubrik_jawaban]`" x-model="question.rubrik_jawaban" rows="3" required placeholder="Tuliskan kunci atau kriteria jawaban benar..." class="w-full rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"></textarea>
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-bold text-blue-700">Pembahasan untuk Siswa (Opsional)</label>
                                    <textarea :name="`questions[${index}][pembahasan]`" x-model="question.pembahasan" rows="3" placeholder="Jelaskan cara atau alasan jawaban yang benar..." class="w-full rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"></textarea>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="flex items-center justify-between gap-3 border-t border-slate-200 p-5">
                    <button type="button" @click="addQuestion()" class="inline-flex items-center justify-center rounded-xl bg-[#18b56b] px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#169d5e]">
                        <span class="mr-2 text-base">＋</span>
                        <span>Tambah Soal</span>
                    </button>

                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-[#2f5be7] px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#244bcb]">
                        <span class="mr-2 text-base">💾</span>
                        <span>Simpan &amp; Terbitkan LKPD</span>
                    </button>
                </div>
            </section>
        </div>
    </form>
</div>

<script>
    function lkpdBuilder() {
        return {
            questions: [{ key: Date.now(), pertanyaan: '', rubrik_jawaban: '', pembahasan: '' }],
            addQuestion() {
                this.questions.push({ key: Date.now() + Math.random(), pertanyaan: '', rubrik_jawaban: '', pembahasan: '' });
            },
            removeQuestion(index) {
                if (this.questions.length > 1) {
                    this.questions.splice(index, 1);
                }
            }
        };
    }
</script>
@endsection