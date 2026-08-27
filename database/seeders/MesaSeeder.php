<?php

namespace Database\Seeders;

use App\Models\Mesa;
use Illuminate\Database\Seeder;

class MesaSeeder extends Seeder
{
    public function run(): void
    {

        for ($i = 1; $i <= 16; $i++) {
            Mesa::updateOrCreate(['numero' => 'L' . $i], ['piso' => 1, 'precio' => 150.00]);
        }

        for ($i = 1; $i <= 13; $i++) {
            Mesa::updateOrCreate(['numero' => 'R' . $i], ['piso' => 1, 'precio' => 180.00]);
        }

        foreach (['D1', 'D2'] as $codigo) {
            Mesa::updateOrCreate(['numero' => $codigo], ['piso' => 1, 'precio' => 200.00]);
        }

        for ($i = 1; $i <= 16; $i++) {
            Mesa::updateOrCreate(['numero' => 'F' . $i], ['piso' => 2, 'precio' => 220.00]);
        }

        for ($i = 1; $i <= 4; $i++) {
            Mesa::updateOrCreate(['numero' => 'A' . $i], ['piso' => 2, 'precio' => 300.00]);
        }
    }
}
