<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CoverConfiguracion;
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
            'evento_id' => 'required|string',
        ]);

        $esGeneral = $validado['evento_id'] === 'general';

        if (! $esGeneral) {
            $request->validate(['evento_id' => 'integer|exists:eventos,id']);
        }

        try {
            $resultado = DB::transaction(function () use ($validado, $esGeneral) {
                if ($esGeneral) {
                    CoverConfiguracion::actualizarPrecio($validado['precio']);

                    return ['titulo' => null, 'precio' => $validado['precio']];
                }

                $evento = Evento::lockForUpdate()->findOrFail($validado['evento_id']);
                $evento->cover_precio = $validado['precio'];
                $evento->cover_entrada_libre = false;
                $evento->save();

                return ['titulo' => $evento->titulo, 'precio' => (float) $evento->cover_precio];
            });
        } catch (\Throwable $e) {
            Log::error('Error actualizando precio de cover: '.$e->getMessage(), [
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

        $mensaje = $resultado['titulo']
            ? '¡Precio de cover de "'.$resultado['titulo'].'" actualizado a $'.number_format($resultado['precio'], 2).'!'
            : '¡Precio de cover general actualizado a $'.number_format($resultado['precio'], 2).'!';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'precio' => $resultado['precio'],
            ]);
        }

        return redirect()->route('admin.mesas.index')->with('success', $mensaje);
    }

    public function activarEntradaLibre(Request $request)
    {
        $validado = $request->validate([
            'evento_id' => 'required|string',
        ]);

        $esGeneral = $validado['evento_id'] === 'general';

        if (! $esGeneral) {
            $request->validate(['evento_id' => 'integer|exists:eventos,id']);
        }

        try {
            $resultado = DB::transaction(function () use ($validado, $esGeneral) {
                if ($esGeneral) {
                    CoverConfiguracion::activarEntradaLibre();

                    return null;
                }

                $evento = Evento::lockForUpdate()->findOrFail($validado['evento_id']);
                $evento->cover_entrada_libre = true;
                $evento->save();

                return $evento->titulo;
            });
        } catch (\Throwable $e) {
            Log::error('Error activando Entrada Libre: '.$e->getMessage(), [
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

        $mensaje = $resultado
            ? '¡Cover de "'.$resultado.'" puesto en Entrada Libre!'
            : '¡Cover general puesto en Entrada Libre!';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $mensaje,
            ]);
        }

        return redirect()->route('admin.mesas.index')->with('success', $mensaje);
    }
}
