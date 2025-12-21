<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. BUAT ADMIN
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@dishut.com',
            'password' => Hash::make('password'), // passwordnya: password
            'role' => 'admin',
            'jabatan' => 'Administrator Sistem',
        ]);

        // 2. BUAT KABID (Kepala Bidang)
        $kabid = User::create([
            'name' => 'Bapak Budi (Kabid)',
            'email' => 'kabid@dishut.com',
            'password' => Hash::make('password'),
            'role' => 'kabid',
            'jabatan' => 'Kepala Bidang Konservasi',
        ]);

        // 3. BUAT KASI (Kepala Seksi) - Bawahan Kabid
        // Perhatikan 'parent_id' mengambil id dari $kabid
        $kasi = User::create([
            'name' => 'Ibu Siti (Kasi)',
            'email' => 'kasi@dishut.com',
            'password' => Hash::make('password'),
            'role' => 'kasi',
            'parent_id' => $kabid->id, // INI KUNCI HIERARKINYA
            'jabatan' => 'Kasi Rehabilitasi Hutan',
        ]);

        // 4. BUAT STAF - Bawahan Kasi
        // Perhatikan 'parent_id' mengambil id dari $kasi
        User::create([
            'name' => 'Mas Asep (Staf)',
            'email' => 'staf@dishut.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'parent_id' => $kasi->id, // INI KUNCI HIERARKINYA
            'jabatan' => 'Staf Lapangan',
        ]);

        // Buat satu staf lagi biar ramai
        User::create([
            'name' => 'Mba Dewi (Staf)',
            'email' => 'staf2@dishut.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'parent_id' => $kasi->id,
            'jabatan' => 'Staf Administrasi',
        ]);
    }
}
