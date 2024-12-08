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
        Schema::create('obat', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jenis_obat_id');
            $table->string('nama_obat');
            $table->string('dosis');
            $table->integer('stok');
            $table->double('harga');
            $table->date('exp')->nullable();
            $table->longtext('keterangan')->nullable();
            $table->string('foto')->nullable();
            $table->foreign('jenis_obat_id')->references('id')->on('jenis_obat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obat');
    }
};
