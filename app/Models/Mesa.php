<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Mesa extends Model
{
    use HasFactory;

    protected $table = 'mesas';

    protected $fillable = [
        'numero',
        'piso',
        'precio',
        'disponible',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'disponible' => 'boolean',
        'piso' => 'integer',
    ];

    public function reservas(): BelongsToMany
    {
        return $this->belongsToMany(Reserva::class, 'mesa_reserva')
            ->withPivot('precio_al_momento')
            ->withTimestamps();
    }

    public function reservaActiva()
    {
        return $this->reservas()
            ->whereNull('escaneada_at')
            ->where('estado', '!=', 'cancelada')
            ->first();
    }

    /**
     * ¿Esta mesa tiene una reservación real (pagada, no cancelada) para
     * una fecha específica? Se usa antes de liberar un bloqueo manual de
     * evento, para no destapar una mesa que alguien ya pagó de verdad.
     */
    public function tieneReservaRealParaFecha($fecha): bool
    {
        return $this->reservas()
            ->where('reservas.fecha', $fecha)
            ->where('reservas.estado', '!=', 'cancelada')
            ->exists();
    }
}
