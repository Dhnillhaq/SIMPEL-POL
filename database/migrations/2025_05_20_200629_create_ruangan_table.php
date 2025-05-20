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
        Schema::create('ruangan', function (Blueprint $table) {
            $table->id('id_ruangan');

            $table->string('kode_ruangan', 10)->unique();
            $table->string('nama_ruangan', 50)->unique();

            $table->unsignedBigInteger('id_lantai');
            $table->foreign('id_lantai')->references('id_lantai')->on('lantai');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ruangan');
    }
};
