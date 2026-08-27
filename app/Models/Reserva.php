<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class Reserva extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'codigo_reserva',
        'qr_path',
        'qr_firma',
        'pdf_path',
        'nombre',
        'fecha',
        'mesa_id',
        'zona',
        'precio',
        'metodo_pago',
        'pago_estado',
        'pago_referencia',
        'estado',
        'escaneada_at',
        'escaneada_por',
    ];

    protected $casts = [
        'fecha' => 'date',
        'precio' => 'decimal:2',
        'escaneada_at' => 'datetime',
        'pago_referencia' => 'encrypted',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mesas(): BelongsToMany
    {
        return $this->belongsToMany(Mesa::class, 'mesa_reserva')
            ->withPivot('precio_al_momento')
            ->withTimestamps();
    }

    public function escaneadaPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'escaneada_por');
    }

    public function scopeNoEscaneadas(Builder $query): Builder
    {
        return $query->whereNull('escaneada_at');
    }

    public function estaEscaneada(): bool
    {
        return ! is_null($this->escaneada_at);
    }
}
