<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Exam;
use App\Models\Aktivitas;
use App\Models\Lkpd;
use App\Models\LkpdQuestion;
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
use Illuminate\Support\Facades\Http;
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

    public function test_student_material_list_only_uses_their_assigned_class(): void
    {
        $targetClass = Kelas::create(['nama_kelas' => 'Kelas Materi Siswa']);
        $otherClass = Kelas::create(['nama_kelas' => 'Kelas Materi Lain']);
        $student = $this->createStudent($targetClass);
        $mapel = MataPelajaran::create(['nama_mapel' => 'Matematika']);
        $targetMaterial = Materi::create([
            'kelas_id' => $targetClass->id,
            'mapel_id' => $mapel->id,
            'urutan' => 1,
            'judul' => 'Materi Kelas Siswa',
            'konten' => 'Konten siswa',
            'status' => 'aktif',
        ]);
        Materi::create([
            'kelas_id' => $otherClass->id,
            'mapel_id' => $mapel->id,
            'urutan' => 1,
            'judul' => 'Materi Kelas Lain',
            'konten' => 'Konten kelas lain',
            'status' => 'aktif',
        ]);
        Materi::create([
            'kelas_id' => $targetClass->id,
            'mapel_id' => $mapel->id,
            'urutan' => 2,
            'judul' => 'Materi Draft',
            'konten' => 'Draft guru',
            'status' => 'draft',
        ]);

        $this->actingAs($student)
            ->get(route('siswa.materi.index'))
            ->assertOk()
            ->assertViewHas('groupedMateris', function ($groupedMateris) use ($targetMaterial) {
                return $groupedMateris->sum(fn ($materials) => $materials->count()) === 1
                    && $groupedMateris->first()->first()->is($targetMaterial);
            });

        $this->get(route('siswa.materi.index', ['type' => 'aktivitas']))
            ->assertRedirect(route('siswa.aktivitas.index'));

        $this->get(route('siswa.aktivitas.index'))
            ->assertOk()
            ->assertViewIs('siswa-aktivitas-index')
            ->assertSee('bg-[#E0EDFF] text-[#2563EB] font-bold shadow-xs', false);

        $this->get(route('siswa.materi.show', $targetMaterial->id))->assertOk();
        $this->get(route('siswa.materi.show', Materi::where('judul', 'Materi Kelas Lain')->value('id')))
            ->assertNotFound();
        $this->get(route('siswa.materi.show', Materi::where('judul', 'Materi Draft')->value('id')))
            ->assertNotFound();
    }

    public function test_student_can_open_lkpd_detail_and_see_its_questions(): void
    {
        Storage::fake('public');

        $teacher = $this->createUser('guru');
        $kelas = Kelas::create(['nama_kelas' => 'Kelas LKPD']);
        $student = $this->createStudent($kelas);
        $gambarPath = 'lkpd/questions/diagram.png';
        Storage::disk('public')->put($gambarPath, 'image-content');
        $lkpd = Lkpd::create([
            'guru_id' => $teacher->id,
            'kelas_id' => $kelas->id,
            'judul' => 'LKPD Barisan',
            'deskripsi' => 'Latihan barisan bilangan.',
            'instruksi' => 'Kerjakan semua soal.',
            'status' => 'published',
        ]);
        $question = LkpdQuestion::create([
            'lkpd_id' => $lkpd->id,
            'urutan' => 1,
            'pertanyaan' => 'Tentukan suku berikutnya: 2, 4, 6, ...',
            'gambar_path' => $gambarPath,
        ]);

        $this->actingAs($student)
            ->get(route('siswa.lkpd.show', $lkpd))
            ->assertOk()
            ->assertSee('LKPD Barisan')
            ->assertSee('Aktivitas & LKPD', false)
            ->assertSee('Tentukan suku berikutnya: 2, 4, 6, ...')
            ->assertSee('src="' . route('siswa.lkpd.question-image', [$lkpd, $question]) . '"', false)
            ->assertSee('name="jawaban[' . $lkpd->questions()->first()->id . ']"', false)
            ->assertSee(route('siswa.lkpd.submit', $lkpd), false);

        $this->get(route('siswa.lkpd.question-image', [$lkpd, $question]))
            ->assertOk()
            ->assertStreamedContent('image-content');
    }

    public function test_student_lkpd_submission_returns_to_activities_with_success_message(): void
    {
        $teacher = $this->createUser('guru');
        $kelas = Kelas::create(['nama_kelas' => 'Kelas Submit LKPD']);
        $student = $this->createStudent($kelas);
        $lkpd = Lkpd::create([
            'guru_id' => $teacher->id,
            'kelas_id' => $kelas->id,
            'judul' => 'LKPD Submit',
            'status' => 'published',
        ]);
        $question = LkpdQuestion::create([
            'lkpd_id' => $lkpd->id,
            'urutan' => 1,
            'pertanyaan' => 'Jawab pertanyaan ini',
            'rubrik_jawaban' => 'Jawaban adalah 42',
            'pembahasan' => 'Gunakan operasi yang dijelaskan pada materi.',
        ]);

        $this->fakeLkpdAiAssessment([[
            'question_id' => $question->id,
            'is_correct' => false,
            'feedback' => 'Jawaban belum sesuai dengan kunci.',
        ]]);

        $response = $this->actingAs($student)
            ->post(route('siswa.lkpd.submit', $lkpd), [
                'jawaban' => [$question->id => 'Jawaban siswa'],
            ]);

        $response->assertRedirect(route('siswa.aktivitas.index'));
        $this->assertDatabaseHas('lkpd_submissions', [
            'lkpd_id' => $lkpd->id,
            'siswa_id' => $student->id,
            'status' => 'submitted',
            'nilai' => 0,
        ]);

        $submission = $lkpd->submissions()->firstOrFail();
        $this->assertSame(false, $submission->hasil_penilaian[(string) $question->id]['is_correct']);

        $this->get(route('siswa.aktivitas.index'))
            ->assertOk()
            ->assertSee('Jawaban LKPD berhasil dikirim.')
            ->assertSee('Aktivitas & LKPD', false);

        $this->get(route('siswa.aktivitas.api'))
            ->assertOk()
            ->assertJsonPath('data.0.is_submitted', true);

        $this->get(route('siswa.lkpd.show', $lkpd))
            ->assertOk()
            ->assertSee('Jawaban Anda:')
            ->assertSee('Jawaban siswa')
            ->assertSee('Perlu diperbaiki')
            ->assertSee('Jawaban belum sesuai dengan kunci.')
            ->assertSee('Gunakan operasi yang dijelaskan pada materi.');
    }

    public function test_teacher_can_update_lkpd_rubric_and_explanation_without_replacing_question(): void
    {
        $teacher = $this->createUser('guru');
        $kelas = Kelas::create([
            'nama_kelas' => 'Kelas Edit Rubrik LKPD',
            'wali_kelas_id' => $teacher->id,
        ]);
        $lkpd = Lkpd::create([
            'guru_id' => $teacher->id,
            'kelas_id' => $kelas->id,
            'judul' => 'LKPD Rubrik',
            'deadline' => now()->addDay(),
            'status' => 'published',
        ]);
        $question = LkpdQuestion::create([
            'lkpd_id' => $lkpd->id,
            'urutan' => 1,
            'pertanyaan' => 'Pertanyaan lama',
            'rubrik_jawaban' => 'Kunci lama',
            'pembahasan' => 'Pembahasan lama',
        ]);

        $this->actingAs($teacher)
            ->put(route('guru.lkpd.update', $lkpd), [
                'kelas_id' => $kelas->id,
                'judul' => $lkpd->judul,
                'deadline' => now()->addDay()->format('Y-m-d H:i:s'),
                'questions' => [[
                    'id' => $question->id,
                    'pertanyaan' => 'Pertanyaan baru',
                    'rubrik_jawaban' => 'Kunci baru',
                    'pembahasan' => 'Pembahasan baru',
                ]],
            ])
            ->assertRedirect(route('guru.lkpd.index'));

        $question->refresh();
        $this->assertSame('Pertanyaan baru', $question->pertanyaan);
        $this->assertSame('Kunci baru', $question->rubrik_jawaban);
        $this->assertSame('Pembahasan baru', $question->pembahasan);
        $this->assertSame(1, $lkpd->questions()->count());
    }

    public function test_teacher_can_open_lkpd_edit_page(): void
    {
        $teacher = $this->createUser('guru');
        $kelas = Kelas::create([
            'nama_kelas' => 'Kelas Edit LKPD',
            'wali_kelas_id' => $teacher->id,
        ]);
        $lkpd = Lkpd::create([
            'guru_id' => $teacher->id,
            'kelas_id' => $kelas->id,
            'judul' => 'LKPD untuk Diedit',
            'status' => 'published',
        ]);

        $this->actingAs($teacher)
            ->get(route('guru.lkpd.edit', $lkpd))
            ->assertOk()
            ->assertSee('Edit LKPD');
    }

    public function test_realtime_activity_feed_includes_only_published_lkpds_for_the_students_class(): void
    {
        $teacher = $this->createUser('guru');
        $kelas = Kelas::create(['nama_kelas' => 'Kelas Feed LKPD']);
        $otherClass = Kelas::create(['nama_kelas' => 'Kelas Feed Lain']);
        $student = $this->createStudent($kelas);

        Aktivitas::create([
            'guru_id' => $teacher->id,
            'kelas_id' => $kelas->id,
            'judul' => 'Aktivitas API Lama',
            'status' => 'published',
        ]);
        $targetLkpd = Lkpd::create([
            'guru_id' => $teacher->id,
            'kelas_id' => $kelas->id,
            'judul' => 'LKPD API Target',
            'status' => 'published',
        ]);
        Lkpd::create([
            'guru_id' => $teacher->id,
            'kelas_id' => $otherClass->id,
            'judul' => 'LKPD Kelas Lain',
            'status' => 'published',
        ]);
        Lkpd::create([
            'guru_id' => $teacher->id,
            'kelas_id' => $kelas->id,
            'judul' => 'LKPD Draft',
            'status' => 'draft',
        ]);

        $this->actingAs($student)
            ->getJson(route('siswa.aktivitas.api'))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonFragment([
                'id' => 'aktivitas-' . Aktivitas::where('judul', 'Aktivitas API Lama')->value('id'),
                'type' => 'aktivitas',
                'judul' => 'Aktivitas API Lama',
            ])
            ->assertJsonFragment([
                'id' => 'lkpd-' . $targetLkpd->id,
                'type' => 'lkpd',
                'judul' => 'LKPD API Target',
                'show_url' => route('siswa.lkpd.show', $targetLkpd),
            ])
            ->assertJsonMissing(['judul' => 'LKPD Kelas Lain'])
            ->assertJsonMissing(['judul' => 'LKPD Draft']);
    }

    public function test_realtime_activity_feed_works_when_student_has_no_legacy_activities(): void
    {
        $teacher = $this->createUser('guru');
        $kelas = Kelas::create(['nama_kelas' => 'Kelas LKPD Tanpa Aktivitas']);
        $student = $this->createStudent($kelas);
        $lkpd = Lkpd::create([
            'guru_id' => $teacher->id,
            'kelas_id' => $kelas->id,
            'judul' => 'LKPD Tanpa Aktivitas Lama',
            'status' => 'published',
        ]);

        $this->actingAs($student)
            ->getJson(route('siswa.aktivitas.api'))
            ->assertOk()
            ->assertJsonFragment([
                'id' => 'lkpd-' . $lkpd->id,
                'type' => 'lkpd',
                'judul' => 'LKPD Tanpa Aktivitas Lama',
            ]);
    }

    public function test_material_notifications_are_sent_only_when_published_to_its_class(): void
    {
        $teacher = $this->createUser('guru');
        $targetClass = Kelas::create(['nama_kelas' => 'Kelas Materi Publish', 'wali_kelas_id' => $teacher->id]);
        $otherClass = Kelas::create(['nama_kelas' => 'Kelas Materi Lain']);
        $student = $this->createStudent($targetClass);
        $otherStudent = $this->createStudent($otherClass);

        $this->actingAs($teacher)
            ->post(route('guru.materi.store'), [
                'judul' => 'Materi Draft',
                'pekan' => '1',
                'kelas_id' => $targetClass->id,
                'konten' => 'Konten materi',
                'status' => 'draft',
            ])
            ->assertRedirect(route('guru.materi.index'));

        $materi = Materi::where('judul', 'Materi Draft')->firstOrFail();
        $this->assertSame(0, $student->notifications()->count());

        $this->put(route('guru.materi.update', $materi->id), [
            'judul' => 'Materi Draft',
            'pekan' => '1',
            'konten' => 'Konten materi',
            'status' => 'aktif',
        ])->assertRedirect(route('guru.materi.index'));

        $this->assertSame(1, $student->notifications()->count());
        $this->assertSame(0, $otherStudent->notifications()->count());
        $this->assertSame(
            route('siswa.materi.show', $materi->id),
            $student->notifications()->first()->data['url']
        );
    }

    public function test_student_activity_detail_and_download_require_published_class_access(): void
    {
        Storage::fake('public');

        $teacher = $this->createUser('guru');
        $targetClass = Kelas::create(['nama_kelas' => 'Kelas Aktivitas', 'wali_kelas_id' => $teacher->id]);
        $otherClass = Kelas::create(['nama_kelas' => 'Kelas Aktivitas Lain']);
        $student = $this->createStudent($targetClass);

        $targetActivity = Aktivitas::create([
            'guru_id' => $teacher->id,
            'kelas_id' => $targetClass->id,
            'judul' => 'Aktivitas Target',
            'status' => 'published',
        ]);
        $targetActivity->forceFill(['lkpd_path' => 'lkpd/target.pdf'])->save();
        Storage::disk('public')->put('lkpd/target.pdf', 'target');

        $otherActivity = Aktivitas::create([
            'guru_id' => $teacher->id,
            'kelas_id' => $otherClass->id,
            'judul' => 'Aktivitas Kelas Lain',
            'status' => 'published',
        ]);
        $otherActivity->forceFill(['lkpd_path' => 'lkpd/other.pdf'])->save();
        Storage::disk('public')->put('lkpd/other.pdf', 'other');

        $draftActivity = Aktivitas::create([
            'guru_id' => $teacher->id,
            'kelas_id' => $targetClass->id,
            'judul' => 'Aktivitas Draft',
            'status' => 'draft',
        ]);

        $this->actingAs($student)
            ->get(route('siswa.aktivitas.show', $targetActivity))
            ->assertOk();
        $this->get(route('siswa.aktivitas.lkpd.download', $targetActivity))->assertOk();
        $this->get(route('siswa.aktivitas.show', $otherActivity))->assertForbidden();
        $this->get(route('siswa.aktivitas.lkpd.download', $otherActivity))->assertForbidden();
        $this->get(route('siswa.aktivitas.show', $draftActivity))->assertForbidden();

        $otherTeacher = $this->createUser('guru');
        $this->actingAs($otherTeacher)
            ->get(route('guru.aktivitas.lkpd.download', $targetActivity))
            ->assertForbidden();
    }

    public function test_published_activity_submission_notifies_only_its_class_teacher(): void
    {
        $teacher = $this->createUser('guru');
        $targetClass = Kelas::create(['nama_kelas' => 'Kelas Aktivitas Publish', 'wali_kelas_id' => $teacher->id]);
        $otherClass = Kelas::create(['nama_kelas' => 'Kelas Aktivitas Bukan Target']);
        $student = $this->createStudent($targetClass);
        $otherStudent = $this->createStudent($otherClass);

        $this->actingAs($teacher)
            ->post(route('guru.aktivitas.store'), [
                'judul' => 'Aktivitas Terhubung',
                'tujuan' => 'Latihan kelas',
                'pertanyaan' => 'Tuliskan jawaban',
                'respons_type' => 'text',
                'kelas_id' => $targetClass->id,
                'status' => 'draft',
            ])
            ->assertRedirect(route('guru.aktivitas.index'));

        $activity = Aktivitas::where('judul', 'Aktivitas Terhubung')->firstOrFail();
        $this->assertNull($activity->published_at);
        $this->assertSame(0, $student->notifications()->count());

        $this->put(route('guru.aktivitas.update', $activity->id), [
            'judul' => 'Aktivitas Terhubung',
            'tujuan' => 'Latihan kelas',
            'pertanyaan' => 'Tuliskan jawaban',
            'respons_type' => 'text',
            'kelas_id' => $targetClass->id,
            'status' => 'published',
        ])->assertRedirect(route('guru.aktivitas.index'));

        $activity->refresh();
        $this->assertNotNull($activity->published_at);
        $this->assertSame(1, $student->notifications()->count());
        $this->assertSame(0, $otherStudent->notifications()->count());

        $this->actingAs($student)
            ->post(route('siswa.aktivitas.submit', $activity), [
                'text_answer' => 'Jawaban dari siswa target',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('aktivitas_submissions', [
            'aktivitas_id' => $activity->id,
            'siswa_id' => $student->id,
            'text_answer' => 'Jawaban dari siswa target',
            'status' => 'submitted',
        ]);
        $this->assertSame(1, $teacher->notifications()->count());
        $this->assertSame(
            route('guru.aktivitas.submissions', $activity->id),
            $teacher->notifications()->first()->data['url']
        );
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
        $textOnlyStudent = $this->createStudent($targetClass);
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
            ->get(route('siswa.tugas.show', $tugas->id))
            ->assertOk()
            ->assertSee(route('siswa.tugas.submit', $tugas->id), false)
            ->assertSee('name="jawaban"', false)
            ->assertSee('name="file_submission"', false);

        $this->actingAs($student)
            ->post(route('siswa.tugas.submit', $tugas->id), [
                'jawaban' => 'Jawaban tugas',
                'file_submission' => UploadedFile::fake()->create('jawaban.pdf', 10, 'application/pdf'),
            ])
            ->assertRedirect(route('siswa.tugas.show', $tugas->id));

        $this->actingAs($textOnlyStudent)
            ->post(route('siswa.tugas.submit', $tugas->id), [
                'jawaban' => 'Jawaban tanpa lampiran',
            ])
            ->assertRedirect(route('siswa.tugas.show', $tugas->id));

        $this->assertDatabaseHas('submissions', [
            'siswa_id' => $textOnlyStudent->id,
            'tugas_id' => $tugas->id,
            'jawaban' => 'Jawaban tanpa lampiran',
            'file_path' => null,
        ]);

        $submission = Submission::where('tugas_id', $tugas->id)->where('siswa_id', $student->id)->firstOrFail();
        $this->assertCount(2, $teacher->notifications);

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

        $otherTeacher = $this->createUser('guru');
        $this->actingAs($otherTeacher)
            ->post(route('guru.ujian.grade', $exam->submissions()->firstOrFail()), [
                'score' => 20,
                'feedback' => 'Tidak berwenang.',
            ])
            ->assertForbidden();

        $this->actingAs($student)
            ->get(route('siswa.ujian.show', $exam->id))
            ->assertOk()
            ->assertSee('Kuis Alur');

        $this->actingAs($teacher)
            ->post(route('guru.ujian.grade', $exam->submissions()->firstOrFail()), [
                'score' => 88,
                'feedback' => 'Jawaban tepat dan lengkap.',
            ])
            ->assertRedirect(route('guru.ujian.index'));

        $this->assertDatabaseHas('exam_submissions', [
            'exam_id' => $exam->id,
            'student_id' => $student->id,
            'score' => 88,
            'feedback' => 'Jawaban tepat dan lengkap.',
        ]);
        $this->assertSame(1, $student->notifications()->where('data->type', 'feedback')->count());

        $gradeNotification = $student->notifications()->where('data->type', 'feedback')->firstOrFail();
        $this->actingAs($student)
            ->post(route('notifications.read', $gradeNotification->id))
            ->assertRedirect(route('siswa.ujian.result', $exam->submissions()->firstOrFail()));

        $this->get(route('siswa.ujian.result', $exam->submissions()->firstOrFail()))
            ->assertOk()
            ->assertSee('88')
            ->assertSee('Jawaban tepat dan lengkap.');

        $this->get(route('siswa.evaluasi'))
            ->assertOk()
            ->assertSee('Kuis Alur')
            ->assertSee('Jawaban tepat dan lengkap.')
            ->assertSee('88');

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

    private function fakeLkpdAiAssessment(array $results): void
    {
        config(['services.gemini.api_key' => 'test-key']);
        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [[
                    'content' => [
                        'parts' => [[
                            'text' => json_encode(['results' => $results]),
                        ]],
                    ],
                ]],
            ]),
        ]);
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