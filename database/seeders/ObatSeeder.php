<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ObatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nonaktifkan foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Hapus data lama
        DB::table('obat')->truncate();

        // Tambahkan data baru
        DB::table('obat')->insert([
            ['jenis_obat_id' => 1, 'nama_obat' => 'Amoxicillin Caps', 'dosis' => '500mg', 'stok' => 10, 'harga' => 2000, 'exp' => '2025-12-31', 'created_at' => now(), 'updated_at' => now()],
            ['jenis_obat_id' => 2, 'nama_obat' => 'Cetirizine', 'dosis' => '10mg', 'stok' => 8000, 'harga' => 3000, 'exp' => '2025-12-31', 'created_at' => now(), 'updated_at' => now()],
            ['jenis_obat_id' => 1, 'nama_obat' => 'Ibuprofen', 'dosis' => '400mg', 'stok' => 0, 'harga' => 2500, 'exp' => '2025-12-31', 'created_at' => now(), 'updated_at' => now()],
            ['jenis_obat_id' => 1, 'nama_obat' => 'Paracetamol', 'dosis' => '500mg', 'stok' => 8000, 'harga' => 500, 'exp' => '2025-12-31', 'created_at' => now(), 'updated_at' => now()],
            ['jenis_obat_id' => 3, 'nama_obat' => 'Tolak Angin', 'dosis' => '15ml', 'stok' => 8000, 'harga' => 2000, 'exp' => '2025-12-31', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Aktifkan kembali foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
