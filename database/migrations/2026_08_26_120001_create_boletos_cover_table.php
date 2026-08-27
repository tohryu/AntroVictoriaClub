<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boletos_cover', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('codigo_boleto')->unique();
            $table->string('qr_path')->nullable();
            $table->string('qr_firma')->nullable();
            $table->string('pdf_path')->nullable();
            $table->string('nombre');
            $table->date('fecha');
            $table->unsignedInteger('cantidad')->default(1);
            $table->decimal('precio_unitario', 10, 2)->default(0);
            $table->decimal('precio_total', 10, 2)->default(0);
            $table->string('metodo_pago')->nullable();
            $table->string('pago_estado')->default('pendiente');
            $table->text('pago_referencia')->nullable();
            $table->string('estado')->default('confirmado');
            $table->timestamp('escaneada_at')->nullable();
            $table->foreignId('escaneada_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boletos_cover');
    }
};
