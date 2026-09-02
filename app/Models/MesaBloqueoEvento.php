<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MesaBloqueoEvento extends Model
{
    protected $table = 'mesa_bloqueos_evento';

    protected $fillable = [
        'mesa_id',
        'evento_id',
    ];

    public function mesa()
    {
        return $this->belongsTo(Mesa::class);
    }

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }
}
