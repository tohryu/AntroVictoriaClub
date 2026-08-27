<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promocion extends Model
{
    use HasFactory;

    protected $fillable = [
        'badge',
        'titulo',
        'descripcion',
        'precio_etiqueta',
        'activo',
    ];
}