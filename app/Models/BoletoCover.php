<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BoletoCover extends Model
{
    use HasFactory;

    protected $table = 'boletos_cover';

    protected $fillable = [
        'user_id',
        'codigo_boleto',
        'qr_path',
        'qr_firma',
        'pdf_path',
        'nombre',
        'fecha',
        'cantidad',
        'precio_unitario',
        'precio_total',
        'metodo_pago',
        'pago_estado',
        'pago_referencia',
        'estado',
        'escaneada_at',
        'escaneada_por',
    ];

    protected $casts = [
        'fecha' => 'date',
        'precio_unitario' => 'decimal:2',
        'precio_total' => 'decimal:2',
        'cantidad' => 'integer',
        'escaneada_at' => 'datetime',
        'pago_referencia' => 'encrypted',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
