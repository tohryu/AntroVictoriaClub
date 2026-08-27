<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->string('qr_path')->nullable()->after('codigo_reserva');
            $table->string('qr_firma')->nullable()->after('qr_path');
            $table->string('pdf_path')->nullable()->after('qr_firma');
            $table->string('pago_estado')->default('pendiente')->after('metodo_pago');
            $table->text('pago_referencia')->nullable()->after('pago_estado');
            $table->timestamp('escaneada_at')->nullable()->after('estado');
            $table->foreignId('escaneada_por')->nullable()->after('escaneada_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('escaneada_por');
            $table->dropColumn(['qr_path', 'qr_firma', 'pdf_path', 'pago_estado', 'pago_referencia', 'escaneada_at']);
        });
    }
};
