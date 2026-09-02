<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ConvierteImagenAWebp;
use App\Models\Evento;
use App\Models\EventoImagen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventoGaleriaController extends Controller
{
    use ConvierteImagenAWebp;

    /**
     * Vista pública de la galería de un evento. Si quien la ve es admin,
     * la misma vista también le muestra el formulario para subir/borrar
     * imágenes (nadie más puede subir imágenes, solo el administrador).
     */
    public function mostrar(Request $request, $id)
    {
        $evento = Evento::with('imagenes')->findOrFail($id);
        $esAdmin = $request->user() && $request->user()->es_admin;

        return view('evento-galeria', compact('evento', 'esAdmin'));
    }

    public function subir(Request $request, $id)
    {
        $evento = Evento::findOrFail($id);

        $validated = $request->validate([
            'imagenes' => 'required|array|min:1',
            'imagenes.*' => 'image|max:8192',
        ]);

        foreach ($request->file('imagenes') as $archivo) {
            $ruta = $this->guardarImagenComoWebp($archivo, 'eventos-galeria');

            EventoImagen::create([
                'evento_id' => $evento->id,
                'ruta' => $ruta,
            ]);
        }

        return redirect()->route('evento.galeria', $evento->id)->with('success', 'Imágenes subidas correctamente.');
    }

    public function eliminar(Request $request, $id, $imagenId)
    {
        $imagen = EventoImagen::where('evento_id', $id)->findOrFail($imagenId);

        if (Storage::disk('public')->exists($imagen->ruta)) {
            Storage::disk('public')->delete($imagen->ruta);
        }

        $imagen->delete();

        return redirect()->route('evento.galeria', $id)->with('success', 'Imagen eliminada.');
    }
}
