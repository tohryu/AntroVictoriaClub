<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mesa_bloqueos_evento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mesa_id')->constrained('mesas')->cascadeOnDelete();
            $table->foreignId('evento_id')->constrained('eventos')->cascadeOnDelete();
            $table->timestamps();

            // Una mesa solo puede estar bloqueada UNA vez por evento.
            $table->unique(['mesa_id', 'evento_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mesa_bloqueos_evento');
    }
};
