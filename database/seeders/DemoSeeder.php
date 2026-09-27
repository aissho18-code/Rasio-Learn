<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Materi;
use App\Models\MateriProgress;
use App\Models\Tugas;
use App\Models\Submission;
use App\Models\PreTest;
use App\Models\PreTestHasil;
use App\Models\GuruProfile;
use App\Models\SiswaProfile;
use Carbon\Carbon;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // pastikan role ada
        Role::firstOrCreate(['name'=>'admin']);
        Role::firstOrCreate(['name'=>'guru']);
        Role::firstOrCreate(['name'=>'siswa']);

        // Admin (jika belum ada)
        $admin = User::firstOrCreate(
            ['email'=>'admin@example.com'],
            ['name'=>'Administrator','password'=>Hash::make('password'),'role'=>'admin']
        );
        $admin->assignRole('admin');

        // Guru demo
        $guru = User::firstOrCreate(
            ['email'=>'guru1@example.com'],
            ['name'=>'Guru Contoh','password'=>Hash::make('password'),'role'=>'guru']
        );
        $guru->assignRole('guru');
        GuruProfile::updateOrCreate(
            ['user_id'=>$guru->id],
            ['nip'=>'GURU001','mapel_id'=>null]
        );

        // Buat kelas
        $kelas = Kelas::firstOrCreate(['nama_kelas'=>'X IPA 1'], ['wali_kelas_id'=>$guru->id]);

        // Buat siswa demo (5 siswa)
        $siswaUsers = [];
        for ($i=1; $i<=5; $i++) {
            $email = "student{$i}@example.com";
            $user = User::firstOrCreate(
                ['email'=>$email],
                ['name'=>"Siswa Demo {$i}",'password'=>Hash::make('password'),'role'=>'siswa']
            );
            $user->assignRole('siswa');
            SiswaProfile::updateOrCreate(
                ['user_id'=>$user->id],
                ['nis'=>sprintf('NIS%03d',$i),'kelas_id'=>$kelas->id]
            );
            $siswaUsers[] = $user;
        }

        // Buat mata pelajaran dan kaitkan ke guru
        $mapel = MataPelajaran::firstOrCreate(['nama_mapel'=>'Matematika'], ['guru_id'=>$guru->id]);
        // update guru profile mapel_id
        $gp = GuruProfile::where('user_id',$guru->id)->first();
        if ($gp) { $gp->update(['mapel_id'=>$mapel->id]); }

        // Buat materi (3 urutan) dengan penambahan data 'pekan'
        $materiList = [];
        for ($u=1; $u<=3; $u++) {
            $m = Materi::create([
                'kelas_id' => $kelas->id,
                'mapel_id' => $mapel->id,
                'urutan' => $u,
                'judul' => "Materi {$u}: Pengenalan Topik {$u}",
                'pekan' => "Pekan {$u}", // <-- PENAMBAHAN KOLOM PEKAN DI SINI
                'konten' => "<p>Konten demo untuk materi {$u}. Penjelasan singkat dan contoh soal.</p>"
            ]);
            $materiList[] = $m;
        }

        // Atur materi progress: unlock materi pertama untuk semua siswa, sisanya locked
        foreach ($siswaUsers as $s) {
            foreach ($materiList as $idx => $m) {
                $status = ($idx === 0) ? 'unlocked' : 'locked';
                MateriProgress::updateOrCreate(
                    ['siswa_id'=>$s->id,'materi_id'=>$m->id],
                    [
                        'status'=>$status,
                        'unlocked_by'=>($status==='unlocked') ? $guru->id : null,
                        'unlocked_at'=>($status==='unlocked') ? Carbon::now() : null
                    ]
                );
            }
        }

        // Buat tugas untuk tiap materi dengan rubrik sederhana (json)
        $tugasList = [];
        foreach ($materiList as $m) {
            $tugas = Tugas::create([
                'materi_id'=>$m->id,
                'judul'=>"Tugas: Latihan Materi {$m->urutan}",
                'instruksi'=>"Kerjakan latihan untuk materi {$m->urutan}.",
                'deadline'=>Carbon::now()->addDays(7),
                'rubrik_penilaian'=>json_encode([
                    ['kriteria'=>'Pemahaman konsep','bobot'=>50],
                    ['kriteria'=>'Ketepatan jawaban','bobot'=>30],
                    ['kriteria'=>'Kerapihan','bobot'=>20],
                ]),
            ]);
            $tugasList[] = $tugas;
        }

        // Buat submission: beberapa siswa submit tugas 1
        $firstTugas = $tugasList[0];
        Submission::create([
            'siswa_id'=>$siswaUsers[0]->id,
            'tugas_id'=>$firstTugas->id,
            'jawaban'=>'Ini jawaban siswa 1 untuk tugas 1.',
            'file_path'=>null,
            'status'=>'menunggu',
        ]);
        Submission::create([
            'siswa_id'=>$siswaUsers[1]->id,
            'tugas_id'=>$firstTugas->id,
            'jawaban'=>'Ini jawaban siswa 2 untuk tugas 1.',
            'ai_score'=>82.5,
            'ai_feedback'=>'Hasil AI: baik, perbaiki bagian definisi.',
            'status'=>'dinilai_ai',
        ]);

        // Contoh pre-test untuk student1
        $pre = PreTest::create(['siswa_id'=>$siswaUsers[0]->id,'detail'=>null]);
        PreTestHasil::create([
            'pre_test_id'=>$pre->id,
            'siswa_id'=>$siswaUsers[0]->id,
            'skor'=>68,
            'jawaban'=>json_encode(['A','B','C','D','A']),
        ]);

        $this->command->info('Demo data seeded: 1 guru, 5 siswa, 1 kelas, mapel & materi, tugas & submissions created.');
    }
}