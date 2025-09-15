<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Perbaikan;
use App\Models\GambarPerbaikan;

class GambarPerbaikanSeeder extends Seeder
{
    public function run()
    {
        $perbaikans = Perbaikan::all();

        foreach ($perbaikans as $perbaikan) {
            GambarPerbaikan::create([
                'id_perbaikan' => $perbaikan->id_perbaikan,
                'path_gambar' => 'img/no-image.svg',
            ]);
        }
    }
}

