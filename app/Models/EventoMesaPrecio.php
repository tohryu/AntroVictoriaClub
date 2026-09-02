<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventoMesaPrecio extends Model
{
    protected $table = 'evento_mesa_precios';

    protected $fillable = [
        'evento_id',
        'mesa_id',
        'precio',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
    ];

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }

    public function mesa()
    {
        return $this->belongsTo(Mesa::class);
    }
}
