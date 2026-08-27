<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Promociones y Eventos</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-black text-zinc-100 p-4 sm:p-6 md:p-10 font-sans antialiased">
    <div class="max-w-5xl mx-auto space-y-16">

        @if(session('success'))
            <div class="bg-green-500/10 border border-green-500/30 text-green-400 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2 shadow-sm">
                <span>✓</span>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        <section class="space-y-6">
            <div class="border-b border-zinc-800 pb-3">
                <h1 class="text-2xl sm:text-3xl font-black text-amber-500 tracking-tight">Administrar Promociones</h1>
                <p class="text-xs text-zinc-400 mt-1">Gestiona las ofertas especiales y promociones vigentes.</p>
            </div>

            <form action="{{ route('admin.promociones.store') }}" method="POST" class="bg-zinc-900/90 p-5 rounded-xl border border-zinc-800/80 space-y-4 backdrop-blur-sm shadow-xl">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1.5">Título</label>
                        <input type="text" name="titulo" required class="w-full bg-zinc-950/80 border border-zinc-700/70 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/50 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1.5">Dias</label>
                        <input type="text" name="badge" class="w-full bg-zinc-950/80 border border-zinc-700/70 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/50 transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1.5">Descripción</label>
                    <textarea name="descripcion" rows="2" class="w-full bg-zinc-950/80 border border-zinc-700/70 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/50 transition-all resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1.5">Etiqueta de Precio</label>
                    <input type="text" name="precio_etiqueta" class="w-full bg-zinc-950/80 border border-zinc-700/70 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/50 transition-all">
                </div>
                <div class="pt-1">
                    <button type="submit" class="bg-amber-500 text-black font-bold text-sm px-5 py-2 rounded-lg hover:bg-amber-400 transition-all shadow-md shadow-amber-500/10 active:scale-95">
                        Guardar Promoción
                    </button>
                </div>
            </form>

            <div class="pt-2 space-y-3">
                <h2 class="text-xs uppercase tracking-widest font-bold text-zinc-400 mb-3">Promociones Existentes</h2>
                <div class="space-y-2.5">
                    @forelse($promociones as $promo)
                        <div class="bg-zinc-900/70 hover:bg-zinc-900 p-3.5 sm:p-4 rounded-xl flex items-center justify-between border border-zinc-800/80 transition-all shadow-sm gap-4">
                            <div class="space-y-1">
                                <span class="inline-block text-[10px] uppercase tracking-wider bg-amber-500/15 text-amber-400 border border-amber-500/30 px-2 py-0.5 rounded-full font-bold">{{ $promo->badge ?? 'Sin badge' }}</span>
                                <h3 class="text-base font-bold text-white leading-tight">{{ $promo->titulo }}</h3>
                                @if($promo->descripcion)
                                    <p class="text-xs text-zinc-400 line-clamp-2 leading-relaxed">{{ $promo->descripcion }}</p>
                                @endif
                                <div class="pt-0.5">
                                    <span class="text-xs text-amber-300 font-mono font-semibold">{{ $promo->precio_etiqueta }}</span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2 flex-shrink-0">
                                <form action="{{ route('admin.promociones.toggle', $promo->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="px-2.5 py-1 rounded-md text-xs font-semibold transition-all {{ $promo->activo ? 'bg-green-500/20 text-green-400 border border-green-500/30 hover:bg-green-500/30' : 'bg-zinc-800 text-zinc-400 border border-zinc-700 hover:bg-zinc-700' }}">
                                        {{ $promo->activo ? 'Activa' : 'Oculta' }}
                                    </button>
                                </form>
                                <form action="{{ route('admin.promociones.destroy', $promo->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="return confirm('¿Eliminar esta promoción?')" class="text-red-400 hover:text-red-300 text-xs font-semibold px-2 py-1 hover:underline transition">Eliminar</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-zinc-500 text-xs italic bg-zinc-900/30 p-4 rounded-xl border border-zinc-800/40 text-center">No hay promociones registradas.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <div class="relative py-2">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-zinc-800/80"></div>
            </div>
            <div class="relative flex justify-center">
                <span class="bg-black px-4 text-xs font-bold uppercase tracking-widest text-zinc-400 border border-zinc-800/80 rounded-full py-0.5">Eventos</span>
            </div>
        </div>

        <section class="space-y-6">
            <div class="border-b border-zinc-800 pb-3">
                <h2 class="text-2xl sm:text-3xl font-black text-amber-500 tracking-tight">Administrar Próximos Eventos</h2>
                <p class="text-xs text-zinc-400 mt-1">Programa carteleras, fechas especiales y eventos.</p>
            </div>

            <form action="{{ route('admin.promociones.eventos.store') }}" method="POST" enctype="multipart/form-data" class="bg-zinc-900/90 p-5 rounded-xl border border-zinc-800/80 space-y-4 backdrop-blur-sm shadow-xl">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1.5">Título del Evento *</label>
                        <input type="text" name="titulo" required class="w-full bg-zinc-950/80 border border-zinc-700/70 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/50 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1.5">Subtítulo / DJ invitado</label>
                        <input type="text" name="subtitulo" class="w-full bg-zinc-950/80 border border-zinc-700/70 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/50 transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1.5">Fecha *</label>
                        <input type="date" name="fecha" required class="w-full bg-zinc-950/80 border border-zinc-700/70 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/50 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1.5">Etiqueta de Precio</label>
                        <input type="text" name="precio_etiqueta" required class="w-full bg-zinc-950/80 border border-zinc-700/70 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/50 transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1.5">Descripción</label>
                    <textarea name="descripcion" rows="2" class="w-full bg-zinc-950/80 border border-zinc-700/70 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/50 transition-all resize-none"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1.5">Imagen del Evento (Archivo)</label>
                    <input type="file" name="imagen" accept="image/*" required class="w-full bg-zinc-950/80 border border-zinc-700/70 rounded-lg text-xs text-zinc-300 focus:outline-none focus:border-amber-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-amber-500 file:text-black hover:file:bg-amber-400 cursor-pointer">
                </div>

                <div class="pt-1">
                    <button type="submit" class="bg-amber-500 text-black font-bold text-sm px-5 py-2 rounded-lg hover:bg-amber-400 transition-all shadow-md shadow-amber-500/10 active:scale-95">
                        Guardar Evento
                    </button>
                </div>
            </form>

            <div class="pt-2 space-y-3">
                <h2 class="text-xs uppercase tracking-widest font-bold text-zinc-400 mb-3">Eventos Programados</h2>
                <div class="space-y-2.5">
                    @forelse($eventos as $evento)
                        <div class="bg-zinc-900/70 hover:bg-zinc-900 p-3.5 sm:p-4 rounded-xl flex items-center justify-between border border-zinc-800/80 transition-all shadow-sm gap-4">
                            <div class="flex items-center space-x-3.5 min-w-0">
                                @if($evento->imagen)
                                    <img src="{{ Storage::url($evento->imagen) }}" alt="{{ $evento->titulo }}" class="w-12 h-12 sm:w-14 sm:h-14 object-cover rounded-lg border border-zinc-700/80 flex-shrink-0 shadow-sm">
                                @endif
                                <div class="min-w-0 space-y-0.5">
                                    <span class="inline-block text-[10px] uppercase tracking-wider bg-amber-500/15 text-amber-400 border border-amber-500/30 px-2 py-0.5 rounded-full font-bold">{{ $evento->fecha }}</span>
                                    <h3 class="text-base font-bold text-white leading-tight truncate">
                                        {{ $evento->titulo }}
                                        @if($evento->subtitulo)
                                            <span class="text-xs font-normal text-zinc-400">({{ $evento->subtitulo }})</span>
                                        @endif
                                    </h3>
                                    @if($evento->descripcion)
                                        <p class="text-xs text-zinc-400 truncate leading-relaxed">{{ $evento->descripcion }}</p>
                                    @endif
                                    <div>
                                        <span class="text-xs text-amber-300 font-mono font-semibold">{{ $evento->precio_etiqueta }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center space-x-2 flex-shrink-0">
                                <form action="{{ route('admin.promociones.eventos.toggle', $evento->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="px-2.5 py-1 rounded-md text-xs font-semibold transition-all {{ $evento->activo ? 'bg-green-500/20 text-green-400 border border-green-500/30 hover:bg-green-500/30' : 'bg-zinc-800 text-zinc-400 border border-zinc-700 hover:bg-zinc-700' }}">
                                        {{ $evento->activo ? 'Activo' : 'Oculto' }}
                                    </button>
                                </form>
                                <form action="{{ route('admin.promociones.eventos.destroy', $evento->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="return confirm('¿Eliminar este evento?')" class="text-red-400 hover:text-red-300 text-xs font-semibold px-2 py-1 hover:underline transition">Eliminar</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-zinc-500 text-xs italic bg-zinc-900/30 p-4 rounded-xl border border-zinc-800/40 text-center">No hay eventos programados.</p>
                    @endforelse
                </div>
            </div>
        </section>

    </div>
</body>
</html>