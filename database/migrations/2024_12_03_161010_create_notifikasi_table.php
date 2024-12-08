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
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // ID user yang menerima notifikasi
            $table->string('judul'); // Judul notifikasi
            $table->text('pesan'); // Isi pesan notifikasi
            $table->boolean('dibaca')->default(false); // Status baca
            $table->enum('level', ['admin', 'operator']); // Level notifikasi
            $table->timestamps();
            // Relasi ke tabel user
            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
