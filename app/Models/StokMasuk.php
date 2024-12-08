<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokMasuk extends Model
{
    use HasFactory;
    protected $table = 'stok_masuk';


    protected $fillable = [
        'obat_id',     // Pastikan ini ditambahkan
        'jumlah',
        'sumber',
        'tanggal',
    ];

    public function obat()
    {
        return $this->belongsTo(Obat::class);
    }
}
