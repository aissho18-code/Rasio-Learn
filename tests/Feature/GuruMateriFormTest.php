<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Materi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuruMateriFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_guru_materi_form_exposes_save_draft_and_publish_actions(): void
    {
        $user = User::create([
            'name' => 'Guru Test',
            'email' => 'guru.test@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'role' => 'guru',
        ]);

        $response = $this->actingAs($user)->get(route('guru.materi.index'));

        $response->assertOk();
        $response->assertSee('id="materi-form"', false);
        $response->assertSee('id="status-input"', false);
        $response->assertSee('Simpan Draft', false);
        $response->assertSee('Publish', false);
    }

    public function test_guru_can_open_a_dedicated_material_edit_page_with_the_sidebar(): void
    {
        $user = User::create([
            'name' => 'Guru Edit',
            'email' => 'guru.edit@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'role' => 'guru',
        ]);
        $kelas = Kelas::create([
            'nama_kelas' => 'Kelas Edit',
            'wali_kelas_id' => $user->id,
        ]);
        $mapel = MataPelajaran::create(['nama_mapel' => 'Matematika']);
        $materi = Materi::create([
            'judul' => 'Materi untuk diedit',
            'pekan' => 'Pertemuan 3',
            'konten' => 'Konten sebelumnya',
            'mapel_id' => $mapel->id,
            'kelas_id' => $kelas->id,
            'urutan' => 1,
            'status' => 'aktif',
        ]);

        $this->actingAs($user)
            ->get('/guru/materi/' . $materi->id . '/edit')
            ->assertOk()
            ->assertSee('Edit Materi')
            ->assertSee('Materi untuk diedit')
            ->assertSee('Kelola Materi');
    }

    public function test_guru_can_store_materi_as_draft_action(): void
    {
        $user = User::create([
            'name' => 'Guru Draft',
            'email' => 'guru.draft@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'role' => 'guru',
        ]);

        Kelas::create([
            'nama_kelas' => 'Kelas VII',
            'wali_kelas_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->post(route('guru.materi.store'), [
            'judul' => 'Materi Draft Baru',
            'pekan' => 'Pertemuan 1',
            'kelas_id' => Kelas::where('wali_kelas_id', $user->id)->first()->id,
            'konten' => 'Isi draft materi',
            'status' => 'draft',
        ]);

        $response->assertRedirect(route('guru.materi.index'));
        $this->assertDatabaseHas('materi', [
            'judul' => 'Materi Draft Baru',
            'status' => 'draft',
        ]);
    }

    public function test_guru_can_publish_materi_and_update_it(): void
    {
        $user = User::create([
            'name' => 'Guru Publish',
            'email' => 'guru.publish@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'role' => 'guru',
        ]);

        $mapel = MataPelajaran::create(['nama_mapel' => 'Matematika']);
        $kelas = Kelas::create([
            'nama_kelas' => 'Kelas VII',
            'wali_kelas_id' => $user->id,
        ]);

        $materi = Materi::create([
            'judul' => 'Materi Lama',
            'pekan' => 'Pertemuan 1',
            'konten' => 'Konten lama',
            'mapel_id' => $mapel->id,
            'kelas_id' => $kelas->id,
            'urutan' => 1,
            'status' => 'draft',
        ]);

        $this->actingAs($user)
            ->put(route('guru.materi.update', $materi->id), [
                'judul' => 'Materi Diperbarui',
                'pekan' => 'Pertemuan 2',
                'konten' => 'Konten baru',
                'status' => 'aktif',
            ])
            ->assertRedirect(route('guru.materi.index'));

        $this->assertDatabaseHas('materi', [
            'id' => $materi->id,
            'judul' => 'Materi Diperbarui',
            'status' => 'aktif',
        ]);
    }
}
