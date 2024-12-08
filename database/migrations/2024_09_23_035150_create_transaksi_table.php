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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('obat_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('jumlah');
            $table->integer('total');
            $table->date('tanggal');
            $table->integer('acc')->default(0);
            $table->string('status')->default('Pengajuan');
            $table->string('alasan_penolakan')->nullable();
            $table->foreign('obat_id')->references('id')->on('obat');
            $table->foreign('user_id')->references('id')->on('user');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
