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
        Schema::create('kategoribuku_relasi', function (Blueprint $table) {
            $table->id('KategoriBukuID');
            $table->foreignId('BukuID');
            $table->foreignId('KategoriID');
            $table->timestamps();

            // FOREIGN KEY BUKU
            $table->foreign('BukuID')
                ->references('BukuID')
                ->on('buku')
                ->onDelete('cascade');

            // FOREIGN KEY KATEGORI
            $table->foreign('KategoriID')
                ->references('KategoriID')
                ->on('kategoribuku')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategoribuku_relasi');
    }
};
