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
    public function index()
    {
        $mesas = Mesa::all();
        $eventoActivo = Evento::proximoEventoActivo();

        return view('admin.mesas.index', compact('mesas', 'eventoActivo'));
    }

    public function updatePrecio(Request $request, $id)
    {
        $validado = $request->validate([
            'precio' => 'required|numeric|min:0|max:999999.99',
        ]);

        try {
            $mesa = DB::transaction(function () use ($validado, $id) {
                $mesa = Mesa::lockForUpdate()->findOrFail($id);
                $mesa->precio = $validado['precio'];
                $mesa->save();

                return $mesa->fresh();
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

        $mensaje = "¡Precio de {$mesa->numero} actualizado a $".number_format((float) $mesa->precio, 2).'!';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'mesa' => [
                    'id' => $mesa->id,
                    'numero' => $mesa->numero,
                    'precio' => (float) $mesa->precio,
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
        try {
            $mesa = DB::transaction(function () use ($id) {
                $mesa = Mesa::lockForUpdate()->findOrFail($id);

                if (! $mesa->disponible) {
                    // Se está intentando volver a poner disponible: si hay una
                    // reservación real (pagada, no cancelada, no escaneada) sobre
                    // esta mesa, no la liberamos para evitar un doble apartado.
                    if ($mesa->reservaActiva()) {
                        throw new \RuntimeException('Esta mesa tiene una reservación activa en el sistema. No se puede liberar hasta que esa reservación se cancele o se complete el evento.');
                    }
                }

                $mesa->disponible = ! $mesa->disponible;
                $mesa->save();

                return $mesa->fresh();
            });
        } catch (\RuntimeException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }

            return redirect()->route('admin.mesas.index')->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('Error cambiando disponibilidad de mesa '.$id.': '.$e->getMessage(), [
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

        $mensaje = $mesa->disponible
            ? "¡Mesa {$mesa->numero} vuelve a estar disponible!"
            : "¡Mesa {$mesa->numero} marcada como reservada (bloqueada en la web)!";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'mesa' => [
                    'id' => $mesa->id,
                    'numero' => $mesa->numero,
                    'disponible' => (bool) $mesa->disponible,
                ],
            ]);
        }

        return redirect()->route('admin.mesas.index')->with('success', $mensaje);
    }

    /**
     * Prende/apaga el candado manual de ventas (Opción 1) para el evento
     * que esté activo en este momento (el más próximo). Mientras esté
     * apagado, nadie puede reservar mesa ni comprar cover para ese evento,
     * aunque ya se vea como "el próximo" en la página principal — esto es
     * justo para darle tiempo al admin de actualizar los precios antes de
     * abrir la venta.
     */
    public function toggleVentasEvento(Request $request)
    {
        try {
            $evento = DB::transaction(function () {
                $evento = Evento::proximoEventoActivo();

                if (! $evento) {
                    throw new \RuntimeException('No hay ningún evento próximo configurado para activar/desactivar ventas.');
                }

                $evento = Evento::lockForUpdate()->findOrFail($evento->id);
                $evento->ventas_activas = ! $evento->ventas_activas;
                $evento->save();

                return $evento->fresh();
            });
        } catch (\RuntimeException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }

            return redirect()->route('admin.mesas.index')->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('Error cambiando ventas_activas del evento: '.$e->getMessage(), [
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
}
