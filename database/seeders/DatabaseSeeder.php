<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Admin
        User::updateOrCreate(
            ['email' => 'admin@email.com'],
            [
                'name'           => 'Administrator',
                'password'       => bcrypt('adminkeren'),
                'plain_password' => 'adminkeren',
                'role'           => 'admin',
            ]
        );

        // 2. Akun Guru
        User::updateOrCreate(
            ['email' => 'guru@email.com'],
            [
                'name'           => 'Guru Matematik Kemren',
                'password'       => bcrypt('gurumtk'),
                'plain_password' => 'gurumtk',
                'role'           => 'guru',
            ]
        );

        // 3. Akun Siswa
        User::updateOrCreate(
            ['email' => 'siswa@email.com'],
            [
                'name'           => 'Siswa Demo',
                'password'       => bcrypt('siswamtk'),
                'plain_password' => 'siswamtk',
                'role'           => 'siswa',
            ]
        );
    }
}