<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisObatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nonaktifkan foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Hapus data lama
        DB::table('jenis_obat')->truncate();

        // Tambahkan data baru
        DB::table('jenis_obat')->insert([
            ['nama_jenis' => 'Tablet', 'created_at' => now(), 'updated_at' => now()],
            ['nama_jenis' => 'Sirup', 'created_at' => now(), 'updated_at' => now()],
            ['nama_jenis' => 'Injeksi', 'created_at' => now(), 'updated_at' => now()],
            ['nama_jenis' => 'Kapsul', 'created_at' => now(), 'updated_at' => now()],
            ['nama_jenis' => 'Salep', 'created_at' => now(), 'updated_at' => now()],
            ['nama_jenis' => 'Suspensi', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Aktifkan kembali foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
