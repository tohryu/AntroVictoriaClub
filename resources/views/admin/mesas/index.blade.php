@php
    $mesas = $mesas ?? \App\Models\Mesa::all();
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Modificar Precios - Control de Mesas Espacial</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>

        #starfield {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background-color: #05050b;
        }
    </style>
</head>
<body class="text-gray-100 min-h-screen p-6 relative">

    <canvas id="starfield"></canvas>

    <div id="toast-notificacion" class="fixed top-4 right-4 left-4 sm:left-auto sm:top-6 sm:right-6 z-50 max-w-sm sm:w-full mx-auto sm:mx-0 translate-x-[120%] opacity-0 transition-all duration-300 ease-out">
        <div id="toast-contenido" class="p-4 rounded-xl shadow-2xl border backdrop-blur-md flex items-start gap-3 bg-emerald-950/90 border-emerald-500 text-emerald-300">
            <span id="toast-icono" class="text-lg leading-none">✓</span>
            <p id="toast-mensaje" class="text-sm font-medium leading-snug"></p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto relative z-10">

        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between border-b border-gray-800 pb-4">
            <div>
                <h1 class="text-3xl font-extrabold text-white tracking-wide drop-shadow-md">Panel de Control de Tarifas</h1>
                <p class="text-gray-400 text-sm mt-1">Selecciona una mesa para cambiar su precio en el sistema.</p>
            </div>

            <div class="flex gap-2 mt-4 md:mt-0 bg-gray-900/80 p-1.5 rounded-xl border border-gray-700/60 backdrop-blur-md">
                <button type="button"
                        id="btn-planta-baja"
                        onclick="cambiarPlanta('baja')"
                        class="px-5 py-2 rounded-lg font-bold text-sm transition shadow-lg bg-blue-600 text-white cursor-pointer">
                    Planta Baja
                </button>
                <button type="button"
                        id="btn-segunda-planta"
                        onclick="cambiarPlanta('alta')"
                        class="px-5 py-2 rounded-lg font-bold text-sm transition text-gray-400 hover:text-white hover:bg-gray-800/60 cursor-pointer">
                    Segunda Planta
                </button>
                <button type="button"
                        id="btn-cover"
                        onclick="cambiarPlanta('cover')"
                        class="px-5 py-2 rounded-lg font-bold text-sm transition text-gray-400 hover:text-white hover:bg-gray-800/60 cursor-pointer">
                    Cover
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-950/80 border-l-4 border-emerald-500 text-emerald-300 rounded-lg shadow-lg backdrop-blur-md">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 bg-gray-900/70 p-6 rounded-2xl shadow-2xl border border-gray-800 backdrop-blur-md">

                <div id="vista-planta-baja" class="space-y-8">

                    <div>
                        <h2 class="text-lg font-bold text-blue-400 mb-3 pb-1 border-b border-gray-800 flex items-center gap-2">
                            <span>Zona L (L1 - L16)</span>
                        </h2>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-gray-950/60 p-4 rounded-xl border border-gray-800/80">
                            @foreach(range(1, 16) as $i)
                                @php
                                    $codigo = 'L' . $i;
                                    $mesa = $mesas->firstWhere('numero', $codigo);
                                    $precio = $mesa ? $mesa->precio : 0.00;
                                    $id = $mesa ? $mesa->id : '';
                                @endphp
                                <button type="button"
                                        onclick="seleccionarMesaParaEditar('{{ $id }}', '{{ $codigo }}', {{ $precio }})"
                                        class="p-3 border border-gray-800 bg-gray-900/80 hover:bg-blue-950/50 hover:border-blue-500/80 rounded-xl font-bold text-center transition shadow-md group focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                    <span class="block text-gray-200 group-hover:text-blue-400 text-base">{{ $codigo }}</span>
                                    <span id="precio-{{ $codigo }}" class="block text-xs text-gray-400 font-normal mt-1">${{ number_format($precio, 2) }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <h2 class="text-lg font-bold text-blue-400 mb-3 pb-1 border-b border-gray-800 flex items-center gap-2">
                            <span>Zona R - Mesas Cuadradas (R1 - R13)</span>
                        </h2>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-gray-950/60 p-4 rounded-xl border border-gray-800/80">
                            @foreach(range(1, 13) as $i)
                                @php
                                    $codigo = 'R' . $i;
                                    $mesa = $mesas->firstWhere('numero', $codigo);
                                    $precio = $mesa ? $mesa->precio : 0.00;
                                    $id = $mesa ? $mesa->id : '';
                                @endphp
                                <button type="button"
                                        onclick="seleccionarMesaParaEditar('{{ $id }}', '{{ $codigo }}', {{ $precio }})"
                                        class="p-3 border border-gray-800 bg-gray-900/80 hover:bg-blue-950/50 hover:border-blue-500/80 rounded-xl font-bold text-center transition shadow-md group focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                    <span class="block text-gray-200 group-hover:text-blue-400 text-base">{{ $codigo }}</span>
                                    <span id="precio-{{ $codigo }}" class="block text-xs text-gray-400 font-normal mt-1">${{ number_format($precio, 2) }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <h2 class="text-lg font-bold text-amber-400 mb-3 pb-1 border-b border-gray-800 flex items-center gap-2">
                            <span>Mesas Redondas - Izquierda de la Pista (BL1 - BL6)</span>
                        </h2>
                        <div class="grid grid-cols-3 sm:grid-cols-6 gap-3 bg-gray-950/60 p-4 rounded-xl border border-gray-800/80 justify-items-center">
                            @foreach(range(1, 6) as $i)
                                @php
                                    $codigo = 'BL' . $i;
                                    $mesa = $mesas->firstWhere('numero', $codigo);
                                    $precio = $mesa ? $mesa->precio : 0.00;
                                    $id = $mesa ? $mesa->id : '';
                                @endphp
                                <button type="button"
                                        onclick="seleccionarMesaParaEditar('{{ $id }}', '{{ $codigo }}', {{ $precio }})"
                                        class="w-20 h-20 flex flex-col items-center justify-center border-2 border-amber-600/70 bg-amber-950/30 hover:bg-amber-900/50 hover:border-amber-400 rounded-full font-bold text-center transition shadow-md group focus:outline-none focus:ring-2 focus:ring-amber-500 cursor-pointer">
                                    <span class="block text-amber-200 group-hover:text-amber-300 text-sm">{{ $codigo }}</span>
                                    <span id="precio-{{ $codigo }}" class="block text-[10px] text-amber-400/80 font-normal mt-1">${{ number_format($precio, 2) }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <h2 class="text-lg font-bold text-amber-400 mb-3 pb-1 border-b border-gray-800 flex items-center gap-2">
                            <span>Mesas Redondas - Derecha de la Pista (BR1 - BR6)</span>
                        </h2>
                        <div class="grid grid-cols-3 sm:grid-cols-6 gap-3 bg-gray-950/60 p-4 rounded-xl border border-gray-800/80 justify-items-center">
                            @foreach(range(1, 6) as $i)
                                @php
                                    $codigo = 'BR' . $i;
                                    $mesa = $mesas->firstWhere('numero', $codigo);
                                    $precio = $mesa ? $mesa->precio : 0.00;
                                    $id = $mesa ? $mesa->id : '';
                                @endphp
                                <button type="button"
                                        onclick="seleccionarMesaParaEditar('{{ $id }}', '{{ $codigo }}', {{ $precio }})"
                                        class="w-20 h-20 flex flex-col items-center justify-center border-2 border-amber-600/70 bg-amber-950/30 hover:bg-amber-900/50 hover:border-amber-400 rounded-full font-bold text-center transition shadow-md group focus:outline-none focus:ring-2 focus:ring-amber-500 cursor-pointer">
                                    <span class="block text-amber-200 group-hover:text-amber-300 text-sm">{{ $codigo }}</span>
                                    <span id="precio-{{ $codigo }}" class="block text-[10px] text-amber-400/80 font-normal mt-1">${{ number_format($precio, 2) }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <h2 class="text-lg font-bold text-blue-400 mb-3 pb-1 border-b border-gray-800 flex items-center gap-2">
                            <span>Zona D - Junto a la Barra (D1 - D2)</span>
                        </h2>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-gray-950/60 p-4 rounded-xl border border-gray-800/80">
                            @foreach(['D1', 'D2'] as $codigo)
                                @php
                                    $mesa = $mesas->firstWhere('numero', $codigo);
                                    $precio = $mesa ? $mesa->precio : 0.00;
                                    $id = $mesa ? $mesa->id : '';
                                @endphp
                                <button type="button"
                                        onclick="seleccionarMesaParaEditar('{{ $id }}', '{{ $codigo }}', {{ $precio }})"
                                        class="p-3 border border-gray-800 bg-gray-900/80 hover:bg-blue-950/50 hover:border-blue-500/80 rounded-xl font-bold text-center transition shadow-md group focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                    <span class="block text-gray-200 group-hover:text-blue-400 text-base">{{ $codigo }}</span>
                                    <span id="precio-{{ $codigo }}" class="block text-xs text-gray-400 font-normal mt-1">${{ number_format($precio, 2) }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <h2 class="text-lg font-bold text-blue-400 mb-3 pb-1 border-b border-gray-800 flex items-center gap-2">
                            <span>Zona E - Junto a la Entrada (E1 - E2)</span>
                        </h2>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-gray-950/60 p-4 rounded-xl border border-gray-800/80">
                            @foreach(['E1', 'E2'] as $codigo)
                                @php
                                    $mesa = $mesas->firstWhere('numero', $codigo);
                                    $precio = $mesa ? $mesa->precio : 0.00;
                                    $id = $mesa ? $mesa->id : '';
                                @endphp
                                <button type="button"
                                        onclick="seleccionarMesaParaEditar('{{ $id }}', '{{ $codigo }}', {{ $precio }})"
                                        class="p-3 border border-gray-800 bg-gray-900/80 hover:bg-blue-950/50 hover:border-blue-500/80 rounded-xl font-bold text-center transition shadow-md group focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                    <span class="block text-gray-200 group-hover:text-blue-400 text-base">{{ $codigo }}</span>
                                    <span id="precio-{{ $codigo }}" class="block text-xs text-gray-400 font-normal mt-1">${{ number_format($precio, 2) }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div id="vista-segunda-planta" class="space-y-8 hidden">

                    <div>
                        <h2 class="text-lg font-bold text-purple-400 mb-3 pb-1 border-b border-gray-800 flex items-center gap-2">
                            <span>Zona F (F1 - F16)</span>
                        </h2>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-gray-950/60 p-4 rounded-xl border border-gray-800/80">
                            @foreach(range(1, 16) as $i)
                                @php
                                    $codigo = 'F' . $i;
                                    $mesa = $mesas->firstWhere('numero', $codigo);
                                    $precio = $mesa ? $mesa->precio : 0.00;
                                    $id = $mesa ? $mesa->id : '';
                                @endphp
                                <button type="button"
                                        onclick="seleccionarMesaParaEditar('{{ $id }}', '{{ $codigo }}', {{ $precio }})"
                                        class="p-3 border border-gray-800 bg-gray-900/80 hover:bg-purple-950/50 hover:border-purple-500/80 rounded-xl font-bold text-center transition shadow-md group focus:outline-none focus:ring-2 focus:ring-purple-500 cursor-pointer">
                                    <span class="block text-gray-200 group-hover:text-purple-400 text-base">{{ $codigo }}</span>
                                    <span id="precio-{{ $codigo }}" class="block text-xs text-gray-400 font-normal mt-1">${{ number_format($precio, 2) }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <h2 class="text-lg font-bold text-purple-400 mb-3 pb-1 border-b border-gray-800 flex items-center gap-2">
                            <span>Zona A (A1 - A4)</span>
                        </h2>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-gray-950/60 p-4 rounded-xl border border-gray-800/80">
                            @foreach(range(1, 4) as $i)
                                @php
                                    $codigo = 'A' . $i;
                                    $mesa = $mesas->firstWhere('numero', $codigo);
                                    $precio = $mesa ? $mesa->precio : 0.00;
                                    $id = $mesa ? $mesa->id : '';
                                @endphp
                                <button type="button"
                                        onclick="seleccionarMesaParaEditar('{{ $id }}', '{{ $codigo }}', {{ $precio }})"
                                        class="p-3 border border-gray-800 bg-gray-900/80 hover:bg-purple-950/50 hover:border-purple-500/80 rounded-xl font-bold text-center transition shadow-md group focus:outline-none focus:ring-2 focus:ring-purple-500 cursor-pointer">
                                    <span class="block text-gray-200 group-hover:text-purple-400 text-base">{{ $codigo }}</span>
                                    <span id="precio-{{ $codigo }}" class="block text-xs text-gray-400 font-normal mt-1">${{ number_format($precio, 2) }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div id="vista-cover" class="space-y-8 hidden">
                    <div>
                        <h2 class="text-lg font-bold text-amber-400 mb-3 pb-1 border-b border-gray-800 flex items-center gap-2">
                            <span>Precio del Boleto de Cover</span>
                        </h2>
                        <div class="bg-gray-950/60 p-6 rounded-xl border border-gray-800/80 max-w-sm">
                            <p class="text-xs text-gray-400 mb-4">Este es el precio por persona que se cobra al comprar un boleto digital de cover.</p>

                            @if(\App\Models\CoverConfiguracion::entradaLibreActiva())
                                <div class="text-center mb-4">
                                    <span class="inline-block bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 text-sm font-black uppercase tracking-wide px-4 py-2 rounded-lg">
                                        Entrada Libre Activa
                                    </span>
                                    <p class="text-[11px] text-gray-500 mt-2">Los clientes no pagan nada por el cover en este momento.</p>
                                </div>
                            @else
                                <div class="text-center mb-4">
                                    <span class="text-4xl font-black text-amber-400">${{ number_format((float) \App\Models\CoverConfiguracion::precioActual(), 2) }}</span>
                                    <span class="text-sm font-bold text-amber-400/70 ml-1">MXN</span>
                                    <span id="precio-cover-actual" class="hidden">{{ (float) \App\Models\CoverConfiguracion::precioActual() }}</span>
                                </div>
                            @endif

                            <form id="form-modificar-precio-cover">
                                @csrf
                                @method('PATCH')
                                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Nuevo Precio de Cover ($)</label>
                                <div class="relative rounded-lg shadow-sm mb-3">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">$</span>
                                    </div>
                                    <input type="text"
                                           inputmode="decimal"
                                           id="precio-cover-input"
                                           name="precio"
                                           value="{{ number_format((float) \App\Models\CoverConfiguracion::precioActual(), 2) }}"
                                           onblur="formatearCampoPrecio(this)"
                                           class="w-full pl-7 bg-gray-950 border border-gray-800 rounded-lg p-2.5 text-white font-semibold focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none"
                                           required>
                                </div>
                                <button type="submit" class="w-full bg-amber-600 hover:bg-amber-500 text-black font-bold py-2.5 px-4 rounded-lg transition shadow-lg text-sm cursor-pointer">
                                    Guardar Precio de Cover
                                </button>
                            </form>

                            <div class="mt-3 pt-3 border-t border-gray-800/80">
                                <button type="button"
                                        id="btn-entrada-libre"
                                        onclick="activarEntradaLibre()"
                                        class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-2.5 px-4 rounded-lg transition shadow-lg text-sm cursor-pointer">
                                    Poner Entrada Libre
                                </button>
                                <p class="text-[11px] text-gray-500 mt-2 text-center">La página de compra de cover mostrará "Entrada Libre" y no se cobrará nada. Para volver a cobrar, guarda un precio arriba.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="bg-gray-900/70 p-6 rounded-2xl shadow-2xl border border-gray-800 backdrop-blur-md h-fit sticky top-6">
                <h2 class="text-xl font-bold mb-4 text-white border-b border-gray-800 pb-2">Modificar Precio</h2>

                <form id="form-modificar-precio" action="" method="POST">
                    @csrf
                    @method('PATCH')

                    <div id="mensaje-seleccion" class="p-6 border border-dashed border-gray-800 rounded-xl text-center text-gray-400 text-sm bg-gray-950/30">
                        Haz clic en cualquier mesa de la planta actual para editar su tarifa.
                    </div>

                    <div id="campos-edicion" class="space-y-4 hidden">
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Mesa Seleccionada</label>
                            <input type="text" id="mesa_nombre_display" class="w-full bg-gray-950 border border-gray-800 rounded-lg p-2.5 text-white font-bold outline-none cursor-not-allowed" readonly>
                        </div>

                        <div>
                            <label for="precio" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Nuevo Precio ($)</label>
                            <div class="relative rounded-lg shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500">$</span>
                                </div>
                                <input type="text"
                                       inputmode="decimal"
                                       id="precio"
                                       name="precio"
                                       onblur="formatearCampoPrecio(this)"
                                       class="w-full pl-7 bg-gray-950 border border-gray-800 rounded-lg p-2.5 text-white font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                                       placeholder="0.00"
                                       required>
                            </div>
                        </div>

                        <div class="pt-2 flex gap-2">
                            <button type="button"
                                    onclick="cancelarEdicion()"
                                    class="w-1/3 bg-gray-800 hover:bg-gray-700 text-gray-300 font-bold py-2.5 px-4 rounded-lg transition text-sm cursor-pointer">
                                Cancelar
                            </button>
                            <button type="submit"
                                    class="w-2/3 bg-blue-600 hover:bg-blue-500 text-white font-bold py-2.5 px-4 rounded-lg transition shadow-lg text-sm cursor-pointer">
                                Guardar Precio
                            </button>
                        </div>

                        <div class="pt-3 mt-3 border-t border-gray-800/60">
                            <p class="text-xs text-gray-400 mb-2">
                                Estado: <span id="mesa_estado_display" class="font-bold"></span>
                            </p>
                            <button type="button"
                                    id="btn-toggle-disponible"
                                    onclick="toggleDisponibilidadMesa()"
                                    class="w-full font-bold py-2.5 px-4 rounded-lg transition text-sm cursor-pointer">
                            </button>
                            <p class="text-[11px] text-gray-500 mt-2">Márcala como reservada si alguien apartó esta mesa por fuera de la página web (por teléfono, en persona, etc.). También úsalo para liberarla después de un evento.</p>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>

        const mesasDisponibilidad = {
            @foreach($mesas as $m)
                '{{ $m->id }}': {{ $m->disponible ? 'true' : 'false' }},
            @endforeach
        };

        function cambiarPlanta(planta) {
            const vistaBaja = document.getElementById('vista-planta-baja');
            const vistaAlta = document.getElementById('vista-segunda-planta');
            const vistaCover = document.getElementById('vista-cover');
            const btnBaja = document.getElementById('btn-planta-baja');
            const btnAlta = document.getElementById('btn-segunda-planta');
            const btnCover = document.getElementById('btn-cover');

            const inactivo = "px-5 py-2 rounded-lg font-bold text-sm transition text-gray-400 hover:text-white hover:bg-gray-800/60 cursor-pointer";

            vistaBaja.classList.add('hidden');
            vistaAlta.classList.add('hidden');
            vistaCover.classList.add('hidden');
            btnBaja.className = inactivo;
            btnAlta.className = inactivo;
            btnCover.className = inactivo;

            if (planta === 'baja') {
                vistaBaja.classList.remove('hidden');
                btnBaja.className = "px-5 py-2 rounded-lg font-bold text-sm transition shadow-lg bg-blue-600 text-white cursor-pointer";
            } else if (planta === 'cover') {
                vistaCover.classList.remove('hidden');
                btnCover.className = "px-5 py-2 rounded-lg font-bold text-sm transition shadow-lg bg-amber-600 text-black cursor-pointer";
            } else {
                vistaAlta.classList.remove('hidden');
                btnAlta.className = "px-5 py-2 rounded-lg font-bold text-sm transition shadow-lg bg-purple-600 text-white cursor-pointer";
                btnBaja.className = "px-5 py-2 rounded-lg font-bold text-sm transition text-gray-400 hover:text-white hover:bg-gray-800/60 cursor-pointer";
            }
            cancelarEdicion();
        }

        let mesaSeleccionadaId = null;
        let mesaSeleccionadaCodigo = null;

        function seleccionarMesaParaEditar(id, nombre, precioActual) {
            if (!id) {
                mostrarToast('Esta mesa todavía no existe en la base de datos. Ejecuta las migraciones/seeders antes de editar su precio.', 'error');
                return;
            }

            const form = document.getElementById('form-modificar-precio');
            form.action = `/admin/mesas/${id}/precio`;

            mesaSeleccionadaId = id;
            mesaSeleccionadaCodigo = nombre;

            document.getElementById('mesa_nombre_display').value = 'Mesa ' + nombre;
            document.getElementById('precio').value = Number(precioActual).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            document.getElementById('mensaje-seleccion').classList.add('hidden');
            document.getElementById('campos-edicion').classList.remove('hidden');

            actualizarEstadoMesaUI(mesasDisponibilidad[id]);

            const inputPrecio = document.getElementById('precio');
            inputPrecio.focus();
            inputPrecio.select();
        }

        function actualizarEstadoMesaUI(disponible) {
            const estadoEl = document.getElementById('mesa_estado_display');
            const btn = document.getElementById('btn-toggle-disponible');

            if (disponible) {
                estadoEl.textContent = 'Disponible';
                estadoEl.className = 'font-bold text-emerald-400';
                btn.textContent = 'Marcar como Reservada';
                btn.className = 'w-full bg-red-700 hover:bg-red-600 text-white font-bold py-2.5 px-4 rounded-lg transition text-sm cursor-pointer';
            } else {
                estadoEl.textContent = 'Reservada (bloqueada en la web)';
                estadoEl.className = 'font-bold text-red-400';
                btn.textContent = 'Marcar como Disponible';
                btn.className = 'w-full bg-emerald-700 hover:bg-emerald-600 text-white font-bold py-2.5 px-4 rounded-lg transition text-sm cursor-pointer';
            }
        }

        function toggleDisponibilidadMesa() {
            if (!mesaSeleccionadaId) {
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const btn = document.getElementById('btn-toggle-disponible');
            const textoOriginal = btn.textContent;

            btn.disabled = true;
            btn.textContent = 'Guardando...';

            fetch(`/admin/mesas/${mesaSeleccionadaId}/disponibilidad`, {
                method: 'PATCH',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
            .then(async (response) => {
                const data = await response.json().catch(() => null);

                if (!response.ok || !data || !data.success) {
                    const mensajeError = (data && data.message)
                        ? data.message
                        : 'No se pudo actualizar el estado de la mesa.';
                    throw new Error(mensajeError);
                }

                return data;
            })
            .then((data) => {
                mostrarToast(data.message, 'success');
                mesasDisponibilidad[mesaSeleccionadaId] = data.mesa.disponible;
                actualizarEstadoMesaUI(data.mesa.disponible);
            })
            .catch((error) => {
                mostrarToast(error.message, 'error');
            })
            .finally(() => {
                btn.disabled = false;
            });
        }

        function cancelarEdicion() {
            const form = document.getElementById('form-modificar-precio');
            form.action = '';

            mesaSeleccionadaId = null;
            mesaSeleccionadaCodigo = null;

            document.getElementById('mesa_nombre_display').value = '';
            document.getElementById('precio').value = '';

            document.getElementById('campos-edicion').classList.add('hidden');
            document.getElementById('mensaje-seleccion').classList.remove('hidden');
        }

        let toastTimeout = null;
        function mostrarToast(mensaje, tipo = 'success') {
            const toast = document.getElementById('toast-notificacion');
            const contenido = document.getElementById('toast-contenido');
            const icono = document.getElementById('toast-icono');
            const texto = document.getElementById('toast-mensaje');

            texto.textContent = mensaje;

            if (tipo === 'error') {
                contenido.className = 'p-4 rounded-xl shadow-2xl border backdrop-blur-md flex items-start gap-3 bg-red-950/90 border-red-500 text-red-300';
                icono.textContent = '✕';
            } else {
                contenido.className = 'p-4 rounded-xl shadow-2xl border backdrop-blur-md flex items-start gap-3 bg-emerald-950/90 border-emerald-500 text-emerald-300';
                icono.textContent = '✓';
            }

            toast.classList.remove('translate-x-[120%]', 'opacity-0');

            clearTimeout(toastTimeout);
            toastTimeout = setTimeout(() => {
                toast.classList.add('translate-x-[120%]', 'opacity-0');
            }, 4000);
        }

        // El admin puede escribir el precio con comas de miles (ej. "1,500.00");
        // aquí se limpia antes de mandarlo, así la coma nunca afecta lo que se cobra.
        function limpiarPrecio(valorTexto) {
            const limpio = String(valorTexto).replace(/,/g, '').trim();
            const numero = parseFloat(limpio);
            return Number.isFinite(numero) ? numero : NaN;
        }

        function formatearCampoPrecio(input) {
            const numero = limpiarPrecio(input.value);
            if (Number.isFinite(numero) && numero >= 0) {
                input.value = numero.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
        }

        document.getElementById('form-modificar-precio').addEventListener('submit', function (e) {
            e.preventDefault();

            if (!mesaSeleccionadaId) {
                mostrarToast('Selecciona primero una mesa de la lista.', 'error');
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const precioInput = document.getElementById('precio');
            const precioLimpio = limpiarPrecio(precioInput.value);

            if (!Number.isFinite(precioLimpio) || precioLimpio < 0) {
                mostrarToast('Ingresa un precio válido.', 'error');
                return;
            }

            const submitBtn = e.target.querySelector('button[type="submit"]');
            const textoOriginalBtn = submitBtn.textContent;

            submitBtn.disabled = true;
            submitBtn.textContent = 'Guardando...';

            fetch(`/admin/mesas/${mesaSeleccionadaId}/precio`, {
                method: 'PATCH',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    precio: precioLimpio,
                }),
            })
            .then(async (response) => {
                const data = await response.json().catch(() => null);

                if (!response.ok || !data || !data.success) {
                    const mensajeError = (data && data.message)
                        ? data.message
                        : 'No se pudo guardar el precio. Verifica el valor e intenta de nuevo.';
                    throw new Error(mensajeError);
                }

                return data;
            })
            .then((data) => {

                const spanPrecio = document.getElementById('precio-' + data.mesa.numero);
                if (spanPrecio) {
                    spanPrecio.textContent = '$' + Number(data.mesa.precio).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }

                mostrarToast(data.message, 'success');
                cancelarEdicion();
            })
            .catch((error) => {
                mostrarToast(error.message, 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = textoOriginalBtn;
            });
        });

        document.getElementById('form-modificar-precio-cover').addEventListener('submit', function (e) {
            e.preventDefault();

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const precioInput = document.getElementById('precio-cover-input');
            const precioLimpio = limpiarPrecio(precioInput.value);

            if (!Number.isFinite(precioLimpio) || precioLimpio < 0) {
                mostrarToast('Ingresa un precio de cover válido.', 'error');
                return;
            }

            const submitBtn = e.target.querySelector('button[type="submit"]');
            const textoOriginalBtn = submitBtn.textContent;

            submitBtn.disabled = true;
            submitBtn.textContent = 'Guardando...';

            fetch('{{ route('admin.mesas.cover.update_precio') }}', {
                method: 'PATCH',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    precio: precioLimpio,
                }),
            })
            .then(async (response) => {
                const data = await response.json().catch(() => null);

                if (!response.ok || !data || !data.success) {
                    const mensajeError = (data && data.message)
                        ? data.message
                        : 'No se pudo guardar el precio del cover. Verifica el valor e intenta de nuevo.';
                    throw new Error(mensajeError);
                }

                return data;
            })
            .then((data) => {
                mostrarToast(data.message, 'success');
                if (typeof data.precio !== 'undefined') {
                    precioInput.value = Number(data.precio).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
            })
            .catch((error) => {
                mostrarToast(error.message, 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = textoOriginalBtn;
            });
        });

        function activarEntradaLibre() {
            if (!confirm('¿Poner el cover en Entrada Libre? Los clientes dejarán de pagar hasta que guardes un precio de nuevo.')) {
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const btn = document.getElementById('btn-entrada-libre');
            const textoOriginal = btn.textContent;

            btn.disabled = true;
            btn.textContent = 'Activando...';

            fetch('{{ route('admin.mesas.cover.entrada_libre') }}', {
                method: 'PATCH',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
            .then(async (response) => {
                const data = await response.json().catch(() => null);

                if (!response.ok || !data || !data.success) {
                    const mensajeError = (data && data.message)
                        ? data.message
                        : 'No se pudo activar Entrada Libre. Intenta de nuevo.';
                    throw new Error(mensajeError);
                }

                return data;
            })
            .then((data) => {
                mostrarToast(data.message, 'success');
                window.location.reload();
            })
            .catch((error) => {
                mostrarToast(error.message, 'error');
                btn.disabled = false;
                btn.textContent = textoOriginal;
            });
        }

        const canvas = document.getElementById('starfield');
        const ctx = canvas.getContext('2d');

        let stars = [];
        const numStars = 350;

        function resizeCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }

        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();

        class Star {
            constructor() {
                this.reset();
            }

            reset() {
                this.x = Math.random() * canvas.width;
                this.y = Math.random() * canvas.height;
                this.size = Math.random() * 1.8 + 0.2;
                this.vx = (Math.random() - 0.5) * 0.3;
                this.vy = (Math.random() - 0.5) * 0.3;
                this.alpha = Math.random();
                this.alphaChange = (Math.random() * 0.02) + 0.005;
            }

            update() {
                this.x += this.vx;
                this.y += this.vy;

                this.alpha += this.alphaChange;
                if (this.alpha <= 0.1 || this.alpha >= 1) {
                    this.alphaChange = -this.alphaChange;
                }

                if (this.x < 0 || this.x > canvas.width || this.y < 0 || this.y > canvas.height) {
                    this.reset();
                }
            }

            draw() {
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(255, 255, 255, ${this.alpha})`;
                ctx.shadowBlur = this.size > 1 ? 8 : 0;
                ctx.shadowColor = '#ffffff';
                ctx.fill();
            }
        }

        for (let i = 0; i < numStars; i++) {
            stars.push(new Star());
        }

        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            stars.forEach(star => {
                star.update();
                star.draw();
            });
            requestAnimationFrame(animate);
        }

        animate();
    </script>
</body>
</html>