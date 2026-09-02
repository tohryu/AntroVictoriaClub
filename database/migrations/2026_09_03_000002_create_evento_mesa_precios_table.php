<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evento_mesa_precios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_id')->constrained('eventos')->cascadeOnDelete();
            $table->foreignId('mesa_id')->constrained('mesas')->cascadeOnDelete();
            $table->decimal('precio', 10, 2);
            $table->timestamps();

            // Una mesa solo puede tener UN precio configurado por evento.
            $table->unique(['evento_id', 'mesa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evento_mesa_precios');
    }
};
