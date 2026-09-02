<!DOCTYPE html>
<html lang="es" class="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Galería — {{ $evento->titulo }} · Victoria Luxury Club</title>
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white antialiased min-h-screen">

  <div class="max-w-5xl mx-auto px-4 sm:px-6 py-10">

    <a href="{{ route('home') }}#eventos" class="text-xs text-zinc-500 hover:text-amber-400 transition-colors">&larr; Volver a Próximos Eventos</a>

    <div class="mt-4 mb-8">
      <span class="text-xs font-semibold text-amber-400 uppercase tracking-wider">
        {{ \Carbon\Carbon::parse($evento->fecha)->locale('es')->isoFormat('dddd D [de] MMMM, YYYY') }}
      </span>
      <h1 class="text-3xl sm:text-4xl font-black text-white mt-1">{{ $evento->titulo }}</h1>
      @if($evento->subtitulo)
        <p class="text-zinc-400 mt-1">{{ $evento->subtitulo }}</p>
      @endif
    </div>

    @if(session('success'))
      <div class="mb-6 p-4 bg-emerald-950/80 border border-emerald-500 text-emerald-300 text-sm rounded-xl">
        {{ session('success') }}
      </div>
    @endif

    @if($esAdmin)
      <div class="mb-10 p-5 bg-zinc-900/70 border border-zinc-800 rounded-2xl">
        <h2 class="text-sm font-bold text-amber-400 uppercase tracking-wide mb-3">Subir imágenes (solo administrador)</h2>
        <form action="{{ route('admin.promociones.eventos.galeria.subir', $evento->id) }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-3">
          @csrf
          <input type="file" name="imagenes[]" accept="image/*" multiple required class="flex-1 text-sm text-zinc-300 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-amber-500 file:text-black file:font-bold file:cursor-pointer bg-zinc-950 border border-zinc-800 rounded-lg px-3 py-2">
          <button type="submit" class="bg-amber-500 hover:bg-amber-400 text-black font-bold text-sm px-5 py-2.5 rounded-lg transition-colors whitespace-nowrap">
            Subir a la galería
          </button>
        </form>
        <p class="text-[11px] text-zinc-600 mt-2">Puedes seleccionar varias imágenes a la vez. Se convierten automáticamente a WebP.</p>
      </div>
    @endif

    @if($evento->imagenes->isEmpty())
      <div class="text-center py-16">
        <p class="text-zinc-500">Todavía no hay imágenes en la galería de este evento.</p>
      </div>
    @else
      <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
        @foreach($evento->imagenes as $imagen)
          <div class="relative group aspect-square rounded-xl overflow-hidden bg-zinc-900 border border-zinc-800">
            <img src="{{ Storage::url($imagen->ruta) }}" alt="{{ $evento->titulo }}" loading="lazy" decoding="async" class="w-full h-full object-cover">

            @if($esAdmin)
              <form action="{{ route('admin.promociones.eventos.galeria.eliminar', [$evento->id, $imagen->id]) }}" method="POST" onsubmit="return confirm('¿Eliminar esta imagen?');" class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-500 text-white text-xs font-bold w-7 h-7 rounded-full flex items-center justify-center shadow-lg">
                  ✕
                </button>
              </form>
            @endif
          </div>
        @endforeach
      </div>
    @endif

  </div>

</body>
</html>
