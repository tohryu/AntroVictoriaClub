<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            // Candado manual: aunque el evento ya sea "el próximo" por fecha,
            // las reservas de mesa y compras de cover NO se abren hasta que
            // el admin confirme que ya actualizó los precios y active esto.
            $table->boolean('ventas_activas')->default(false)->after('activo');
        });
    }

    public function down(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->dropColumn('ventas_activas');
        });
    }
};
