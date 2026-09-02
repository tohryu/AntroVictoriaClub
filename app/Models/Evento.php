<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'subtitulo',
        'fecha',
        'descripcion',
        'precio_etiqueta',
        'imagen',
        'activo',
        'ventas_activas',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'ventas_activas' => 'boolean',
    ];

    /**
     * El evento "actual" para efectos de reservar mesa / comprar cover:
     * el más próximo entre los futuros/de hoy (activos). Es el mismo
     * evento que aparece primero en "Próximos Eventos" en la página
     * principal. Si no hay ninguno, regresa null (nada que vender).
     */
    public static function proximoEventoActivo(): ?self
    {
        $hoy = now()->toDateString();

        return static::where('activo', true)
            ->where('fecha', '>=', $hoy)
            ->orderBy('fecha', 'asc')
            ->first();
    }
}