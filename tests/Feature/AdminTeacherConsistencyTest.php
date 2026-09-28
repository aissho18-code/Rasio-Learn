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

    public function test_canonical_user_role_is_the_only_role_used_for_teacher_lookup(): void
    {
        $student = $this->createUser('siswa');
        Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']);
        $student->assignRole('guru');
        $teacher = $this->createUser('guru');

        $this->assertFalse(User::forRoles('guru')->whereKey($student->id)->exists());
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

    public function test_class_wali_must_have_the_canonical_guru_role(): void
    {
        $admin = $this->createUser('admin');
        $student = $this->createUser('siswa');

        $this->actingAs($admin)
            ->post(route('admin.kelas.store'), [
                'nama_kelas' => 'Kelas Validasi Wali',
                'wali_kelas_id' => $student->id,
            ])
            ->assertSessionHasErrors('wali_kelas_id');

        $this->assertDatabaseMissing('kelas', ['nama_kelas' => 'Kelas Validasi Wali']);
    }

    public function test_admin_can_view_active_user_counts_and_guru_is_limited_to_owned_classes(): void
    {
        $admin = $this->createUser('admin');
        $teacher = $this->createUser('guru');
        $student = $this->createUser('siswa');
        $otherStudent = $this->createUser('siswa');
        Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']);
        $student->assignRole('guru');
        $ownedClass = Kelas::create(['nama_kelas' => 'Kelas Guru', 'wali_kelas_id' => $teacher->id]);
        $otherClass = Kelas::create(['nama_kelas' => 'Kelas Lain']);
        $student->siswaProfile()->create(['kelas_id' => $ownedClass->id]);
        $otherStudent->siswaProfile()->create(['kelas_id' => $otherClass->id]);
        $teacher->forceFill(['last_activity_at' => now()])->saveQuietly();
        $student->forceFill(['last_activity_at' => now()])->saveQuietly();
        $otherStudent->forceFill(['last_activity_at' => now()->subHour()])->saveQuietly();

        $this->actingAs($admin)
            ->getJson(route('admin.monitoring.status'))
            ->assertOk()
            ->assertJsonPath('counts.guru', 1)
            ->assertJsonPath('counts.guru_active', 1)
            ->assertJsonPath('counts.siswa', 2)
            ->assertJsonPath('counts.siswa_active', 1)
            ->assertJsonPath('counts.total_active', 2);

        $this->actingAs($teacher)
            ->getJson(route('guru.monitoring.status', ['kelas_id' => $ownedClass->id]))
            ->assertOk()
            ->assertJsonPath('classes.0.id', $ownedClass->id)
            ->assertJsonPath('classes.0.students.0.id', $student->id)
            ->assertJsonPath('classes.0.students.0.is_active', true);

        $this->getJson(route('guru.monitoring.status', ['kelas_id' => $otherClass->id]))
            ->assertNotFound();

        $this->actingAs($student)
            ->getJson(route('admin.monitoring.status'))
            ->assertForbidden();
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