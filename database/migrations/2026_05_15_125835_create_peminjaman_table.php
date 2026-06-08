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
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id('PeminjamanID');
            $table->foreignId('UserID');
            $table->foreignId('BukuID');
            $table->date('TanggalPeminjaman')->nullable();
            $table->date('TanggalPengembalian')->nullable();
            $table->string('StatusPeminjaman', 50)->nullable();
            $table->timestamps();

            // FOREIGN KEY USER
            $table->foreign('UserID')
                ->references('UserID')
                ->on('user')
                ->onDelete('cascade');

            // FOREIGN KEY BUKU
            $table->foreign('BukuID')
                ->references('BukuID')
                ->on('buku')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
