<?php

namespace Database\Seeders;

use App\Http\Enums\JenisKriteria;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'kode_kriteria' => 'JLP',
                'nama_kriteria' => 'Jumlah Pelapor',
                'jenis_kriteria' => JenisKriteria::BENEFIT,
                'bobot' => 15
            ],
            [
                'kode_kriteria' => 'UGS',
                'nama_kriteria' => 'Urgensi',
                'jenis_kriteria' => JenisKriteria::BENEFIT,
                'bobot' => 23
            ],
            [
                'kode_kriteria' => 'PRB',
                'nama_kriteria' => 'Perkiraan Biaya',
                'jenis_kriteria' => JenisKriteria::COST,
                'bobot' => 31
            ],
            [
                'kode_kriteria' => 'TKR',
                'nama_kriteria' => 'Tingkat Kerusakan',
                'jenis_kriteria' => JenisKriteria::BENEFIT,
                'bobot' => 12
            ],
            [
                'kode_kriteria' => 'LPB',
                'nama_kriteria' => 'Laporan Berulang',
                'jenis_kriteria' => JenisKriteria::BENEFIT,
                'bobot' => 7
            ],
            [
                'kode_kriteria' => 'BTP',
                'nama_kriteria' => 'Bobot Pelapor',
                'jenis_kriteria' => JenisKriteria::BENEFIT,
                'bobot' => 12
            ],
        ];

        DB::table('kriteria')->insert($data);
    }
}