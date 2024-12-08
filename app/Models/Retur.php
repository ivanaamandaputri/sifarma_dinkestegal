<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retur extends Model
{
    use HasFactory;

    protected $table = 'retur';

    protected $fillable = [
        'transaksi_id',
        'obat_id',
        'user_id',
        'password',
        'jumlah',
        'alasan_retur',
        'status'
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }

    public function obat()
    {
        return $this->belongsTo(Obat::class, 'obat_id');
    }

    public function jenisobat()
    {
        return $this->belongsTo(JenisObat::class, 'jenis_obat_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
