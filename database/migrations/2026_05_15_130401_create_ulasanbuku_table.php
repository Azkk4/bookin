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
        Schema::create('ulasanbuku', function (Blueprint $table) {
            $table->id('UlasanID');
            $table->foreignId('UserID');
            $table->foreignId('BukuID');
            $table->text('Ulasan')->nullable();
            $table->integer('Rating')->nullable();
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
        Schema::dropIfExists('ulasanbuku');
    }
};
