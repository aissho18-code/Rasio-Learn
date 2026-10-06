<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Spatie\Permission\Models\Role;

class SiswaAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['Abdul Aziz Tri Nugroho', 'aziz01@email.com', 'Aziz@2026'],
            ['Abid Rafif Alaudin', 'rafif02@email.com', 'Rafif@2026'],
            ['Adhimaz Anugrah', 'adham03@email.com', 'Adhimaz@2026'],
            ['Afifah Alfina', 'afifah04@email.com', 'Afifah@2026'],
            ['Aghnia Fahimatul Alya', 'aghnia05@email.com', 'Aghnia@2026'],
            ['Ahmad Badri Mudhhar', 'ahmad06@email.com', 'Ahmad@2026'],
            ['Al Buruuj Mawarda', 'buruuj07@email.com', 'Buruuj@2026'],
            ['Alya Sheva Albelda', 'alya08@email.com', 'Alya@2026'],
            ['Amelia Virna Fitriani', 'amelia09@email.com', 'Amelia@2026'],
            ['Amnia Aqilatus Salwa', 'amnia10@email.com', 'Amnia@2026'],
            ['Angelia Daffa Lindrawati', 'angel11@email.com', 'Angel@2026'],
            ['Azka Sabrina Meylida', 'azka12@email.com', 'Azka@2026'],
            ['Camilla Izzati Hendarto', 'camilla13@email.com', 'Camilla@2026'],
            ['Cinta Rahmadani', 'cinta14@email.com', 'Cinta@2026'],
            ['Diva Jihan Yuwanita', 'diva15@email.com', 'Diva@2026'],
            ['Fadlilah Zahira Safarina', 'fadlilah16@email.com', 'Fadlilah@2026'],
            ['Fahrurrozy Ady Fahmy Kurniawan', 'fahrur17@email.com', 'Fahrur@2026'],
            ['Farros Beryl Rafif Sukmawan', 'farros18@email.com', 'Farros@2026'],
            ['Flora Early Azzulfatul Zahra', 'flora19@email.com', 'Flora@2026'],
            ['Gede Adhi Darma Pradnyana', 'gede20@email.com', 'Gede@2026'],
            ["Laila Qurrota A'yuni", 'laila21@email.com', 'Laila@2026'],
            ['Liha Akhsanul Azhar Kurniawan', 'liha22@email.com', 'Liha@2026'],
            ['Mohammad Firliy Asyfaliy Ghaniy', 'firliy23@email.com', 'Firliy@2026'],
            ['Mutiara Dewi Timurrini', 'mutiara24@email.com', 'Mutiara@2026'],
            ['Muzaffar Prawira Ghifari', 'muza25@email.com', 'Muzaffar@2026'],
            ['Naira Nusyuur Rahma', 'naira26@email.com', 'Naira@2026'],
            ['Nikmatus Syukriyah', 'nikma27@email.com', 'Nikmatus@2026'],
            ['Nila Nailatul Lailiyah', 'nila28@email.com', 'Nila@2026'],
            ['Nur Aulia Rahmadina', 'aulia29@email.com', 'Aulia@2026'],
            ['Rizky Masyhury', 'rizky30@email.com', 'Rizky@2026'],
            ['Salsabila Putri Afandi', 'salsa31@email.com', 'Salsabila@2026'],
            ['Sheli Rahmadina', 'sheli32@email.com', 'Sheli@2026'],
            ['Shintya Olivia Angela', 'shintya33@email.com', 'Shintya@2026'],
            ['Siti Aisyah Ainur Rofiq', 'aisyah34@email.com', 'Aisyah@2026'],
            ['Sofi Nur Lailatul Maulida', 'sofi35@email.com', 'Sofi@2026'],
            ['Syifa Mulyana Andini', 'syifa36@email.com', 'Syifa@2026'],
            ['Vanny Auria Febrian Anggraeni', 'vanny37@email.com', 'Vanny@2026'],
        ];

        $conflictingAccount = User::whereIn('email', array_column($accounts, 1))
            ->whereIn('role', ['admin', 'guru'])
            ->value('email');

        if ($conflictingAccount !== null) {
            throw new RuntimeException("Siswa account email is already assigned to an Admin or Guru: {$conflictingAccount}");
        }

        Role::firstOrCreate(['name' => 'siswa', 'guard_name' => 'web']);

        foreach ($accounts as [$name, $email, $password]) {
            $user = User::firstOrNew(['email' => $email]);
            $user->name = $name;
            $user->password = Hash::make($password);
            $user->plain_password = null;
            $user->role = 'siswa';
            $user->email_verified_at ??= now();
            $user->save();
            $user->syncRoles(['siswa']);
        }

        $this->command?->info('Seeded 37 student accounts.');
    }
}