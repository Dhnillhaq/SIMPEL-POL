<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('perbaikan', function (Blueprint $table) {
            $table->text('detail_perbaikan')->nullable()->after('tanggal_selesai'); // Kolom untuk detail perbaikan
        });
    }

    public function down()
    {
        Schema::table('perbaikan', function (Blueprint $table) {
            $table->dropColumn(['detail_perbaikan']); // Menghapus kolom detail_perbaikan dan gambar_perbaikan
        });
    }
};
