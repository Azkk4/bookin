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
        Schema::create('auditlog', function (Blueprint $table) {

            $table->id('LogID');

            $table->unsignedBigInteger('UserID');

            $table->string('Aksi');

            $table->text('Deskripsi');

            $table->timestamp('CreatedAt')
                ->useCurrent();

            $table->foreign('UserID')
                ->references('UserID')
                ->on('user')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditlog');
    }
};
