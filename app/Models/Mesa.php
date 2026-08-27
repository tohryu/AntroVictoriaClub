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
}
