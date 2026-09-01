<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoverConfiguracion extends Model
{
    protected $table = 'cover_configuracion';

    protected $fillable = [
        'precio',
        'entrada_libre',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'entrada_libre' => 'boolean',
    ];

    /**
     * El precio del cover vive en una sola fila (singleton). Se ordena
     * explícitamente por id para que, aunque llegara a existir más de
     * una fila por error, siempre se lea/actualice la misma de forma
     * determinística: la que realmente se va a cobrar.
     */
    public static function precioActual(): float
    {
        return (float) (static::query()->orderBy('id')->value('precio') ?? 0);
    }

    public static function entradaLibreActiva(): bool
    {
        return (bool) (static::query()->orderBy('id')->value('entrada_libre') ?? false);
    }

    public static function precioConfigurado(): bool
    {
        return static::entradaLibreActiva() || static::precioActual() > 0;
    }

    public static function actualizarPrecio(float $precio): self
    {
        $config = static::query()->orderBy('id')->lockForUpdate()->first();

        if (! $config) {
            $config = new self();
        }

        $config->precio = $precio;
        // Guardar un precio específico siempre desactiva "Entrada Libre".
        $config->entrada_libre = false;
        $config->save();

        return $config;
    }

    public static function activarEntradaLibre(): self
    {
        $config = static::query()->orderBy('id')->lockForUpdate()->first();

        if (! $config) {
            $config = new self();
        }

        $config->entrada_libre = true;
        $config->save();

        return $config;
    }
}
