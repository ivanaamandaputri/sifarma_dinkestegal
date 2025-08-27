<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokPuskesmas extends Model
{
    use HasFactory;

    protected $table = 'stok_puskesmas';

    protected $fillable = [
        'user_id', 'obat_id', 'jumlah'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function obat()
    {
        return $this->belongsTo(Obat::class);
    }
}
