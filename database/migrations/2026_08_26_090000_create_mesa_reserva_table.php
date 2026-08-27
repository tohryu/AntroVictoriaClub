<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mesa_reserva', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reserva_id')->constrained('reservas')->cascadeOnDelete();
            $table->foreignId('mesa_id')->constrained('mesas')->cascadeOnDelete();
            $table->decimal('precio_al_momento', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['reserva_id', 'mesa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mesa_reserva');
    }
};
