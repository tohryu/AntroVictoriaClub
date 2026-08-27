<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $ahora = now();

        $mesas = [
            [
                'numero' => 'E1',
                'piso' => 1,
                'precio' => 100.00,
                'disponible' => true,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
            [
                'numero' => 'E2',
                'piso' => 1,
                'precio' => 140.00,
                'disponible' => true,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
        ];

        foreach ($mesas as $mesa) {
            DB::table('mesas')->updateOrInsert(
                ['numero' => $mesa['numero']],
                $mesa
            );
        }
    }

    public function down(): void
    {
        DB::table('mesas')->whereIn('numero', ['E1', 'E2'])->delete();
    }
};
