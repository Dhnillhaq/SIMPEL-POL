<?php

namespace Database\Seeders;

use App\Models\Aduan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AduanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Aduan::factory()->count(10)->create(

        );
    }
}
