<?php

namespace App\Http\Controllers;

use App\Models\Promocion;
use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PromocionController extends Controller
{

    public function publicIndex()
    {
        $promociones = Promocion::where('activo', true)->get();
        $eventos = Evento::where('activo', true)->orderBy('fecha', 'asc')->get();

        return view('welcome', compact('promociones', 'eventos'));
    }

    public function index()
    {
        $promociones = Promocion::latest()->get();
        $eventos = Evento::latest()->get();

        return view('admin.promociones', compact('promociones', 'eventos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'badge' => 'nullable|string|max:100',
            'descripcion' => 'nullable|string',
            'precio_etiqueta' => 'nullable|string|max:100',
        ]);

        $validated['activo'] = true;

        Promocion::create($validated);

        return redirect()->route('admin.promociones.index')->with('success', 'Promoción creada correctamente.');
    }

    public function storeEvento(Request $request)
    {
        $validated = $request->validate([
            'titulo'          => 'required|string|max:255',
            'subtitulo'       => 'nullable|string|max:255',
            'fecha'           => 'required|date',
            'descripcion'     => 'nullable|string',
            'precio_etiqueta' => 'required|string|max:100',
            'imagen'          => 'required|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        if ($request->hasFile('imagen')) {
            $validated['imagen'] = $request->file('imagen')->store('eventos', 'public');
        }

        $validated['activo'] = true;

        Evento::create($validated);

        return redirect()->route('admin.promociones.index')->with('success', 'Evento guardado correctamente.');
    }

    public function toggleStatus($id)
    {
        $promo = Promocion::findOrFail($id);
        $promo->activo = !$promo->activo;
        $promo->save();

        return redirect()->route('admin.promociones.index')->with('success', 'Estado de la promoción actualizado.');
    }

    public function toggleStatusEvento($id)
    {
        $evento = Evento::findOrFail($id);
        $evento->activo = !$evento->activo;
        $evento->save();

        return redirect()->route('admin.promociones.index')->with('success', 'Estado del evento actualizado.');
    }

    public function destroy($id)
    {
        Promocion::destroy($id);
        return redirect()->route('admin.promociones.index')->with('success', 'Promoción eliminada.');
    }

    public function destroyEvento($id)
    {
        $evento = Evento::findOrFail($id);

        if ($evento->imagen && Storage::disk('public')->exists($evento->imagen)) {
            Storage::disk('public')->delete($evento->imagen);
        }

        $evento->delete();
        return redirect()->route('admin.promociones.index')->with('success', 'Evento eliminado.');
    }
}