<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'user';

    protected $fillable = [
        'nip',
        'kode_user',
        'password',
        'level',
        'foto',
        'nama_pegawai',
        'jabatan',
        'ruangan',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function setNamaPegawaiAttribute($value)
    {
        $this->attributes['nama_pegawai'] = ucwords(strtolower($value)); // Ubah huruf pertama menjadi kapital
    }

    public function setNipAttribute($value)
    {
        $this->attributes['nip'] = strtoupper($value); // Ubah menjadi huruf kapital
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = ($value);
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class);  // Satu user dapat memiliki banyak transaksi
    }


    public function stokMasuk()
    {
        return $this->hasMany(StokMasuk::class, 'user_id');
    }

    public function pemakaianObat()
    {
        return $this->hasMany(PemakaianObat::class, 'user_id');
    }

    public function stokPuskesmas()
    {
        return $this->hasMany(StokPuskesmas::class, 'user_id');
    }
}
