<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Models\Mesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MesaAdminController extends Controller
{
    public function index(Request $request)
    {
        $mesas = Mesa::all();
        $eventosDisponibles = Evento::futurosActivos();

        $eventoParam = $request->query('evento', 'general');
        $modoGeneral = $eventoParam === 'general';
        $eventoSeleccionado = null;

        if (! $modoGeneral) {
            $eventoSeleccionado = $eventosDisponibles->firstWhere('id', (int) $eventoParam);

            if (! $eventoSeleccionado) {
                $modoGeneral = true;
            }
        }

        $mapaPrecios = $eventoSeleccionado ? $eventoSeleccionado->mapaPreciosMesa() : [];
        $mesasOcupadasIds = $eventoSeleccionado ? $eventoSeleccionado->mesasOcupadasIds() : [];

        $eventoActivo = $eventoSeleccionado;

        $diasOperacion = \App\Models\DiaOperacionGeneral::orderBy('dia_semana')->get();
        $precioCoverGeneral = \App\Models\CoverConfiguracion::precioActual();
        $entradaLibreGeneral = \App\Models\CoverConfiguracion::entradaLibreActiva();

        return view('admin.mesas.index', compact('mesas', 'eventoActivo', 'eventoSeleccionado', 'eventosDisponibles', 'mapaPrecios', 'mesasOcupadasIds', 'modoGeneral', 'diasOperacion', 'precioCoverGeneral', 'entradaLibreGeneral'));
    }

    public function updatePrecio(Request $request, $id)
    {
        $validado = $request->validate([
            'precio' => 'required|numeric|min:0|max:999999.99',
            'evento_id' => 'required',
        ]);

        $esGeneral = $validado['evento_id'] === 'general';

        if (! $esGeneral) {
            $request->validate(['evento_id' => 'integer|exists:eventos,id']);
        }

        try {
            $mesa = DB::transaction(function () use ($validado, $id, $esGeneral) {
                $mesa = Mesa::lockForUpdate()->findOrFail($id);

                if ($esGeneral) {
                    $mesa->precio = $validado['precio'];
                    $mesa->save();
                } else {
                    \App\Models\EventoMesaPrecio::updateOrCreate(
                        ['evento_id' => (int) $validado['evento_id'], 'mesa_id' => $mesa->id],
                        ['precio' => $validado['precio']]
                    );
                }

                return $mesa;
            });
        } catch (\Throwable $e) {
            Log::error('Error actualizando precio de mesa '.$id.': '.$e->getMessage(), [
                'exception' => $e,
            ]);

            $mensajeError = config('app.debug')
                ? 'Error de base de datos: '.$e->getMessage()
                : 'No se pudo guardar el precio en la base de datos. Intenta de nuevo.';

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $mensajeError], 500);
            }

            return redirect()->route('admin.mesas.index')->with('error', $mensajeError);
        }

        $mensaje = "¡Precio de {$mesa->numero} actualizado a $".number_format((float) $validado['precio'], 2).($esGeneral ? '!' : ' para este evento!');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'mesa' => [
                    'id' => $mesa->id,
                    'numero' => $mesa->numero,
                    'precio' => (float) $validado['precio'],
                ],
            ]);
        }

        return redirect()->route('admin.mesas.index')->with('success', $mensaje);
    }

    /**
     * Marca/desmarca una mesa como "reservada por fuera del sistema" (por
     * teléfono, en persona, etc.), sin necesidad de pasar por el flujo de
     * pago en la web. También es la forma de LIBERAR una mesa después de
     * un evento para que vuelva a estar disponible en la próxima fecha,
     * ya que el sistema no libera mesas automáticamente.
     */
    public function toggleDisponibilidad(Request $request, $id)
    {
        $validado = $request->validate([
            'evento_id' => 'required|integer|exists:eventos,id',
        ]);

        try {
            $resultado = DB::transaction(function () use ($id, $validado) {
                $mesa = Mesa::lockForUpdate()->findOrFail($id);
                $evento = Evento::findOrFail($validado['evento_id']);

                $bloqueo = \App\Models\MesaBloqueoEvento::where('mesa_id', $mesa->id)
                    ->where('evento_id', $evento->id)
                    ->first();

                if ($bloqueo) {
                    // Se está intentando volver a poner disponible PARA ESTE
                    // EVENTO: si hay una reservación real (pagada, no
                    // cancelada) para la fecha de este evento, no la
                    // liberamos, para evitar un doble apartado.
                    if ($mesa->tieneReservaRealParaFecha($evento->fecha)) {
                        throw new \RuntimeException('Esta mesa tiene una reservación real activa para este evento. No se puede liberar hasta que esa reservación se cancele.');
                    }

                    $bloqueo->delete();
                    $disponible = true;
                } else {
                    \App\Models\MesaBloqueoEvento::create([
                        'mesa_id' => $mesa->id,
                        'evento_id' => $evento->id,
                    ]);
                    $disponible = false;
                }

                return ['mesa' => $mesa, 'disponible' => $disponible];
            });
        } catch (\RuntimeException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }

            return redirect()->route('admin.mesas.index')->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('Error cambiando disponibilidad de mesa '.$id.' para el evento: '.$e->getMessage(), [
                'exception' => $e,
            ]);

            $mensajeError = config('app.debug')
                ? 'Error de base de datos: '.$e->getMessage()
                : 'No se pudo actualizar el estado de la mesa. Intenta de nuevo.';

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $mensajeError], 500);
            }

            return redirect()->route('admin.mesas.index')->with('error', $mensajeError);
        }

        $mesa = $resultado['mesa'];
        $disponible = $resultado['disponible'];

        $mensaje = $disponible
            ? "¡Mesa {$mesa->numero} vuelve a estar disponible para este evento!"
            : "¡Mesa {$mesa->numero} marcada como reservada (bloqueada) para este evento!";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'mesa' => [
                    'id' => $mesa->id,
                    'numero' => $mesa->numero,
                    'disponible' => $disponible,
                ],
            ]);
        }

        return redirect()->route('admin.mesas.index')->with('success', $mensaje);
    }

    /**
     * Prende/apaga el candado manual de ventas (Opción 1) para UN evento
     * específico — cada evento tiene el suyo, independiente de los demás.
     * Mientras esté apagado, nadie puede reservar mesa ni comprar cover
     * para ese evento en particular, aunque ya haya pasado su turno o ya
     * tenga precios configurados — esto le da tiempo al admin de revisar
     * todo antes de abrir la venta de cada evento.
     */
    public function toggleVentasEvento(Request $request, $id)
    {
        try {
            $evento = DB::transaction(function () use ($id) {
                $evento = Evento::lockForUpdate()->findOrFail($id);
                $evento->ventas_activas = ! $evento->ventas_activas;
                $evento->save();

                return $evento->fresh();
            });
        } catch (\Throwable $e) {
            Log::error('Error cambiando ventas_activas del evento '.$id.': '.$e->getMessage(), [
                'exception' => $e,
            ]);

            $mensajeError = config('app.debug')
                ? 'Error de base de datos: '.$e->getMessage()
                : 'No se pudo actualizar el estado de ventas. Intenta de nuevo.';

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $mensajeError], 500);
            }

            return redirect()->route('admin.mesas.index')->with('error', $mensajeError);
        }

        $mensaje = $evento->ventas_activas
            ? "¡Ventas activadas para \"{$evento->titulo}\"! Ya se pueden reservar mesas y comprar cover."
            : "Ventas pausadas para \"{$evento->titulo}\". Nadie puede reservar ni comprar cover hasta que las actives de nuevo.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'ventas_activas' => (bool) $evento->ventas_activas,
            ]);
        }

        return redirect()->route('admin.mesas.index')->with('success', $mensaje);
    }

    public function actualizarDiasOperacion(Request $request)
    {
        $validado = $request->validate([
            'dias' => 'array',
            'dias.*' => 'integer|min:0|max:6',
        ]);

        $diasActivos = $validado['dias'] ?? [];

        DB::transaction(function () use ($diasActivos) {
            foreach (range(0, 6) as $dia) {
                \App\Models\DiaOperacionGeneral::where('dia_semana', $dia)
                    ->update(['activo' => in_array($dia, $diasActivos)]);
            }
        });

        return redirect()->route('admin.mesas.index')->with('success', 'Días de operación general actualizados.');
    }
}
