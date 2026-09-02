<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CoverAdminController extends Controller
{
    public function updatePrecio(Request $request)
    {
        $validado = $request->validate([
            'precio' => 'required|numeric|min:0|max:999999.99',
            'evento_id' => 'required|integer|exists:eventos,id',
        ]);

        try {
            $evento = DB::transaction(function () use ($validado) {
                $evento = Evento::lockForUpdate()->findOrFail($validado['evento_id']);
                $evento->cover_precio = $validado['precio'];
                // Guardar un precio específico siempre desactiva Entrada Libre.
                $evento->cover_entrada_libre = false;
                $evento->save();

                return $evento->fresh();
            });
        } catch (\Throwable $e) {
            Log::error('Error actualizando precio de cover del evento: '.$e->getMessage(), [
                'exception' => $e,
            ]);

            $mensajeError = config('app.debug')
                ? 'Error de base de datos: '.$e->getMessage()
                : 'No se pudo guardar el precio del cover. Intenta de nuevo.';

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $mensajeError], 500);
            }

            return redirect()->route('admin.mesas.index')->with('error', $mensajeError);
        }

        $mensaje = '¡Precio de cover de "'.$evento->titulo.'" actualizado a $'.number_format((float) $evento->cover_precio, 2).'!';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'precio' => (float) $evento->cover_precio,
            ]);
        }

        return redirect()->route('admin.mesas.index')->with('success', $mensaje);
    }

    public function activarEntradaLibre(Request $request)
    {
        $validado = $request->validate([
            'evento_id' => 'required|integer|exists:eventos,id',
        ]);

        try {
            $evento = DB::transaction(function () use ($validado) {
                $evento = Evento::lockForUpdate()->findOrFail($validado['evento_id']);
                $evento->cover_entrada_libre = true;
                $evento->save();

                return $evento->fresh();
            });
        } catch (\Throwable $e) {
            Log::error('Error activando Entrada Libre para el evento: '.$e->getMessage(), [
                'exception' => $e,
            ]);

            $mensajeError = config('app.debug')
                ? 'Error de base de datos: '.$e->getMessage()
                : 'No se pudo activar Entrada Libre. Intenta de nuevo.';

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $mensajeError], 500);
            }

            return redirect()->route('admin.mesas.index')->with('error', $mensajeError);
        }

        $mensaje = '¡Cover de "'.$evento->titulo.'" puesto en Entrada Libre! Ya no se cobrará para este evento.';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'entrada_libre' => (bool) $evento->cover_entrada_libre,
            ]);
        }

        return redirect()->route('admin.mesas.index')->with('success', $mensaje);
    }
}
