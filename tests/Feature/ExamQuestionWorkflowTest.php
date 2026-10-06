<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ExamQuestionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_answers_are_saved_and_objective_score_is_calculated_on_the_server(): void
    {
        [$teacher, $student, $kelas, $exam] = $this->createExamForStudent();
        $question = $exam->questions()->create([
            'prompt' => '2 + 2 = ?',
            'type' => 'multiple_choice',
            'options' => ['3', '4'],
            'correct_answer' => ['4'],
            'points' => 5,
            'position' => 0,
        ]);

        $this->actingAs($student)
            ->get(route('siswa.ujian.show', $exam->id))
            ->assertOk()
            ->assertSee('2 + 2 = ?');

        $submission = $exam->submissions()->where('student_id', $student->id)->firstOrFail();
        $this->actingAs($student)
            ->postJson(route('siswa.ujian.answers', $exam->id), ['answers' => [$question->id => '4']])
            ->assertOk()
            ->assertJsonPath('saved', true);

        $this->actingAs($student)
            ->post(route('siswa.ujian.submit', $exam->id), [
                'answers' => [$question->id => '4'],
                'score' => 0,
            ])
            ->assertRedirect(route('siswa.ujian.result', $submission));

        $this->assertDatabaseHas('exam_submissions', [
            'id' => $submission->id,
            'score' => 100,
            'correct_count' => 1,
            'wrong_count' => 0,
            'graded_at' => now()->toDateTimeString(),
        ]);
    }

    public function test_essay_attempt_waits_for_teacher_grading_and_uses_question_weights(): void
    {
        [$teacher, $student, $kelas, $exam] = $this->createExamForStudent(['exam_model' => 'essay']);
        $question = $exam->questions()->create([
            'prompt' => 'Jelaskan langkah penyelesaian.',
            'type' => 'essay',
            'points' => 5,
            'position' => 0,
        ]);

        $this->actingAs($student)->get(route('siswa.ujian.show', $exam->id))->assertOk();
        $submission = $exam->submissions()->where('student_id', $student->id)->firstOrFail();

        $this->actingAs($student)
            ->post(route('siswa.ujian.submit', $exam->id), ['answers' => [$question->id => 'Langkah penyelesaian siswa']])
            ->assertRedirect(route('siswa.ujian.result', $submission));

        $submission->refresh();
        $this->assertNull($submission->score);
        $this->assertNull($submission->graded_at);

        $this->actingAs($teacher)
            ->post(route('guru.ujian.grade', $submission), [
                'essay_scores' => [$question->id => 4],
                'feedback' => 'Langkah sudah tepat.',
            ])
            ->assertRedirect(route('guru.ujian.index'));

        $this->assertDatabaseHas('exam_submissions', [
            'id' => $submission->id,
            'score' => 80,
            'feedback' => 'Langkah sudah tepat.',
        ]);
    }

    public function test_student_and_teacher_access_is_limited_to_their_class_and_exam(): void
    {
        [$teacher, $student, $kelas, $exam] = $this->createExamForStudent();
        $otherTeacher = $this->createUser('guru');
        $otherClass = Kelas::create(['nama_kelas' => 'Kelas Lain']);
        $otherStudent = $this->createStudent($otherClass);

        $this->actingAs($otherTeacher)
            ->get(route('guru.ujian.questions.index', $exam->id))
            ->assertForbidden();

        $this->actingAs($otherStudent)
            ->get(route('siswa.ujian.show', $exam->id))
            ->assertNotFound();
    }

    public function test_timeout_auto_submits_the_saved_answers_and_unanswered_text_is_allowed(): void
    {
        [$teacher, $student, $kelas, $exam] = $this->createExamForStudent();
        $question = $exam->questions()->create([
            'prompt' => 'Tuliskan hasil.',
            'type' => 'short_answer',
            'options' => null,
            'correct_answer' => ['42'],
            'points' => 1,
            'position' => 0,
        ]);

        $this->actingAs($student)->get(route('siswa.ujian.show', $exam->id))->assertOk();
        $submission = $exam->submissions()->where('student_id', $student->id)->firstOrFail();
        $this->actingAs($student)
            ->postJson(route('siswa.ujian.answers', $exam->id), ['answers' => [$question->id => '']])
            ->assertOk();

        $this->travel(61)->minutes();
        $this->actingAs($student)
            ->postJson(route('siswa.ujian.answers', $exam->id), ['answers' => []])
            ->assertStatus(409)
            ->assertJsonPath('expired', true);

        $this->assertDatabaseHas('exam_submissions', [
            'id' => $submission->id,
            'completed_at' => now()->toDateTimeString(),
            'auto_submitted' => true,
            'score' => 0,
            'wrong_count' => 1,
        ]);
    }

    public function test_teacher_saves_exam_then_adds_questions_before_publishing(): void
    {
        $teacher = $this->createUser('guru');
        $kelas = Kelas::create(['nama_kelas' => 'Kelas Guru', 'wali_kelas_id' => $teacher->id]);
        $examSettings = [
            'title' => 'Quiz Interaktif',
            'description' => 'Petunjuk singkat.',
            'kelas_id' => $kelas->id,
            'exam_model' => 'quiz_interactive',
            'duration_minutes' => 30,
            'question_count' => 1,
            'min_score' => 70,
            'max_attempts' => 2,
            'shuffle_questions' => 0,
            'shuffle_options' => 1,
            'show_score' => 1,
            'show_explanations' => 1,
            'status' => 'draft',
            'max_violations' => 3,
        ];

        $this->actingAs($teacher)
            ->post(route('guru.ujian.store'), $examSettings)
            ->assertRedirect();

        $exam = Exam::where('title', 'Quiz Interaktif')->firstOrFail();
        $this->actingAs($teacher)
            ->post(route('guru.ujian.questions.store', $exam->id), [
                'prompt' => 'Pernyataan ini benar?',
                'type' => 'true_false',
                'correct_option' => 'Benar',
                'points' => 1,
            ])
            ->assertRedirect(route('guru.ujian.questions.index', $exam->id));

        $this->actingAs($teacher)
            ->get(route('guru.ujian.questions.index', $exam->id))
            ->assertOk()
            ->assertSee('Kelola Soal')
            ->assertSee('Pernyataan ini benar?');

        $this->actingAs($teacher)
            ->put(route('guru.ujian.update', $exam->id), array_merge($examSettings, ['status' => 'published']))
            ->assertRedirect(route('guru.ujian.questions.index', $exam->id));

        $this->assertDatabaseHas('exams', ['id' => $exam->id, 'status' => 'published']);
        $this->assertSame(['Benar'], $exam->questions()->firstOrFail()->correct_answer);
    }

    public function test_teacher_can_add_and_edit_essay_questions_on_a_cbt_exam(): void
    {
        [$teacher, , , $exam] = $this->createExamForStudent();

        $this->actingAs($teacher)
            ->post(route('guru.ujian.questions.store', $exam->id), [
                'prompt' => 'Jelaskan alasan jawabanmu.',
                'type' => 'essay',
                'points' => 5,
            ])
            ->assertRedirect(route('guru.ujian.questions.index', $exam->id));

        $question = $exam->questions()->firstOrFail();
        $this->assertSame('essay', $question->type);
        $this->assertNull($question->options);
        $this->assertNull($question->correct_answer);

        $this->put(route('guru.ujian.questions.update', [$exam->id, $question->id]), [
            'prompt' => 'Jelaskan alasan dan langkah jawabanmu.',
            'type' => 'essay',
            'points' => 7,
        ])->assertRedirect(route('guru.ujian.questions.index', $exam->id));

        $this->assertDatabaseHas('exam_questions', [
            'id' => $question->id,
            'type' => 'essay',
            'prompt' => 'Jelaskan alasan dan langkah jawabanmu.',
            'points' => 7,
        ]);
    }

    public function test_supported_objective_questions_remain_available_and_unknown_types_are_rejected(): void
    {
        [$teacher, , , $exam] = $this->createExamForStudent();

        $this->actingAs($teacher)
            ->post(route('guru.ujian.questions.store', $exam->id), [
                'prompt' => 'Berapakah 2 + 2?',
                'type' => 'multiple_choice',
                'options' => ['3', '4'],
                'correct_option' => 1,
                'points' => 1,
            ])
            ->assertRedirect(route('guru.ujian.questions.index', $exam->id));

        $this->assertDatabaseHas('exam_questions', [
            'exam_id' => $exam->id,
            'type' => 'multiple_choice',
            'prompt' => 'Berapakah 2 + 2?',
        ]);

        $this->post(route('guru.ujian.questions.store', $exam->id), [
            'prompt' => 'Tipe yang tidak dikenal.',
            'type' => 'unsupported',
            'points' => 1,
        ])->assertSessionHasErrors('type');
    }

    public function test_essay_exam_still_rejects_objective_questions(): void
    {
        [$teacher, , , $exam] = $this->createExamForStudent(['exam_model' => 'essay']);

        $this->actingAs($teacher)
            ->get(route('guru.ujian.questions.create', $exam->id))
            ->assertOk()
            ->assertSee('<option value="essay" selected>', false);

        $this->actingAs($teacher)
            ->post(route('guru.ujian.questions.store', $exam->id), [
                'prompt' => 'Berapakah 2 + 2?',
                'type' => 'multiple_choice',
                'options' => ['3', '4'],
                'correct_option' => 1,
                'points' => 1,
            ])
            ->assertStatus(422);
    }

    public function test_teacher_can_create_an_essay_question_on_an_essay_exam(): void
    {
        [$teacher, , , $exam] = $this->createExamForStudent(['exam_model' => 'essay']);

        $this->actingAs($teacher)
            ->post(route('guru.ujian.questions.store', $exam->id), [
                'prompt' => 'Jelaskan jawabanmu.',
                'type' => 'essay',
                'points' => 5,
            ])
            ->assertRedirect(route('guru.ujian.questions.index', $exam->id));

        $this->assertDatabaseHas('exam_questions', [
            'exam_id' => $exam->id,
            'type' => 'essay',
            'prompt' => 'Jelaskan jawabanmu.',
        ]);
    }

    public function test_timeout_can_finish_a_legacy_essay_without_a_typed_response(): void
    {
        [, $student, , $exam] = $this->createExamForStudent();
        $this->actingAs($student)->get(route('siswa.ujian.show', $exam->id))->assertOk();
        $submission = $exam->submissions()->where('student_id', $student->id)->firstOrFail();
        $this->travel(61)->minutes();

        $this->actingAs($student)
            ->post(route('siswa.ujian.submit', $exam->id), ['auto_submit' => 1])
            ->assertRedirect(route('siswa.ujian.show', $exam->id));

        $this->assertDatabaseHas('exam_submissions', [
            'id' => $submission->id,
            'submitted_at' => now()->toDateTimeString(),
            'auto_submitted' => true,
            'response' => '',
        ]);
    }

    public function test_admin_can_manage_teacher_exam_and_publish_it_for_students(): void
    {
        $teacher = $this->createUser('guru');
        $admin = $this->createUser('admin');
        $kelas = Kelas::create(['nama_kelas' => 'Kelas Terhubung', 'wali_kelas_id' => $teacher->id]);
        $student = $this->createStudent($kelas);
        $exam = Exam::create([
            'title' => 'Ujian Buatan Guru',
            'kelas_id' => $kelas->id,
            'created_by' => $teacher->id,
            'status' => 'draft',
            'exam_model' => 'cbt',
            'duration_minutes' => 45,
            'question_count' => 1,
            'min_score' => 75,
            'max_attempts' => 1,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.exams.questions.index', $exam->id))
            ->assertOk()
            ->assertSee('Ujian Buatan Guru');
        $this->get(route('admin.exams.questions.create', $exam->id))
            ->assertOk()
            ->assertSee('Portal Admin')
            ->assertSee(route('admin.exams.questions.store', $exam->id));

        $this->actingAs($admin)
            ->post(route('admin.exams.questions.store', $exam->id), [
                'prompt' => 'Berapa hasil 6 x 7?',
                'type' => 'multiple_choice',
                'options' => ['40', '42'],
                'correct_option' => 1,
                'points' => 1,
            ])
            ->assertRedirect(route('admin.exams.edit', $exam->id));

        $this->get(route('admin.exams.edit', $exam->id))
            ->assertOk()
            ->assertSee('Kelola Soal')
            ->assertSee('Berapa hasil 6 x 7?');

        $settings = [
            'title' => $exam->title,
            'description' => 'Petunjuk dari admin.',
            'kelas_id' => $kelas->id,
            'exam_model' => 'cbt',
            'duration_minutes' => 45,
            'question_count' => 1,
            'min_score' => 75,
            'max_attempts' => 1,
            'shuffle_questions' => 0,
            'shuffle_options' => 0,
            'show_score' => 1,
            'show_explanations' => 1,
            'status' => 'published',
            'max_violations' => 3,
        ];
        $this->actingAs($admin)
            ->put(route('admin.exams.update', $exam->id), $settings)
            ->assertRedirect(route('admin.exams.edit', $exam->id));

        $this->actingAs($student)
            ->get(route('siswa.ujian.index'))
            ->assertOk()
            ->assertSee('Ujian Buatan Guru');

        $this->get(route('siswa.ujian.show', $exam->id))
            ->assertOk()
            ->assertSee('Berapa hasil 6 x 7?');
    }

    public function test_admin_can_create_global_exam_and_students_can_take_it(): void
    {
        $admin = $this->createUser('admin');
        $kelas = Kelas::create(['nama_kelas' => 'Kelas Semua Siswa']);
        $student = $this->createStudent($kelas);
        $settings = [
            'title' => 'Ujian Global Admin',
            'description' => 'Dibuat admin untuk seluruh kelas.',
            'kelas_id' => '',
            'exam_model' => 'cbt',
            'duration_minutes' => 20,
            'question_count' => 1,
            'min_score' => 60,
            'max_attempts' => 2,
            'shuffle_questions' => 0,
            'shuffle_options' => 0,
            'show_score' => 1,
            'show_explanations' => 0,
            'status' => 'draft',
            'max_violations' => 3,
        ];

        $this->actingAs($admin)
            ->get(route('admin.exams.create'))
            ->assertOk()
            ->assertSee('Portal Admin')
            ->assertSee('Model Ujian');

        $this->post(route('admin.exams.store'), $settings)->assertRedirect();
        $exam = Exam::where('title', 'Ujian Global Admin')->firstOrFail();
        $this->post(route('admin.exams.questions.store', $exam->id), [
            'prompt' => 'Ibu kota Indonesia?',
            'type' => 'multiple_choice',
            'options' => ['Jakarta', 'Bandung'],
            'correct_option' => 0,
            'points' => 1,
        ])->assertRedirect(route('admin.exams.edit', $exam->id));

        $this->put(route('admin.exams.update', $exam->id), array_merge($settings, ['status' => 'published']))
            ->assertRedirect(route('admin.exams.edit', $exam->id));

        $this->actingAs($student)
            ->get(route('siswa.ujian.index'))
            ->assertOk()
            ->assertSee('Ujian Global Admin');
        $this->get(route('siswa.ujian.show', $exam->id))
            ->assertOk()
            ->assertSee('Ibu kota Indonesia?');

        $question = $exam->questions()->firstOrFail();
        $submission = $exam->submissions()->where('student_id', $student->id)->firstOrFail();
        $this->post(route('siswa.ujian.submit', $exam->id), ['answers' => [$question->id => 'Jakarta']])
            ->assertRedirect(route('siswa.ujian.result', $submission));

        $this->actingAs($admin)
            ->get(route('admin.exams.index'))
            ->assertOk()
            ->assertSee('Kelola Ujian')
            ->assertDontSee('Kelola Soal</a>', false)
            ->assertSee('Pengumpulan dan Hasil Siswa')
            ->assertSee($student->name)
            ->assertSee('Nilai objektif dihitung otomatis');
    }

    private function createExamForStudent(array $attributes = []): array
    {
        $teacher = $this->createUser('guru');
        $kelas = Kelas::create(['nama_kelas' => 'Kelas Ujian', 'wali_kelas_id' => $teacher->id]);
        $student = $this->createStudent($kelas);
        $exam = Exam::create(array_merge([
            'title' => 'Ujian Soal Uji',
            'kelas_id' => $kelas->id,
            'created_by' => $teacher->id,
            'status' => 'published',
            'exam_model' => 'cbt',
            'duration_minutes' => 60,
            'max_attempts' => 1,
            'min_score' => 75,
        ], $attributes));

        return [$teacher, $student, $kelas, $exam];
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
            'name' => ucfirst($role) . ' Uji ' . $sequence,
            'email' => $role . '-exam-' . $sequence . '@example.test',
            'password' => Hash::make('password'),
            'role' => $role,
        ]);
    }
}
