<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminTeacherConsistencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_with_spatie_role_is_in_teacher_lookup(): void
    {
        $teacher = $this->createUser('siswa');
        Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']);
        $teacher->assignRole('guru');

        $this->assertTrue(User::forRoles('guru')->whereKey($teacher->id)->exists());
    }

    public function test_adding_teacher_to_class_updates_teacher_dashboard(): void
    {
        $admin = $this->createUser('admin');
        $teacher = $this->createUser('guru');
        $kelas = Kelas::create(['nama_kelas' => 'Kelas Uji']);

        $this->actingAs($admin)
            ->post(route('admin.kelas.add_participant', $kelas), [
                'user_id' => $teacher->id,
                'role' => 'guru',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('kelas', [
            'id' => $kelas->id,
            'wali_kelas_id' => $teacher->id,
        ]);

        $this->actingAs($teacher)
            ->get(route('guru.dashboard'))
            ->assertOk()
            ->assertViewHas('kelasList', fn ($kelasList) => $kelasList->contains('id', $kelas->id));
    }

    public function test_creating_teacher_with_class_assigns_them_as_wali(): void
    {
        $admin = $this->createUser('admin');
        $kelas = Kelas::create(['nama_kelas' => 'Kelas Uji']);

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Guru Uji',
                'email' => 'guru-uji@example.test',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'guru',
                'kelas_id' => $kelas->id,
            ])
            ->assertRedirect(route('admin.users.index'));

        $teacher = User::where('email', 'guru-uji@example.test')->firstOrFail();

        $this->assertDatabaseHas('kelas', [
            'id' => $kelas->id,
            'wali_kelas_id' => $teacher->id,
        ]);
    }

    public function test_admin_exam_create_page_renders_with_correct_routes(): void
    {
        $admin = $this->createUser('admin');

        $this->actingAs($admin)
            ->get(route('admin.exams.create'))
            ->assertOk()
            ->assertSee('Buat Ujian Baru (Admin)')
            ->assertSee(route('admin.exams.index'));
    }

    private function createUser(string $role): User
    {
        return User::create([
            'name' => ucfirst($role) . ' Uji',
            'email' => uniqid($role . '-', true) . '@example.test',
            'password' => Hash::make('password'),
            'role' => $role,
        ]);
    }
}