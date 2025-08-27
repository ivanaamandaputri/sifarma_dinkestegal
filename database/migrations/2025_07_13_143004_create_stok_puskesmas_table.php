<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('stok_puskesmas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // puskesmas (operator)
            $table->unsignedBigInteger('obat_id'); // obat yang diterima
            $table->integer('jumlah')->default(0); // stok yang tersedia di puskesmas
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
            $table->foreign('obat_id')->references('id')->on('obat')->onDelete('cascade');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_puskesmas');
    }
};
