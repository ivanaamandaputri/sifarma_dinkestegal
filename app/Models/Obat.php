<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    use HasFactory;
    protected $table = 'obat';

    protected $fillable = [
        'nama_obat',
        'dosis',
        'stok',
        'harga',
        'exp',
        'keterangan',
        'foto',
        'jenis_obat_id'
    ];

    public function setNamaObatAttribute($value)
    {
        $this->attributes['nama_obat'] = ucwords(strtolower($value)); // Ubah huruf pertama menjadi kapital
    }

    public function setDosisAttribute($value)
    {
        $this->attributes['dosis'] = ucwords(strtolower($value)); // Ubah huruf pertama menjadi kapital
    }

    public function jenisobat()
    {
        return $this->belongsTo(JenisObat::class, 'jenis_obat_id');
    }
    public function stokMasuk()
    {
        return $this->hasMany(StokMasuk::class);
    }
}
