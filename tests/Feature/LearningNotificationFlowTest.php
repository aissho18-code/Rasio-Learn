<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Exam;
use App\Models\Materi;
use App\Models\MataPelajaran;
use App\Models\Submission;
use App\Models\Tugas;
use App\Models\User;
use App\Notifications\LearningNotification;
use App\Support\LearningNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LearningNotificationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_class_notification_only_reaches_students_in_that_class(): void
    {
        $targetClass = Kelas::create(['nama_kelas' => 'Kelas Target']);
        $otherClass = Kelas::create(['nama_kelas' => 'Kelas Lain']);
        $targetStudent = $this->createStudent($targetClass);
        $secondTargetStudent = $this->createStudent($targetClass);
        $otherStudent = $this->createStudent($otherClass);

        app(LearningNotificationService::class)->notifyStudentsInClass(
            $targetClass->id,
            new LearningNotification('assignment', 'Tugas Baru', 'Tugas untuk kelas target.', '/siswa/tugas/1', 'task:1')
        );

        $this->assertCount(1, $targetStudent->notifications);
        $this->assertCount(1, $secondTargetStudent->notifications);
        $this->assertCount(0, $otherStudent->notifications);
        $this->assertNotSame(
            $targetStudent->notifications->first()->id,
            $secondTargetStudent->notifications->first()->id
        );
        $this->assertSame('/siswa/tugas/1', $targetStudent->notifications->first()->data['url']);
    }

    public function test_notification_read_is_scoped_to_the_signed_in_user_and_redirects_to_target(): void
    {
        $owner = $this->createUser('siswa');
        $otherUser = $this->createUser('siswa');
        $notification = new LearningNotification('feedback', 'Nilai Tersedia', 'Nilai sudah tersedia.', '/siswa/evaluasi', 'grade:1');
        $owner->notify($notification);
        $notificationId = $owner->notifications()->firstOrFail()->id;

        $this->actingAs($otherUser)
            ->post(route('notifications.read', $notificationId))
            ->assertNotFound();

        $this->assertNull($owner->notifications()->firstOrFail()->read_at);

        $this->actingAs($owner)
            ->post(route('notifications.read', $notificationId))
            ->assertRedirect('/siswa/evaluasi');

        $this->assertNotNull($owner->notifications()->firstOrFail()->fresh()->read_at);
    }

    public function test_mark_all_read_only_updates_the_signed_in_users_notifications(): void
    {
        $owner = $this->createUser('siswa');
        $otherUser = $this->createUser('siswa');
        $owner->notify(new LearningNotification('assignment', 'Tugas 1', 'Pesan', '/siswa/tugas', 'task:1'));
        $owner->notify(new LearningNotification('feedback', 'Nilai 1', 'Pesan', '/siswa/evaluasi', 'grade:1'));
        $otherUser->notify(new LearningNotification('assignment', 'Tugas lain', 'Pesan', '/siswa/tugas', 'task:2'));

        $this->actingAs($owner)
            ->post(route('notifications.read-all'))
            ->assertRedirect();

        $this->assertSame(0, $owner->unreadNotifications()->count());
        $this->assertSame(1, $otherUser->unreadNotifications()->count());
    }

    public function test_deadline_reminder_skips_submitted_students_and_is_deduplicated(): void
    {
        $this->travelTo(now());
        $kelas = Kelas::create(['nama_kelas' => 'Kelas Reminder']);
        $submittedStudent = $this->createStudent($kelas);
        $pendingStudent = $this->createStudent($kelas);
        $mapel = MataPelajaran::create(['nama_mapel' => 'Matematika']);
        $materi = Materi::create([
            'kelas_id' => $kelas->id,
            'mapel_id' => $mapel->id,
            'urutan' => 1,
            'judul' => 'Materi Reminder',
            'konten' => 'Konten',
        ]);
        $tugas = Tugas::create([
            'materi_id' => $materi->id,
            'judul' => 'Tugas Reminder',
            'status' => 'aktif',
            'tenggat_waktu' => now()->addHours(8),
        ]);
        Submission::create([
            'tugas_id' => $tugas->id,
            'siswa_id' => $submittedStudent->id,
        ]);

        Artisan::call('notifications:task-deadlines');
        Artisan::call('notifications:task-deadlines');

        $this->assertCount(0, $submittedStudent->notifications()->get());
        $this->assertCount(1, $pendingStudent->notifications()->get());
        $this->assertSame(
            route('siswa.tugas.show', $tugas->id),
            $pendingStudent->notifications()->first()->data['url']
        );
    }

    public function test_task_publication_submission_and_feedback_notify_only_related_users(): void
    {
        Storage::fake('public');

        $teacher = $this->createUser('guru');
        $targetClass = Kelas::create(['nama_kelas' => 'Kelas Tugas', 'wali_kelas_id' => $teacher->id]);
        $otherClass = Kelas::create(['nama_kelas' => 'Kelas Lain']);
        $student = $this->createStudent($targetClass);
        $otherStudent = $this->createStudent($otherClass);
        $mapel = MataPelajaran::create(['nama_mapel' => 'Matematika']);
        $materi = Materi::create([
            'kelas_id' => $targetClass->id,
            'mapel_id' => $mapel->id,
            'urutan' => 1,
            'judul' => 'Materi Tugas',
            'konten' => 'Konten',
        ]);

        $this->actingAs($teacher)
            ->post(route('guru.tugas.store'), [
                'materi_id' => $materi->id,
                'judul' => 'Tugas Uji Alur',
                'tenggat_waktu' => now()->addDays(2)->format('Y-m-d H:i'),
            ])
            ->assertRedirect(route('guru.tugas.index'));

        $tugas = Tugas::where('judul', 'Tugas Uji Alur')->firstOrFail();
        $this->assertCount(1, $student->notifications);
        $this->assertCount(0, $otherStudent->notifications);

        $this->actingAs($student)
            ->post(route('siswa.tugas.submit', $tugas->id), [
                'jawaban' => 'Jawaban tugas',
                'file_submission' => UploadedFile::fake()->create('jawaban.pdf', 10, 'application/pdf'),
            ])
            ->assertRedirect(route('siswa.tugas.show', $tugas->id));

        $submission = Submission::where('tugas_id', $tugas->id)->where('siswa_id', $student->id)->firstOrFail();
        $this->assertCount(1, $teacher->notifications);

        $this->actingAs($teacher)
            ->post(route('guru.penilaian.tugas.store', $submission->id), [
                'nilai' => 95,
                'catatan_guru' => 'Bagus, jawaban benar.',
            ])
            ->assertRedirect(route('guru.penilaian.index'));

        $this->assertSame(2, $student->notifications()->count());
        $this->assertTrue($student->notifications()->where('data->type', 'feedback')->exists());
    }

    public function test_exam_submission_notifies_its_creator_for_review(): void
    {
        $teacher = $this->createUser('guru');
        $kelas = Kelas::create(['nama_kelas' => 'Kelas Ujian', 'wali_kelas_id' => $teacher->id]);
        $student = $this->createStudent($kelas);
        $exam = Exam::create([
            'title' => 'Kuis Alur',
            'kelas_id' => $kelas->id,
            'created_by' => $teacher->id,
        ]);

        $this->actingAs($student)
            ->post(route('siswa.ujian.submit', $exam->id), ['response' => 'Jawaban kuis'])
            ->assertRedirect(route('siswa.ujian.show', $exam->id));

        $this->assertDatabaseHas('exam_submissions', [
            'exam_id' => $exam->id,
            'student_id' => $student->id,
            'response' => 'Jawaban kuis',
        ]);
        $this->assertCount(1, $teacher->notifications);
        $this->assertSame('exam', $teacher->notifications()->first()->data['type']);

        $this->actingAs($student)
            ->get(route('siswa.ujian.show', $exam->id))
            ->assertOk()
            ->assertSee('Kuis Alur');

        $this->actingAs($teacher)
            ->get(route('guru.ujian.index'))
            ->assertOk()
            ->assertSee('Jawaban kuis');
    }

    public function test_admin_account_creation_generates_admin_and_user_notifications(): void
    {
        $admin = $this->createUser('admin');

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Guru Baru',
                'email' => 'guru-baru@example.test',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'guru',
            ])
            ->assertRedirect(route('admin.users.index'));

        $newTeacher = User::where('email', 'guru-baru@example.test')->firstOrFail();
        $this->assertSame(1, $admin->notifications()->count());
        $this->assertSame(1, $newTeacher->notifications()->count());

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('aria-label="Buka notifikasi"', false);
    }

    private function createStudent(Kelas $kelas): User
    {
        $student = $this->createUser('siswa');
        $student->siswaProfile()->create(['kelas_id' => $kelas->id]);

        return $student;
    }

    private function createUser(string $role): User
    {
        static $sequence = 0;
        $sequence++;

        return User::create([
            'name' => ucfirst($role) . ' Test ' . $sequence,
            'email' => $role . $sequence . '@example.test',
            'password' => Hash::make('password'),
            'role' => $role,
        ]);
    }
}