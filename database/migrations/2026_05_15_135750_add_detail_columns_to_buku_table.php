<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buku', function (Blueprint $table) {

            $table->string('Cover')->nullable();

            $table->text('Deskripsi')->nullable();

            $table->integer('Stok')->default(0);

        });
    }

    public function down(): void
    {
        Schema::table('buku', function (Blueprint $table) {

            $table->dropColumn([
                'Cover',
                'Deskripsi',
                'Stok'
            ]);

        });
    }
};