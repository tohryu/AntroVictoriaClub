<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CoverConfiguracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CoverAdminController extends Controller
{
    public function updatePrecio(Request $request)
    {
        $validado = $request->validate([
            'precio' => 'required|numeric|min:0|max:999999.99',
        ]);

        try {
            $config = DB::transaction(fn () => CoverConfiguracion::actualizarPrecio($validado['precio']));
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

        $mensaje = '¡Precio de cover actualizado a $'.number_format((float) $config->precio, 2).'!';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'precio' => (float) $config->precio,
            ]);
        }

        return redirect()->route('admin.mesas.index')->with('success', $mensaje);
    }
}
