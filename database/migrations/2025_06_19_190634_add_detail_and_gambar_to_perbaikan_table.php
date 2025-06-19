<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailAndGambarToPerbaikanTable extends Migration
{
    public function up()
    {
        Schema::table('perbaikan', function (Blueprint $table) {
            $table->text('detail_perbaikan')->nullable()->after('tanggal_selesai'); // Kolom untuk detail perbaikan
            $table->string('gambar_perbaikan')->nullable()->after('detail_perbaikan'); // Kolom untuk gambar perbaikan
        });
    }

    public function down()
    {
        Schema::table('perbaikan', function (Blueprint $table) {
            $table->dropColumn(['detail_perbaikan', 'gambar_perbaikan']);
        });
    }
}