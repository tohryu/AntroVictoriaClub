<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $ahora = now();

        $mesas = [];

        // BL1 - BL6: mesas redondas del lado izquierdo (junto a las mesas L1-L16).
        for ($i = 1; $i <= 6; $i++) {
            $mesas[] = [
                'numero' => 'BL' . $i,
                'piso' => 1,
                'precio' => 150.00,
                'disponible' => true,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ];
        }

        // BR1 - BR6: mesas redondas del lado derecho (junto a las mesas R1-R13).
        for ($i = 1; $i <= 6; $i++) {
            $mesas[] = [
                'numero' => 'BR' . $i,
                'piso' => 1,
                'precio' => 180.00,
                'disponible' => true,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ];
        }

        foreach ($mesas as $mesa) {
            DB::table('mesas')->updateOrInsert(
                ['numero' => $mesa['numero']],
                $mesa
            );
        }
    }

    public function down(): void
    {
        $codigos = [];

        for ($i = 1; $i <= 6; $i++) {
            $codigos[] = 'BL' . $i;
            $codigos[] = 'BR' . $i;
        }

        DB::table('mesas')->whereIn('numero', $codigos)->delete();
    }
};
