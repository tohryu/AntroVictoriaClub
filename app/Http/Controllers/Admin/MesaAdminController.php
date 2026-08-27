<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MesaAdminController extends Controller
{
    public function index()
    {
        $mesas = Mesa::all();

        return view('admin.mesas.index', compact('mesas'));
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
            Log::error('Error actualizando precio de mesa '.$id.': '.$e->getMessage());

            $mensajeError = 'No se pudo guardar el precio en la base de datos. Intenta de nuevo.';

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
}
