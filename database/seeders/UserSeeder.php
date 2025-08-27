<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
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
                'nip' => '123456',
                'password' => bcrypt('pwd123'),
                'level' => 'admin',
                'nama_pegawai' => 'Aziz Poetra',
                'jabatan' => 'Staf',
                'ruangan' => 'Instalasi Farmasi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nip' => '122211',
                'password' => bcrypt('pwd123'),
                'level' => 'operator',
                'nama_pegawai' => 'Santi Sumarni',
                'jabatan' => 'Kepala Apotik',
                'ruangan' => 'puskesmas Kaligangsa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nip' => '122212',
                'password' => bcrypt('pwd123'),
                'level' => 'operator',
                'nama_pegawai' => 'Budi Gunawan',
                'jabatan' => 'Apoteker',
                'ruangan' => 'puskesmas Margadana',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nip' => '122213',
                'password' => bcrypt('pwd123'),
                'level' => 'operator',
                'nama_pegawai' => 'Khasnah Maesaroh',
                'jabatan' => 'Kepala Apotik',
                'ruangan' => 'puskesmas Tegal Barat',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nip' => '122214',
                'password' => bcrypt('pwd123'),
                'level' => 'operator',
                'nama_pegawai' => 'Rizky Fadil',
                'jabatan' => 'Apoteker',
                'ruangan' => 'puskesmas Debong Lor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Aktifkan kembali foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
