<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Pengumuman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengumumanAudienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_publish_an_announcement_to_a_selected_audience(): void
    {
        $admin = $this->createUser('admin');

        $this->actingAs($admin)
            ->get(route('admin.pengumuman.create'))
            ->assertOk()
            ->assertSee('Portal Admin')
            ->assertSee('Sasaran Pengumuman')
            ->assertSee('Guru saja')
            ->assertSee('Siswa saja')
            ->assertSee('Guru dan siswa');

        $this->actingAs($admin)
            ->post(route('admin.pengumuman.store'), [
                'target_audience' => 'guru',
                'judul' => 'Rapat Guru',
                'isi' => 'Rapat dimulai pukul 10.',
            ])
            ->assertRedirect(route('admin.pengumuman.index'));

        $this->assertDatabaseHas('pengumuman', [
            'guru_id' => $admin->id,
            'target_audience' => 'guru',
            'judul' => 'Rapat Guru',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.pengumuman.index'))
            ->assertOk()
            ->assertSee('Portal Admin')
            ->assertSee('Rapat Guru');

        $this->actingAs($admin)
            ->post(route('admin.pengumuman.store'), [
                'target_audience' => 'orangtua',
                'judul' => 'Target tidak valid',
                'isi' => 'Isi pengumuman',
            ])
            ->assertSessionHasErrors('target_audience');
    }

    public function test_teacher_can_publish_an_announcement_to_their_class(): void
    {
        $teacher = $this->createUser('guru');
        $kelas = Kelas::create([
            'nama_kelas' => 'Kelas Pengumuman Guru',
            'wali_kelas_id' => $teacher->id,
        ]);

        $this->actingAs($teacher)
            ->post(route('guru.pengumuman.store'), [
                'kelas_id' => $kelas->id,
                'judul' => 'Pengumuman Kelas',
                'isi' => 'Informasi untuk siswa kelas.',
            ])
            ->assertRedirect(route('guru.pengumuman.index'));

        $this->assertDatabaseHas('pengumuman', [
            'guru_id' => $teacher->id,
            'kelas_id' => $kelas->id,
            'target_audience' => 'siswa',
            'judul' => 'Pengumuman Kelas',
        ]);
    }

    public function test_teacher_and_student_only_see_announcements_for_their_audience(): void
    {
        $admin = $this->createUser('admin');
        $teacher = $this->createUser('guru');
        $student = $this->createUser('siswa');
        $class = Kelas::create(['nama_kelas' => 'Kelas Audience']);
        $student->siswaProfile()->create(['kelas_id' => $class->id]);

        $teacherAnnouncement = $this->createAnnouncement($admin, 'guru', 'Untuk Guru');
        $this->createAnnouncement($admin, 'siswa', 'Untuk Siswa');
        $this->createAnnouncement($admin, 'semua', 'Untuk Semua');

        $this->actingAs($teacher)
            ->get(route('guru.pengumuman.index'))
            ->assertOk()
            ->assertSee('Untuk Guru')
            ->assertSee('Untuk Semua')
            ->assertDontSee('Untuk Siswa');

        $this->actingAs($student)
            ->get(route('siswa.pengumuman.index'))
            ->assertOk()
            ->assertSee('Untuk Siswa')
            ->assertSee('Untuk Semua')
            ->assertDontSee('Untuk Guru');

        $this->get(route('siswa.pengumuman.show', $teacherAnnouncement->id))
            ->assertForbidden();
    }

    private function createUser(string $role): User
    {
        static $sequence = 0;
        $sequence++;

        return User::create([
            'name' => ucfirst($role) . ' Audience Test',
            'email' => $role . '-audience-' . $sequence . '@example.test',
            'password' => 'password',
            'role' => $role,
            'email_verified_at' => now(),
        ]);
    }

    private function createAnnouncement(User $admin, string $audience, string $title): Pengumuman
    {
        return Pengumuman::create([
            'guru_id' => $admin->id,
            'target_audience' => $audience,
            'judul' => $title,
            'isi' => 'Isi pengumuman.',
            'diterbitkan_at' => now(),
        ]);
    }
}