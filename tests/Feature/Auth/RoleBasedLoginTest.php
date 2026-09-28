<?php

namespace Tests\Feature\Auth;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleBasedLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_selected_login_role_must_match_user_role_backend(): void
    {
        $admin = $this->createUser('admin');
        $teacher = $this->createUser('guru');
        $student = $this->createUser('siswa');
        $studentClass = Kelas::create(['nama_kelas' => 'Kelas Login']);
        $student->siswaProfile()->create(['kelas_id' => $studentClass->id]);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $teacher->assignRole('admin');

        $this->from(route('login', ['role' => 'siswa']))
            ->post(route('login'), [
                'email' => $teacher->email,
                'password' => 'password',
                'role' => 'siswa',
            ])
            ->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->withSession(['url.intended' => route('guru.dashboard')])
            ->post(route('login'), [
                'email' => $admin->email,
                'password' => 'password',
                'role' => 'admin',
            ])
            ->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
        $this->assertNotNull($admin->fresh()->last_activity_at);
        $this->get(route('dashboard'))->assertRedirect(route('admin.dashboard'));
        $this->get(route('admin.dashboard'))->assertOk()->assertViewIs('dashboard-admin');
        $this->get(route('guru.dashboard'))->assertForbidden();
        $this->get(route('siswa.dashboard'))->assertForbidden();
        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertNull($admin->fresh()->last_activity_at);

        $this->post(route('login'), [
            'email' => $teacher->email,
            'password' => 'password',
            'role' => 'guru',
        ])->assertRedirect(route('guru.dashboard'));
        $this->assertAuthenticatedAs($teacher);
        $this->get(route('dashboard'))->assertRedirect(route('guru.dashboard'));
        $this->get(route('guru.dashboard'))->assertOk()->assertViewIs('dashboard-guru');
        $this->get(route('admin.dashboard'))->assertForbidden();
        $this->get(route('siswa.dashboard'))->assertForbidden();
        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertNull($teacher->fresh()->last_activity_at);

        $this->post(route('login'), [
            'email' => $student->email,
            'password' => 'password',
            'role' => 'siswa',
        ])->assertRedirect(route('siswa.dashboard'));
        $this->assertAuthenticatedAs($student);
        $this->get(route('dashboard'))->assertRedirect(route('siswa.dashboard'));
        $this->get(route('siswa.dashboard'))->assertOk()->assertViewIs('dashboard-siswa');
        $this->get(route('guru.dashboard'))->assertForbidden();
        $this->get(route('admin.dashboard'))->assertForbidden();
        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertNull($student->fresh()->last_activity_at);

        $this->post(route('login'), [
            'email' => $admin->email,
            'password' => 'password',
            'role' => 'admin',
        ])->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
        $this->get(route('admin.dashboard'))->assertOk()->assertViewIs('dashboard-admin');
    }

    public function test_each_portal_rejects_users_with_a_different_persisted_role(): void
    {
        $student = $this->createUser('siswa');
        $kelas = Kelas::create(['nama_kelas' => 'Kelas Akses']);
        $student->siswaProfile()->create(['kelas_id' => $kelas->id]);

        $this->actingAs($student)
            ->get(route('admin.users.index'))
            ->assertForbidden();

        $this->get(route('guru.dashboard'))->assertForbidden();
        $this->get(route('siswa.ujian.index'))->assertOk();
        $this->get(route('siswa.dashboard'))->assertOk()->assertViewIs('dashboard-siswa');
        $this->get(route('dashboard'))->assertRedirect(route('siswa.dashboard'));
    }

    public function test_unknown_role_cannot_log_in_or_render_a_generic_dashboard(): void
    {
        $unknownUser = $this->createUser('superuser');

        $this->post(route('login'), [
            'email' => $unknownUser->email,
            'password' => 'password',
            'role' => 'admin',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();

        $this->actingAs($unknownUser)
            ->get(route('dashboard'))
            ->assertForbidden();
    }

    private function createUser(string $role): User
    {
        static $sequence = 0;
        $sequence++;

        return User::create([
            'name' => ucfirst($role) . ' Login Test',
            'email' => $role . $sequence . '@example.test',
            'password' => Hash::make('password'),
            'role' => $role,
            'email_verified_at' => now(),
        ]);
    }
}