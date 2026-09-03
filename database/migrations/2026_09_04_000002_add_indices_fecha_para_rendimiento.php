<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->index(['fecha', 'estado']);
        });

        Schema::table('boletos_cover', function (Blueprint $table) {
            $table->index('fecha');
        });

        Schema::table('eventos', function (Blueprint $table) {
            $table->index('fecha');
        });
    }

    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->dropIndex(['fecha', 'estado']);
        });

        Schema::table('boletos_cover', function (Blueprint $table) {
            $table->dropIndex(['fecha']);
        });

        Schema::table('eventos', function (Blueprint $table) {
            $table->dropIndex(['fecha']);
        });
    }
};
