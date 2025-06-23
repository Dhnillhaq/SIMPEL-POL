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
        Schema::create('gambar_perbaikan', function (Blueprint $table) {
            $table->id('id_gambar_perbaikan');
            $table->unsignedBigInteger('id_perbaikan');
            $table->foreign('id_perbaikan')
                ->references('id_perbaikan')
                ->on('perbaikan')
                ->onDelete('cascade');
            $table->string('path_gambar');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gambar_perbaikan');
    }
};
