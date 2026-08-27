<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_reserva')->unique();
            $table->string('nombre');
            $table->date('fecha');
            $table->string('mesa_id');
            $table->string('zona')->nullable();
            $table->decimal('precio', 10, 2)->default(0);
            $table->string('metodo_pago')->nullable();
            $table->string('estado')->default('confirmada');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};