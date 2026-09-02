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
        'cover_precio',
        'cover_entrada_libre',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'ventas_activas' => 'boolean',
        'cover_precio' => 'decimal:2',
        'cover_entrada_libre' => 'boolean',
    ];

    public function preciosMesa()
    {
        return $this->hasMany(EventoMesaPrecio::class);
    }

    public function imagenes()
    {
        return $this->hasMany(EventoImagen::class)->orderBy('id');
    }

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

    /**
     * Todos los eventos futuros/de hoy que están activos, ordenados del
     * más próximo al más lejano. Cada uno puede tener sus propias ventas
     * abiertas o cerradas de forma independiente.
     */
    public static function futurosActivos()
    {
        return static::where('activo', true)
            ->where('fecha', '>=', now()->toDateString())
            ->orderBy('fecha', 'asc')
            ->get();
    }

    /**
     * Un evento se puede comprar/reservar cuando: sigue activo, su fecha
     * no ha pasado, y el admin prendió el candado de ventas para ESE
     * evento en particular (independiente de los demás eventos).
     */
    public function estaEnVenta(): bool
    {
        return $this->activo
            && $this->ventas_activas
            && $this->fecha >= now()->toDateString();
    }

    /**
     * Precio de una mesa para ESTE evento. Si el admin configuró un
     * precio específico para esa mesa en este evento, se usa ese. Si no,
     * cae en el precio global de la mesa (así el admin no tiene que
     * configurar las 65 mesas a mano para cada evento nuevo, solo las
     * que de verdad quiera que cambien de precio).
     *
     * $mapaPrecios (opcional): resultado de mapaPreciosMesa(), para
     * evitar una consulta por cada mesa al recorrer el croquis completo.
     */
    public function precioMesa(Mesa $mesa, ?array $mapaPrecios = null): float
    {
        if ($mapaPrecios !== null) {
            return array_key_exists($mesa->id, $mapaPrecios)
                ? (float) $mapaPrecios[$mesa->id]
                : (float) $mesa->precio;
        }

        $override = EventoMesaPrecio::where('evento_id', $this->id)
            ->where('mesa_id', $mesa->id)
            ->value('precio');

        return $override !== null ? (float) $override : (float) $mesa->precio;
    }

    /**
     * [mesa_id => precio] de todas las mesas que tienen un precio propio
     * configurado para este evento. Úsalo UNA vez y pásalo a precioMesa()
     * al recorrer varias mesas, para no hacer una consulta por mesa.
     */
    public function mapaPreciosMesa(): array
    {
        return EventoMesaPrecio::where('evento_id', $this->id)
            ->pluck('precio', 'mesa_id')
            ->toArray();
    }

    /**
     * IDs de todas las mesas ocupadas para ESTE evento en particular
     * (disponibilidad por evento, no global): las bloqueadas a mano por
     * el admin para este evento, más las que ya tienen una reservación
     * real (pagada, no cancelada) para la misma fecha de este evento.
     * Son solo 2 consultas fijas, sin importar cuántas mesas existan.
     */
    public function mesasOcupadasIds(): array
    {
        $bloqueadas = MesaBloqueoEvento::where('evento_id', $this->id)
            ->pluck('mesa_id')
            ->toArray();

        $reservadas = \Illuminate\Support\Facades\DB::table('mesa_reserva')
            ->join('reservas', 'reservas.id', '=', 'mesa_reserva.reserva_id')
            ->where('reservas.fecha', $this->fecha)
            ->where('reservas.estado', '!=', 'cancelada')
            ->pluck('mesa_reserva.mesa_id')
            ->toArray();

        return array_values(array_unique(array_merge($bloqueadas, $reservadas)));
    }
}