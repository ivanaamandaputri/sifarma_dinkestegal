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
        Schema::create('stok_masuk', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('obat_id'); // Relasi ke tabel obat
            $table->integer('jumlah'); // Jumlah stok yang masuk
            $table->string('sumber')->nullable(); // Sumber stok masuk, misalnya Supplier
            $table->date('tanggal'); // Tanggal stok masuk
            $table->timestamps();
            $table->foreign('obat_id')->references('id')->on('obat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_masuk');
    }
};
