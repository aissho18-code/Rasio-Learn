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

    public function test_login_redirects_to_the_persisted_role_even_when_intended_and_form_role_disagree(): void
    {
        $admin = $this->createUser('admin');
        $teacher = $this->createUser('guru');
        $student = $this->createUser('siswa');
        $teacher->assignRole(Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']));

        $this->withSession(['url.intended' => route('guru.dashboard')])
            ->post(route('login'), [
                'email' => $admin->email,
                'password' => 'password',
                'role' => 'siswa',
            ])
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($admin);
        $this->get(route('dashboard'))->assertOk()->assertViewIs('dashboard-admin');
        $this->post(route('logout'))->assertRedirect(route('login'));

        $this->withSession(['url.intended' => route('admin.users.index')])
            ->post(route('login'), [
                'email' => $teacher->email,
                'password' => 'password',
                'role' => 'admin',
            ])
            ->assertRedirect(route('guru.dashboard'));
        $this->assertAuthenticatedAs($teacher);
        $this->get(route('dashboard'))->assertRedirect(route('guru.dashboard'));
        $this->post(route('logout'))->assertRedirect(route('login'));

        $kelas = Kelas::create(['nama_kelas' => 'Kelas Login']);
        $student->siswaProfile()->create(['kelas_id' => $kelas->id]);

        $this->post(route('login'), [
            'email' => $student->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($student);
        $this->get(route('dashboard'))->assertOk()->assertViewIs('dashboard-siswa');
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

        $this->get(route('dashboard'))->assertOk()->assertViewIs('dashboard-siswa');
    }

    public function test_unknown_role_cannot_log_in_or_render_a_generic_dashboard(): void
    {
        $unknownUser = $this->createUser('superuser');

        $this->post(route('login'), [
            'email' => $unknownUser->email,
            'password' => 'password',
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