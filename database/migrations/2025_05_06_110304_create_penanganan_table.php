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
        Schema::create('penanganan', function (Blueprint $table) {
            $table->id('id_penanganan');
            $table->timestamps();

            $table->date('tanggal_perbaikan');
            $table->string('deskripsi_perbaikan');
            $table->unsignedBigInteger('id_user');

            $table->foreign('id_user')->references('id_user')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penanganan');
    }
};
