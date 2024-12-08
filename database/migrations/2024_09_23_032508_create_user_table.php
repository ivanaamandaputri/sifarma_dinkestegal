<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user', function (Blueprint $table) {
            $table->id();
            $table->string('nip')->unique();
            $table->string('password');
            $table->enum('level', ['admin', 'operator'])->default('admin');
            $table->string('foto')->nullable();
            $table->string('nama_pegawai');
            $table->enum('jabatan', ['Kepala Apotik', 'Apoteker', 'Staf']);
            $table->enum('ruangan', [
                'Instalasi Farmasi',
                'puskesmas Kaligangsa',
                'puskesmas Margadana',
                'puskesmas Tegal Barat',
                'puskesmas Debong Lor',
                'puskesmas Tegal Timur',
                'puskesmas Slerok',
                'puskesmas Tegal Selatan',
                'puskesmas Bandung'
            ]);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};
