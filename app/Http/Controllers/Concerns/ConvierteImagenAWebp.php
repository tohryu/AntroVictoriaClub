<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait ConvierteImagenAWebp
{
    /**
     * Convierte la imagen subida a .webp (mucho más ligero) y la guarda en
     * storage/app/public/{$carpeta}. Si el servidor no tiene soporte de
     * WebP en GD, guarda el archivo original tal cual (nunca truena la
     * subida).
     */
    private function guardarImagenComoWebp($file, string $carpeta = 'eventos'): string
    {
        if (! function_exists('imagewebp')) {
            return $file->store($carpeta, 'public');
        }

        $extension = strtolower($file->getClientOriginalExtension());

        $origen = match ($extension) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($file->getRealPath()),
            'png' => @imagecreatefrompng($file->getRealPath()),
            'webp' => @imagecreatefromwebp($file->getRealPath()),
            default => null,
        };

        if (! $origen) {
            return $file->store($carpeta, 'public');
        }

        // Preserva transparencia (importante para PNG con fondo transparente)
        imagepalettetotruecolor($origen);
        imagealphablending($origen, true);
        imagesavealpha($origen, true);

        Storage::disk('public')->makeDirectory($carpeta);

        $nombreArchivo = $carpeta.'/'.Str::random(32).'.webp';
        $rutaCompleta = Storage::disk('public')->path($nombreArchivo);

        imagewebp($origen, $rutaCompleta, 82);
        imagedestroy($origen);

        return $nombreArchivo;
    }
}
