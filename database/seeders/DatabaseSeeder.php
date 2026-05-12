<?php

namespace Database\Seeders;

use App\Models\Delivery;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // === PETUGAS (KURIR) ===
        $petugas1 = User::create([
            'name' => 'Ahmad Kurir',
            'email' => 'petugas@mbg.test',
            'password' => Hash::make('password'),
            'role' => 'petugas',
            'phone' => '081234567890',
            'is_active' => true,
        ]);

        $petugas2 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'petugas2@mbg.test',
            'password' => Hash::make('password'),
            'role' => 'petugas',
            'phone' => '082345678901',
            'is_active' => true,
        ]);

        // === GURU ===
        $guru1 = User::create([
            'name' => 'Ibu Siti Rahayu',
            'email' => 'guru@mbg.test',
            'password' => Hash::make('password'),
            'role' => 'guru',
            'phone' => '083456789012',
            'is_active' => true,
        ]);

        $guru2 = User::create([
            'name' => 'Pak Joko Widodo',
            'email' => 'guru2@mbg.test',
            'password' => Hash::make('password'),
            'role' => 'guru',
            'phone' => '084567890123',
            'is_active' => true,
        ]);

        // === ORANG TUA ===
        $ortu1 = User::create([
            'name' => 'Pak Bambang',
            'email' => 'orangtua@mbg.test',
            'password' => Hash::make('password'),
            'role' => 'orangtua',
            'phone' => '085678901234',
            'is_active' => true,
        ]);

        $ortu2 = User::create([
            'name' => 'Ibu Dewi',
            'email' => 'orangtua2@mbg.test',
            'password' => Hash::make('password'),
            'role' => 'orangtua',
            'phone' => '086789012345',
            'is_active' => true,
        ]);

        $ortu3 = User::create([
            'name' => 'Pak Hendra',
            'email' => 'orangtua3@mbg.test',
            'password' => Hash::make('password'),
            'role' => 'orangtua',
            'phone' => '087890123456',
            'is_active' => true,
        ]);

        // === SEKOLAH ===
        $school1 = School::create([
            'name' => 'SD Negeri 001 Sukajaya',
            'address' => 'Jl. Pendidikan No. 1, Sukajaya',
            'kelurahan' => 'Sukajaya',
            'kecamatan' => 'Pekanbaru Kota',
            'latitude' => 0.5332,
            'longitude' => 101.4474,
            'principal_name' => 'Drs. Suparman',
            'phone' => '0761-12345',
            'teacher_id' => $guru1->id,
        ]);

        $school2 = School::create([
            'name' => 'Madrasah Aliyah Unggulan',
            'address' => 'Rantau Baru',
            'kelurahan' => 'Kerinci',
            'kecamatan' => 'Pangkalan Kerinci',
            'latitude' => 0.5095,
            'longitude' => 101.4781,
            'principal_name' => 'Hj. Marwah, S.Pd',
            'phone' => '0761-67890',
            'teacher_id' => $guru2->id,
        ]);

        // === SISWA ===
        Student::create([
            'name' => 'Andi Putra Bambang',
            'nis' => '2024001',
            'parent_id' => $ortu1->id,
            'school_id' => $school1->id,
            'class' => '3A',
            'gender' => 'L',
        ]);

        Student::create([
            'name' => 'Putri Dewi',
            'nis' => '2024002',
            'parent_id' => $ortu2->id,
            'school_id' => $school1->id,
            'class' => '4B',
            'gender' => 'P',
        ]);

        Student::create([
            'name' => 'Rizki Hendra',
            'nis' => '2024003',
            'parent_id' => $ortu3->id,
            'school_id' => $school2->id,
            'class' => '2A',
            'gender' => 'L',
        ]);

        Student::create([
            'name' => 'Sari Wulandari',
            'nis' => '2024004',
            'parent_id' => $ortu1->id,
            'school_id' => $school1->id,
            'class' => '5C',
            'gender' => 'P',
        ]);

        // === CONTOH DATA PENGIRIMAN RIWAYAT ===
        $delivery = Delivery::create([
            'kode_pengiriman' => 'MBG20240101001',
            'courier_id' => $petugas1->id,
            'school_id' => $school1->id,
            'status' => 'selesai',
            'total_portions' => 30,
            'notes' => 'Semua berjalan lancar',
            'started_at' => now()->subDays(1)->setTime(8, 0),
            'arrived_at' => now()->subDays(1)->setTime(9, 30),
            'completed_at' => now()->subDays(1)->setTime(10, 0),
            'delivery_date' => today()->subDay(),
        ]);

        $this->command->info('✅ Seeder selesai! Akun yang tersedia:');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Petugas/Kurir', 'petugas@mbg.test', 'password'],
                ['Petugas/Kurir 2', 'petugas2@mbg.test', 'password'],
                ['Guru', 'guru@mbg.test', 'password'],
                ['Guru 2', 'guru2@mbg.test', 'password'],
                ['Orang Tua', 'orangtua@mbg.test', 'password'],
                ['Orang Tua 2', 'orangtua2@mbg.test', 'password'],
                ['Orang Tua 3', 'orangtua3@mbg.test', 'password'],
            ]
        );
    }
}
