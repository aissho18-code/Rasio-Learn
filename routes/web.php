<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\DiskusiController;
use App\Http\Controllers\KelasManagementController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\ProctoringController;
use App\Http\Controllers\SiswaAktivitasController;
use App\Http\Controllers\GuruAktivitasController;
use App\Http\Controllers\Auth\DirectPasswordResetController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SiswaPengumumanController;
use App\Http\Controllers\GuruPengumumanController;
use App\Http\Controllers\GuruExamController;
use App\Http\Controllers\AdminExamController;
use App\Http\Controllers\SiswaExamController;
use App\Http\Controllers\Guru\LkpdController as GuruLkpdController;
use App\Http\Controllers\Siswa\LkpdController as SiswaLkpdController;
use App\Http\Controllers\ActivityMonitoringController;

/*
|--------------------------------------------------------------------------
| RUTE PUBLIK & DASHBOARD UTAMA
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Dashboard utama hanya menjadi pintu masuk ke portal berdasarkan user terautentikasi.
Route::get('/dashboard', function (\Illuminate\Http\Request $request) {
    $dashboardRoute = $request->user()->dashboardRouteName();

    abort_unless($dashboardRoute, 403, 'Role akun tidak valid. Hubungi administrator.');

    return redirect()->route($dashboardRoute);
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| GROUP RUTE TERAUTENTIKASI (AUTH)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // =========================================================================
    // 👤 PROFIL PENGGUNA & NOTIFIKASI
    // =========================================================================
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/password', [ProfileController::class, 'editPassword'])->name('profile.password.edit');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.avatar');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');

    // =========================================================================
    // 💬 FORUM DISKUSI (SEMUA USER)
    // =========================================================================
    Route::get('/diskusi', [DiskusiController::class, 'index'])->name('diskusi.index');
    Route::post('/diskusi', [DiskusiController::class, 'store'])->name('diskusi.store');
    Route::post('/diskusi/{id}/komentar', [DiskusiController::class, 'storeKomentar'])->name('diskusi.komentar');
    Route::post('/diskusi/{id}/reaction', [DiskusiController::class, 'reaction'])->name('diskusi.reaction');
    Route::delete('/diskusi/{id}', [DiskusiController::class, 'destroy'])->name('diskusi.destroy');

    // =========================================================================
    // 🎓 RUTE SISWA
    // =========================================================================
    Route::prefix('siswa')->name('siswa.')->middleware('role:siswa')->group(function () {
        Route::get('/dashboard', [SiswaController::class, 'dashboard'])->middleware('verified')->name('dashboard');
        
        // Rute yang Dilindungi 'student.has_class'
        Route::middleware(['student.has_class'])->group(function () {
            Route::get('/materi', [SiswaController::class, 'materiIndex'])->name('materi.index');
            Route::get('/materi/{id}', [SiswaController::class, 'showMateri'])->name('materi.show');
            
            Route::get('/tugas', [SiswaController::class, 'indexTugas'])->name('tugas.index');
            Route::get('/tugas/{id}', [SiswaController::class, 'showTugas'])->name('tugas.show');
            Route::post('/tugas/{id}/submit', [SiswaController::class, 'submitTugas'])->name('tugas.submit');
            
            Route::get('/absensi', [SiswaController::class, 'absensi'])->name('absensi');
            Route::post('/absensi/store', [SiswaController::class, 'storeAbsensi'])->name('absensi.store');

            // Ujian Siswa (Terintegrasi SiswaExamController)
            Route::get('/ujian', [SiswaExamController::class, 'index'])->name('ujian.index');
            Route::get('/ujian/result/{submission}', [SiswaExamController::class, 'result'])->name('ujian.result');
            Route::get('/ujian/{id}', [SiswaExamController::class, 'show'])->whereNumber('id')->name('ujian.show');
            Route::post('/ujian/{id}/answers', [SiswaExamController::class, 'saveAnswers'])->whereNumber('id')->name('ujian.answers');
            Route::post('/ujian/{id}/photo', [SiswaExamController::class, 'savePhoto'])
    ->whereNumber('id')
    ->name('ujian.photo');
            Route::post('/ujian/{id}/submit', [SiswaExamController::class, 'submit'])->whereNumber('id')->name('ujian.submit');

            Route::get('/evaluasi', [SiswaController::class, 'evaluasiIndex'])->name('evaluasi');
            Route::get('/evaluasi/ujian/{id}', [SiswaController::class, 'evaluasiUjianDetail'])->name('evaluasi.ujian.detail');

            Route::get('/refleksi', [SiswaController::class, 'refleksiIndex'])->name('refleksi.index');
            Route::get('/refleksi/{id}', [SiswaController::class, 'refleksiShow'])->name('refleksi.show');
            Route::get('/refleksi/{id}/edit', [SiswaController::class, 'editRefleksi'])->whereNumber('id')->name('refleksi.edit');
            Route::put('/refleksi/{id}', [SiswaController::class, 'updateRefleksi'])->whereNumber('id')->name('refleksi.update');
            Route::post('/refleksi/{id}', [SiswaController::class, 'storeRefleksi'])->name('refleksi.store');

            // LKPD Siswa
            Route::get('/lkpd', [SiswaLkpdController::class, 'index'])->name('lkpd.index');
            Route::get('/lkpd/{lkpd}', [SiswaLkpdController::class, 'show'])->whereNumber('lkpd')->name('lkpd.show');
            Route::get('/lkpd/{lkpd}/questions/{question}/image', [SiswaLkpdController::class, 'questionImage'])
                ->whereNumber(['lkpd', 'question'])
                ->name('lkpd.question-image');
            Route::post('/lkpd/{lkpd}/submit', [SiswaLkpdController::class, 'submit'])->whereNumber('lkpd')->name('lkpd.submit');
        });

        // Pengumuman Siswa
        Route::get('/pengumuman', [SiswaPengumumanController::class, 'index'])->name('pengumuman.index');
        Route::post('/pengumuman/read-all', [SiswaPengumumanController::class, 'markAllAsRead'])->name('pengumuman.read-all');
        Route::get('/pengumuman/{id}', [SiswaPengumumanController::class, 'show'])->whereNumber('id')->name('pengumuman.show');

        // Aktivitas Siswa
        Route::get('/api/aktivitas', [SiswaAktivitasController::class, 'apiIndex'])->name('aktivitas.api');
        Route::get('/aktivitas', [SiswaAktivitasController::class, 'index'])->name('aktivitas.index');
        Route::get('/aktivitas/{aktivitas}', [SiswaAktivitasController::class, 'show'])->name('aktivitas.show');
        Route::post('/aktivitas/{aktivitas}/submit', [SiswaAktivitasController::class, 'submit'])->name('aktivitas.submit');
        Route::get('/aktivitas/{aktivitas}/lkpd/download', [SiswaAktivitasController::class, 'downloadLkpd'])->name('aktivitas.lkpd.download');
    });

    // Alias URL tanda hubung (-) untuk Siswa
    Route::get('/siswa-materi', [SiswaController::class, 'materiIndex'])->middleware(['role:siswa', 'student.has_class']);
    Route::get('/siswa-tugas', [SiswaController::class, 'indexTugas'])->middleware(['role:siswa', 'student.has_class']);
    Route::get('/siswa-absensi', [SiswaController::class, 'absensi'])->middleware(['role:siswa', 'student.has_class']);
    Route::get('/siswa-ujian', [SiswaExamController::class, 'index'])->middleware(['role:siswa', 'student.has_class']);
    Route::get('/siswa-evaluasi', [SiswaController::class, 'evaluasiIndex'])->middleware(['role:siswa', 'student.has_class']);

    // =========================================================================
    // 👨‍🏫 RUTE GURU
    // =========================================================================
    Route::prefix('guru')->name('guru.')->middleware('role:guru')->group(function () {
        Route::get('/dashboard', [GuruController::class, 'dashboard'])->name('dashboard');
        Route::get('/monitoring/status', [ActivityMonitoringController::class, 'guruStatus'])->name('monitoring.status');

        // Pengumuman Guru
        Route::get('/pengumuman', [GuruPengumumanController::class, 'index'])->name('pengumuman.index');
        Route::get('/pengumuman/create', [GuruPengumumanController::class, 'create'])->name('pengumuman.create');
        Route::post('/pengumuman', [GuruPengumumanController::class, 'store'])->name('pengumuman.store');
        Route::get('/pengumuman/{id}/edit', [GuruPengumumanController::class, 'edit'])->whereNumber('id')->name('pengumuman.edit');
        Route::put('/pengumuman/{id}', [GuruPengumumanController::class, 'update'])->whereNumber('id')->name('pengumuman.update');
        Route::delete('/pengumuman/{id}', [GuruPengumumanController::class, 'destroy'])->whereNumber('id')->name('pengumuman.destroy');

        // Materi Guru
        Route::get('/materi', [GuruController::class, 'materiIndex'])->name('materi.index');
        Route::get('/materi/{id}/edit', [GuruController::class, 'materiEdit'])->whereNumber('id')->name('materi.edit');
        Route::post('/materi', [GuruController::class, 'materiStore'])->name('materi.store');
        Route::put('/materi/{id}', [GuruController::class, 'materiUpdate'])->name('materi.update');
        Route::post('/materi/{id}/toggle-lock', [GuruController::class, 'materiToggleLock'])->name('materi.toggle-lock');
        Route::post('/materi/{id}/unlock', [GuruController::class, 'unlockMateri'])->name('unlock');
        Route::delete('/materi/{id}', [GuruController::class, 'materiDestroy'])->name('materi.destroy');

        // Presensi Guru
        Route::get('/presensi', [GuruController::class, 'presensiIndex'])->name('presensi.index');
        Route::get('/presensi/pdf', [GuruController::class, 'presensiPdf'])->name('presensi.pdf');

        // Ujian Guru (Terintegrasi GuruExamController)
        Route::get('/ujian', [GuruExamController::class, 'index'])->name('ujian.index');
        Route::post('/ujian/submissions/{submission}/grade', [GuruExamController::class, 'grade'])->name('ujian.grade');
        Route::get('/ujian/create', [GuruExamController::class, 'create'])->name('ujian.create');
        Route::post('/ujian', [GuruExamController::class, 'store'])->name('ujian.store');
        Route::get('/ujian/{id}/questions', [GuruExamController::class, 'questions'])->whereNumber('id')->name('ujian.questions.index');
        Route::get('/ujian/{id}/questions/create', [GuruExamController::class, 'createQuestion'])->whereNumber('id')->name('ujian.questions.create');
        Route::post('/ujian/{id}/questions', [GuruExamController::class, 'storeQuestion'])->whereNumber('id')->name('ujian.questions.store');
        Route::get('/ujian/{id}/questions/{questionId}/edit', [GuruExamController::class, 'editQuestion'])->whereNumber('id')->whereNumber('questionId')->name('ujian.questions.edit');
        Route::put('/ujian/{id}/questions/{questionId}', [GuruExamController::class, 'updateQuestion'])->whereNumber('id')->whereNumber('questionId')->name('ujian.questions.update');
        Route::delete('/ujian/{id}/questions/{questionId}', [GuruExamController::class, 'destroyQuestion'])->whereNumber('id')->whereNumber('questionId')->name('ujian.questions.destroy');
        Route::get('/ujian/{id}/edit', [GuruExamController::class, 'edit'])->whereNumber('id')->name('ujian.edit');
        Route::put('/ujian/{id}', [GuruExamController::class, 'update'])->whereNumber('id')->name('ujian.update');
        Route::delete('/ujian/{id}', [GuruExamController::class, 'destroy'])->whereNumber('id')->name('ujian.destroy');
        Route::post('/ujian/{id}/toggle-lock', [GuruExamController::class, 'toggleLock'])->whereNumber('id')->name('ujian.toggle-lock');

        // Tugas Guru
        Route::get('/tugas', [GuruController::class, 'tugasIndex'])->name('tugas.index');
        Route::get('/tugas/create', [GuruController::class, 'tugasCreate'])->name('tugas.create');
        Route::post('/tugas', [GuruController::class, 'tugasStore'])->name('tugas.store');
        Route::get('/tugas/{id}/edit', [GuruController::class, 'tugasEdit'])->name('tugas.edit');
        Route::put('/tugas/{id}', [GuruController::class, 'tugasUpdate'])->name('tugas.update');
        Route::delete('/tugas/{id}', [GuruController::class, 'tugasDestroy'])->name('tugas.destroy');

        // Penilaian & AI Guru
        Route::get('/penilaian', [GuruController::class, 'penilaianIndex'])->name('penilaian.index');
        Route::post('/penilaian/{id}', [GuruController::class, 'storePenilaian'])->name('penilaian.store');
        Route::get('/penilaian/ai/{id}', [GuruController::class, 'nilaiDenganAi'])->name('penilaian.ai');

        Route::get('/penilaian/ujian/{id}/detail', [GuruController::class, 'penilaianUjianDetail'])->name('penilaian.ujian.detail');
        Route::post('/penilaian/ujian/{id}', [GuruController::class, 'storePenilaianUjian'])->name('penilaian.ujian.store');
        Route::get('/penilaian/ujian/ai/{id}', [GuruController::class, 'aiAutoCorrectUjian'])->name('penilaian.ujian.ai');

        Route::get('/penilaian/tugas/{id}', [GuruController::class, 'penilaianTugasDetail'])->name('penilaian.tugas.detail');
        Route::post('/penilaian/tugas/{id}/ai', [GuruController::class, 'aiAutoCorrectTugas'])->name('penilaian.tugas.ai');
        Route::post('/penilaian/tugas/{id}/store', [GuruController::class, 'storePenilaianTugas'])->name('penilaian.tugas.store');

        Route::post('/submission/{id}/ai-grade', [GuruController::class, 'nilaiDenganAi'])->name('ai.grade');
        Route::post('/submission/{id}/finalize', [GuruController::class, 'updateNilaiFinal'])->name('finalize');

        // Refleksi Guru
        Route::get('/refleksi', [GuruController::class, 'refleksiIndex'])->name('refleksi.index');
        Route::get('/refleksi/create', [GuruController::class, 'refleksiCreate'])->name('refleksi.create');
        Route::post('/refleksi', [GuruController::class, 'refleksiStore'])->name('refleksi.store');
        Route::get('/refleksi/{id}', [GuruController::class, 'refleksiDetail'])->name('refleksi.detail');
        Route::delete('/refleksi/{id}', [GuruController::class, 'refleksiDestroy'])->name('refleksi.destroy');

        // Aktivitas Guru
        Route::get('/aktivitas', [GuruAktivitasController::class, 'index'])->name('aktivitas.index');
        Route::get('/aktivitas/create', [GuruAktivitasController::class, 'create'])->name('aktivitas.create');
        Route::post('/aktivitas', [GuruAktivitasController::class, 'store'])->name('aktivitas.store');
        Route::get('/aktivitas/{id}/edit', [GuruAktivitasController::class, 'edit'])->whereNumber('id')->name('aktivitas.edit');
        Route::put('/aktivitas/{id}', [GuruAktivitasController::class, 'update'])->whereNumber('id')->name('aktivitas.update');
        Route::delete('/aktivitas/{aktivitas}', [GuruAktivitasController::class, 'destroy'])->name('aktivitas.destroy');
        Route::get('/aktivitas/{aktivitas}/submissions', [GuruAktivitasController::class, 'submissions'])->name('aktivitas.submissions');
        Route::get('/aktivitas/{aktivitas}/lkpd/download', [GuruAktivitasController::class, 'downloadLkpd'])->name('aktivitas.lkpd.download');

        // Kelola LKPD Guru
        Route::get('/lkpd', [GuruLkpdController::class, 'index'])->name('lkpd.index');
        Route::get('/lkpd/create', [GuruLkpdController::class, 'create'])->name('lkpd.create');
        Route::post('/lkpd', [GuruLkpdController::class, 'store'])->name('lkpd.store');
        Route::get('/lkpd/{lkpd}/edit', [GuruLkpdController::class, 'edit'])->whereNumber('lkpd')->name('lkpd.edit');
        Route::put('/lkpd/{lkpd}', [GuruLkpdController::class, 'update'])->whereNumber('lkpd')->name('lkpd.update');
        Route::delete('/lkpd/{lkpd}', [GuruLkpdController::class, 'destroy'])->whereNumber('lkpd')->name('lkpd.destroy');
    });

    // =========================================================================
    // ⚙️ RUTE ADMIN
    // =========================================================================
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\AdminController::class, 'index'])
            ->middleware('verified')
            ->name('dashboard');

        Route::get('monitoring/status', [ActivityMonitoringController::class, 'adminStatus'])->name('monitoring.status');
        
        // Manajemen Users
        Route::get('users', [UserManagementController::class, 'index'])->name('users');
        Route::get('users/list', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserManagementController::class, 'create'])->name('users.create');
        Route::post('users', [UserManagementController::class, 'store'])->name('users.store');
        Route::get('users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserManagementController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');

        // Manajemen Kelas
        Route::get('kelas', [KelasManagementController::class, 'index'])->name('kelas');
        Route::get('kelas/list', [KelasManagementController::class, 'index'])->name('kelas.index');
        Route::get('kelas/create', [KelasManagementController::class, 'create'])->name('kelas.create');
        Route::post('kelas', [KelasManagementController::class, 'store'])->name('kelas.store');
        Route::get('kelas/{kelas}/edit', [KelasManagementController::class, 'edit'])->name('kelas.edit');
        Route::put('kelas/{kelas}', [KelasManagementController::class, 'update'])->name('kelas.update');
        Route::delete('kelas/{kelas}', [KelasManagementController::class, 'destroy'])->name('kelas.destroy');

        Route::post('kelas/{kelas}/add-participant', [KelasManagementController::class, 'addParticipant'])->name('kelas.add_participant');
        Route::post('kelas/{kelas}/remove-participant', [KelasManagementController::class, 'removeParticipant'])->name('kelas.remove_participant');
        Route::post('kelas/{kelas}/move-participant', [KelasManagementController::class, 'moveParticipant'])->name('kelas.move_participant');

        // Pembelajaran Admin menggunakan controller/model yang sama dengan Guru.
        Route::get('/materi', [GuruController::class, 'materiIndex'])->name('materi.index');
        Route::get('/materi/{id}/edit', [GuruController::class, 'materiEdit'])->whereNumber('id')->name('materi.edit');
        Route::post('/materi', [GuruController::class, 'materiStore'])->name('materi.store');
        Route::put('/materi/{id}', [GuruController::class, 'materiUpdate'])->whereNumber('id')->name('materi.update');
        Route::post('/materi/{id}/toggle-lock', [GuruController::class, 'materiToggleLock'])->whereNumber('id')->name('materi.toggle-lock');
        Route::post('/materi/{id}/unlock', [GuruController::class, 'unlockMateri'])->whereNumber('id')->name('materi.unlock');
        Route::delete('/materi/{id}', [GuruController::class, 'materiDestroy'])->whereNumber('id')->name('materi.destroy');

        Route::get('/tugas', [GuruController::class, 'tugasIndex'])->name('tugas.index');
        Route::get('/tugas/create', [GuruController::class, 'tugasCreate'])->name('tugas.create');
        Route::post('/tugas', [GuruController::class, 'tugasStore'])->name('tugas.store');
        Route::get('/tugas/{id}/edit', [GuruController::class, 'tugasEdit'])->whereNumber('id')->name('tugas.edit');
        Route::put('/tugas/{id}', [GuruController::class, 'tugasUpdate'])->whereNumber('id')->name('tugas.update');
        Route::delete('/tugas/{id}', [GuruController::class, 'tugasDestroy'])->whereNumber('id')->name('tugas.destroy');

        Route::get('/aktivitas', [GuruAktivitasController::class, 'index'])->name('aktivitas.index');
        Route::get('/aktivitas/create', [GuruAktivitasController::class, 'create'])->name('aktivitas.create');
        Route::post('/aktivitas', [GuruAktivitasController::class, 'store'])->name('aktivitas.store');
        Route::get('/aktivitas/{id}/edit', [GuruAktivitasController::class, 'edit'])->whereNumber('id')->name('aktivitas.edit');
        Route::put('/aktivitas/{id}', [GuruAktivitasController::class, 'update'])->whereNumber('id')->name('aktivitas.update');
        Route::delete('/aktivitas/{aktivitas}', [GuruAktivitasController::class, 'destroy'])->name('aktivitas.destroy');
        Route::get('/aktivitas/{aktivitas}/submissions', [GuruAktivitasController::class, 'submissions'])->name('aktivitas.submissions');
// Manajemen LKPD Admin
Route::get('/lkpd', [\App\Http\Controllers\Admin\LkpdController::class, 'index'])->name('lkpd.index');
Route::get('/lkpd/create', [\App\Http\Controllers\Admin\LkpdController::class, 'create'])->name('lkpd.create');
Route::post('/lkpd', [\App\Http\Controllers\Admin\LkpdController::class, 'store'])->name('lkpd.store');
Route::get('/lkpd/{lkpd}/edit', [\App\Http\Controllers\Admin\LkpdController::class, 'edit'])->whereNumber('lkpd')->name('lkpd.edit');
Route::put('/lkpd/{lkpd}', [\App\Http\Controllers\Admin\LkpdController::class, 'update'])->whereNumber('lkpd')->name('lkpd.update');
Route::delete('/lkpd/{lkpd}', [\App\Http\Controllers\Admin\LkpdController::class, 'destroy'])->whereNumber('lkpd')->name('lkpd.destroy');
        Route::get('/pengumuman', [\App\Http\Controllers\AdminPengumumanController::class, 'index'])->name('pengumuman.index');
        Route::get('/pengumuman/create', [\App\Http\Controllers\AdminPengumumanController::class, 'create'])->name('pengumuman.create');
        Route::post('/pengumuman', [\App\Http\Controllers\AdminPengumumanController::class, 'store'])->name('pengumuman.store');
        Route::get('/pengumuman/{id}/edit', [\App\Http\Controllers\AdminPengumumanController::class, 'edit'])->whereNumber('id')->name('pengumuman.edit');
        Route::put('/pengumuman/{id}', [\App\Http\Controllers\AdminPengumumanController::class, 'update'])->whereNumber('id')->name('pengumuman.update');
        Route::delete('/pengumuman/{id}', [\App\Http\Controllers\AdminPengumumanController::class, 'destroy'])->whereNumber('id')->name('pengumuman.destroy');

        Route::get('/quiz', [AdminExamController::class, 'index'])->name('quiz.index');
        Route::get('/monitoring', [ActivityMonitoringController::class, 'adminIndex'])->name('monitoring.index');

        // Manajemen Ujian Admin (Terintegrasi AdminExamController)
        Route::get('/exams', [AdminExamController::class, 'index'])->name('exams.index');
        Route::get('/exams/create', [AdminExamController::class, 'create'])->name('exams.create');
        Route::post('/exams', [AdminExamController::class, 'store'])->name('exams.store');
        Route::get('/exams/{id}/edit', [AdminExamController::class, 'edit'])->whereNumber('id')->name('exams.edit');
        Route::put('/exams/{id}', [AdminExamController::class, 'update'])->whereNumber('id')->name('exams.update');
        Route::delete('/exams/{id}', [AdminExamController::class, 'destroy'])->whereNumber('id')->name('exams.destroy');
        Route::post('/exams/{id}/toggle-lock', [AdminExamController::class, 'toggleLock'])->whereNumber('id')->name('exams.toggle-lock');
        Route::get('/exams/{id}/questions', [AdminExamController::class, 'edit'])->whereNumber('id')->name('exams.questions.index');
        Route::get('/exams/{id}/questions/create', [GuruExamController::class, 'createQuestion'])->whereNumber('id')->name('exams.questions.create');
        Route::post('/exams/{id}/questions', [GuruExamController::class, 'storeQuestion'])->whereNumber('id')->name('exams.questions.store');
        Route::get('/exams/{id}/questions/{questionId}/edit', [GuruExamController::class, 'editQuestion'])->whereNumber('id')->whereNumber('questionId')->name('exams.questions.edit');
        Route::put('/exams/{id}/questions/{questionId}', [GuruExamController::class, 'updateQuestion'])->whereNumber('id')->whereNumber('questionId')->name('exams.questions.update');
        Route::delete('/exams/{id}/questions/{questionId}', [GuruExamController::class, 'destroyQuestion'])->whereNumber('id')->whereNumber('questionId')->name('exams.questions.destroy');
        Route::post('/exams/submissions/{submission}/grade', [GuruExamController::class, 'grade'])->name('exams.grade');
        Route::get('/exams/export-logs', [AdminExamController::class, 'exportLogs'])->name('exams.export-logs');

        // Anti Kecurangan CBT (Proctoring)
        Route::get('/proctoring-demo', [ProctoringController::class, 'index'])->name('proctoring');
        Route::post('/proctoring/log', [ProctoringController::class, 'storeLog'])->name('proctoring.log');
        Route::get('/proctoring/logs', [ProctoringController::class, 'getLogs'])->name('proctoring.logs');
    });

});

/*
|--------------------------------------------------------------------------
| RUTE GUEST (PASSWORD RESET)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('forgot-password', [DirectPasswordResetController::class, 'requestForm'])
        ->name('password.direct.request');

    Route::post('forgot-password', [DirectPasswordResetController::class, 'verifyEmail'])
        ->name('password.direct.verify');

    Route::get('reset-password-direct', [DirectPasswordResetController::class, 'resetForm'])
        ->name('password.direct.reset');

    Route::post('reset-password-direct', [DirectPasswordResetController::class, 'updatePassword'])
        ->name('password.direct.update');

    Route::get('forgot-password-alias', [DirectPasswordResetController::class, 'requestForm'])
        ->name('password.request');
});

require __DIR__.'/auth.php';