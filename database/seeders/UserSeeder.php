<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nonaktifkan foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Hapus data lama
        DB::table('user')->truncate();

        // Tambahkan data baru
        DB::table('user')->insert([
            [
                'nip' => '123',
                'password' => bcrypt('pwd123'),
                'level' => 'admin',
                'nama_pegawai' => 'Budi Santoso',
                'jabatan' => 'Kepala Apotik',
                'ruangan' => 'Instalasi Farmasi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nip' => '321',
                'password' => bcrypt('pwd123'),
                'level' => 'operator',
                'nama_pegawai' => 'Myra Dwi',
                'jabatan' => 'Apoteker',
                'ruangan' => 'puskesmas Kaligangsa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Aktifkan kembali foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
