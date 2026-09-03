<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiaOperacionGeneral extends Model
{
    protected $table = 'dias_operacion_general';

    protected $fillable = [
        'dia_semana',
        'activo',
    ];

    protected $casts = [
        'dia_semana' => 'integer',
        'activo' => 'boolean',
    ];

    public static $nombres = [
        0 => 'Domingo',
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado',
    ];

    public static function diasActivos(): array
    {
        return static::where('activo', true)->pluck('dia_semana')->toArray();
    }

    public static function diaPermitido($fecha): bool
    {
        $diaSemana = \Carbon\Carbon::parse($fecha)->dayOfWeek;

        return in_array($diaSemana, static::diasActivos());
    }

    public static function nombresDiasActivos(): string
    {
        $activos = static::where('activo', true)->orderBy('dia_semana')->pluck('dia_semana')->toArray();

        return collect($activos)->map(fn ($d) => static::$nombres[$d])->implode(', ');
    }
}
