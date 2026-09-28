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
        $teacher = $this->createUser('guru');
        $student = $this->createUser('siswa');
        $admin = $this->createUser('admin');

        $this->from(route('login', ['role' => 'siswa']))
            ->post(route('login'), [
                'email' => $teacher->email,
                'password' => 'password',
                'role' => 'siswa',
            ])
            ->assertSessionHasErrors('email');

        $this->assertGuest();

        $this->from(route('login', ['role' => 'guru']))
            ->post(route('login'), [
                'email' => $student->email,
                'password' => 'password',
                'role' => 'guru',
            ])
            ->assertSessionHasErrors('email');

        $this->assertGuest();

        $this->from(route('login', ['role' => 'admin']))
            ->post(route('login'), [
                'email' => $student->email,
                'password' => 'password',
                'role' => 'admin',
            ])
            ->assertSessionHasErrors('email');

        $this->assertGuest();

        $this->from(route('login', ['role' => 'guru']))
            ->post(route('login'), [
                'email' => $teacher->email,
                'password' => 'password',
                'role' => 'guru',
            ])
            ->assertRedirect(route('guru.dashboard'));

        $this->assertAuthenticatedAs($teacher);
        $this->post(route('logout'))->assertRedirect(route('login'));

        $this->from(route('login', ['role' => 'siswa']))
            ->post(route('login'), [
                'email' => $student->email,
                'password' => 'password',
                'role' => 'siswa',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($student);
        $this->post(route('logout'))->assertRedirect(route('login'));

        $this->from(route('login', ['role' => 'admin']))
            ->post(route('login'), [
                'email' => $admin->email,
                'password' => 'password',
                'role' => 'admin',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($admin);
        $this->post(route('logout'))->assertRedirect(route('login'));
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