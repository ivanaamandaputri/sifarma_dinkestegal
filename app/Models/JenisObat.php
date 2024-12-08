<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisObat extends Model
{
    use HasFactory;

    // Menentukan nama tabel secara eksplisit jika diperlukan
    protected $table = 'jenis_obat'; // Pastikan nama tabelnya benar

    // Kolom-kolom yang boleh diisi
    protected $fillable = ['nama_jenis']; // Pastikan ini sesuai dengan kolom di tabel

    // Relasi dengan Obat (satu JenisObat memiliki banyak Obat)
    public function obat()
    {
        return $this->hasMany(Obat::class, 'jenis_obat_id');
    }

    // Relasi dengan Transaksi
    public function transaksi()
    {
        return $this->hasMany(Transaksi::class);
    }

    // Relasi dengan Retur
    public function retur()
    {
        return $this->hasMany(Retur::class);
    }

    // Mutator untuk nama_jenis, mengubah huruf pertama menjadi kapital
    public function setNamaJenisAttribute($value)
    {
        $this->attributes['nama_jenis'] = ucwords(strtolower($value)); // Mengubah huruf pertama menjadi kapital
    }
}
