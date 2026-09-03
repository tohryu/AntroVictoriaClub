<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dias_operacion_general', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('dia_semana')->unique();
            $table->boolean('activo')->default(false);
            $table->timestamps();
        });

        foreach ([0, 1, 2, 3, 4, 5, 6] as $dia) {
            DB::table('dias_operacion_general')->insert([
                'dia_semana' => $dia,
                'activo' => in_array($dia, [4, 5, 6]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('dias_operacion_general');
    }
};
