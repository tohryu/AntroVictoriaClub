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

        $hoy = now()->toDateString();

        $eventos = Evento::where('activo', true)
            ->orderByRaw('CASE WHEN fecha >= ? THEN 0 ELSE 1 END ASC', [$hoy])
            ->orderByRaw('CASE WHEN fecha >= ? THEN fecha END ASC', [$hoy])
            ->orderByRaw('CASE WHEN fecha < ? THEN fecha END DESC', [$hoy])
            ->get();

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
            $validated['imagen'] = $this->guardarImagenComoWebp($request->file('imagen'));
        }

        $validated['activo'] = true;

        Evento::create($validated);

        return redirect()->route('admin.promociones.index')->with('success', 'Evento guardado correctamente.');
    }

    /**
     * Convierte la imagen subida a .webp (mucho más ligero) y la guarda en
     * storage/app/public/eventos. Si el servidor no tiene soporte de WebP
     * en GD, guarda el archivo original tal cual (nunca truena la subida).
     */
    private function guardarImagenComoWebp($file): string
    {
        if (! function_exists('imagewebp')) {
            return $file->store('eventos', 'public');
        }

        $extension = strtolower($file->getClientOriginalExtension());

        $origen = match ($extension) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($file->getRealPath()),
            'png' => @imagecreatefrompng($file->getRealPath()),
            'webp' => @imagecreatefromwebp($file->getRealPath()),
            default => null,
        };

        if (! $origen) {
            return $file->store('eventos', 'public');
        }

        // Preserva transparencia (importante para PNG con fondo transparente)
        imagepalettetotruecolor($origen);
        imagealphablending($origen, true);
        imagesavealpha($origen, true);

        Storage::disk('public')->makeDirectory('eventos');

        $nombreArchivo = 'eventos/'.Str::random(32).'.webp';
        $rutaCompleta = Storage::disk('public')->path($nombreArchivo);

        imagewebp($origen, $rutaCompleta, 82);
        imagedestroy($origen);

        return $nombreArchivo;
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