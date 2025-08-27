<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemakaianObat extends Model
{
    use HasFactory;

    protected $table = 'pemakaian_obat';

    protected $fillable = [
        'obat_id',
        'user_id',
        'nama_pasien',
        'stok_awal',
        'stok_keluar',
        'stok_sisa',
        'keterangan',
        'tanggal'
    ];

    public function obat()
    {
        return $this->belongsTo(Obat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
