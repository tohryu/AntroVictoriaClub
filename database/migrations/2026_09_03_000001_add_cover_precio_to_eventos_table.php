<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            // Cada evento tiene su propio precio de cover, independiente de
            // los demás eventos (antes era un único precio global para
            // TODOS los eventos, guardado en cover_configuracion).
            $table->decimal('cover_precio', 10, 2)->default(0)->after('ventas_activas');
            $table->boolean('cover_entrada_libre')->default(false)->after('cover_precio');
        });
    }

    public function down(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->dropColumn(['cover_precio', 'cover_entrada_libre']);
        });
    }
};
