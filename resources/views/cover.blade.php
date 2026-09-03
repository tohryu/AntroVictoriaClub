<!DOCTYPE html>
<html lang="es" class="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Boleto de Cover - Victoria Luxury Club</title>
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <link rel="preconnect" href="https://cdn.tailwindcss.com">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://pay.conekta.com/v1.0/js/conekta-checkout.min.js"></script>
  <script src="https://www.paypal.com/sdk/js?client-id={{ config('services.paypal.mode') === 'live' ? config('services.paypal.live.client_id') : config('services.paypal.sandbox.client_id') }}&currency=MXN"></script>
</head>
<body class="bg-black text-white min-h-screen p-4 sm:p-8 relative overflow-x-hidden">

  <div class="fixed inset-0 pointer-events-none -z-10 overflow-hidden">
    <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[600px] h-[600px] bg-amber-500/10 rounded-full blur-[140px]"></div>
    <div class="absolute -bottom-40 -left-20 w-[500px] h-[500px] bg-amber-600/10 rounded-full blur-[120px]"></div>
  </div>

  <div class="max-w-lg mx-auto space-y-8 relative z-10">
    <div class="text-center">
      <span class="text-xs font-semibold text-amber-400 uppercase tracking-widest">Boleto Digital</span>
      <h1 class="text-3xl font-black text-white mt-1">Compra tu Cover</h1>
      @if($entradaLibre)
        <p class="text-zinc-500 text-xs mt-2">
          <span class="text-emerald-400 font-bold uppercase tracking-wide">Entrada Libre</span> — no se cobra nada hoy.
        </p>
      @else
        <p class="text-zinc-500 text-xs mt-2">Precio por persona: <span class="text-amber-400 font-bold">${{ number_format($precioCover, 2) }}</span> MXN</p>
      @endif
    </div>

    @php
      $coverFormHabilitado = $modoGeneral ? ($fechaGeneral && ! $bloqueoGeneral && ($entradaLibre || $precioCover > 0)) : ($eventoActivo && $ventasActivas && ($entradaLibre || $precioCover > 0));
    @endphp

    @if ($modoGeneral)
      <div class="bg-zinc-900/80 backdrop-blur-md border border-zinc-800 rounded-2xl p-6">
        <label class="block text-xs font-bold text-zinc-400 mb-2">ELIGE LA FECHA</label>
        <form action="{{ route('cover.formulario') }}" method="GET" class="flex gap-3">
          <input type="date" name="fecha" value="{{ $fechaGeneral }}" required class="flex-1 bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-amber-500 [color-scheme:dark]">
          <button type="submit" class="bg-amber-500 hover:bg-amber-400 text-black font-bold px-6 rounded-xl">Ver</button>
        </form>
        <p class="text-[10px] text-zinc-600 mt-2">Días de cover general: {{ \App\Models\DiaOperacionGeneral::nombresDiasActivos() }}.</p>
      </div>

      @if ($bloqueoGeneral && $bloqueoGeneral['tipo'] === 'evento')
        <div class="bg-amber-500/10 border border-amber-500/40 text-amber-300 text-sm rounded-xl p-4 text-center">
          La compra de cover de <strong>{{ \Carbon\Carbon::parse($fechaGeneral)->locale('es')->isoFormat('D [de] MMMM') }}</strong> se hace por medio del evento
          <strong>{{ $bloqueoGeneral['evento']->titulo }}</strong>.
          <a href="{{ route('cover.formulario', ['evento' => $bloqueoGeneral['evento']->id]) }}" class="underline text-amber-300">Ir a ese evento</a>
        </div>
      @elseif ($bloqueoGeneral && $bloqueoGeneral['tipo'] === 'cerrado')
        <div class="bg-red-950/40 border border-red-500/40 text-red-300 text-sm rounded-xl p-4 text-center">
          Esos días el club permanece cerrado. Días abiertos: {{ $bloqueoGeneral['dias'] }}.
        </div>
      @elseif ($fechaGeneral && $precioCover <= 0 && ! $entradaLibre)
        <div class="bg-amber-500/10 border border-amber-500/40 text-amber-300 text-sm rounded-xl p-4 text-center">
          La venta de cover general todavía no está disponible. El administrador aún no ha configurado el precio.
        </div>
      @endif
    @else
      @if ($precioCover <= 0 && ! $entradaLibre)
        <div class="bg-amber-500/10 border border-amber-500/40 text-amber-300 text-sm rounded-xl p-4 text-center">
          La venta de cover todavía no está disponible. El administrador aún no ha configurado el precio.
        </div>
      @endif

      @if (! $eventoActivo)
        <div class="bg-amber-500/10 border border-amber-500/40 text-amber-300 text-sm rounded-xl p-4 text-center">
          No hay ningún evento próximo configurado todavía. Vuelve más tarde.
        </div>
      @elseif (! $ventasActivas)
        <div class="bg-amber-500/10 border border-amber-500/40 text-amber-300 text-sm rounded-xl p-4 text-center">
          La venta de cover para <strong>{{ $eventoActivo->titulo }}</strong> ({{ \Carbon\Carbon::parse($eventoActivo->fecha)->locale('es')->isoFormat('D [de] MMMM, YYYY') }}) todavía no está abierta. Vuelve más tarde.
        </div>
      @endif
    @endif

    @if ($errors->any())
      <div class="bg-red-950/60 border border-red-500/50 text-red-300 text-sm rounded-xl p-4">
        <ul class="list-disc list-inside space-y-1">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form id="form-cover" action="{{ route('cover.procesar') }}" method="POST" class="space-y-6 {{ ! $coverFormHabilitado ? 'opacity-40 pointer-events-none' : '' }}">
      @csrf

      <div class="bg-zinc-900/80 backdrop-blur-md border border-zinc-800 rounded-2xl p-6 space-y-6">
        <div>
          <label class="block text-xs font-bold text-zinc-400 mb-2">NOMBRE COMPLETO</label>
          <input type="text" name="nombre" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-amber-500">
        </div>

        <div>
          <label class="block text-xs font-bold text-zinc-400 mb-2">FECHA</label>
          <input type="date"
                 id="input_fecha_cover"
                 name="fecha_display"
                 value="{{ $eventoActivo ? $eventoActivo->fecha : ($fechaGeneral ?? '') }}"
                 disabled
                 class="w-full bg-zinc-900 border border-zinc-800 rounded-xl px-4 py-3 text-zinc-400 text-sm cursor-not-allowed [color-scheme:dark]">
          <p class="text-[10px] text-zinc-600 mt-1">{{ $modoGeneral ? 'Cambia la fecha arriba.' : 'Fija: es la fecha del evento activo, no se puede cambiar.' }}</p>
        </div>

        <!-- Un boleto de cover = una persona (el nombre de arriba). -->
        <input type="hidden" id="input_cantidad" name="cantidad" value="1">

        @if($entradaLibre)
          <div class="bg-emerald-500/10 border border-emerald-500/40 rounded-xl p-4 flex justify-between items-center">
            <span class="text-xs text-emerald-400 font-bold uppercase">Total a Pagar:</span>
            <span class="text-2xl font-black text-emerald-400">Entrada Libre</span>
          </div>
        @else
          <div class="bg-amber-500/10 border border-amber-500/40 rounded-xl p-4 flex justify-between items-center">
            <span class="text-xs text-amber-400 font-bold uppercase">Total a Pagar:</span>
            <span id="txt_total_cover" class="text-2xl font-black text-amber-400">${{ number_format($precioCover, 2) }} <span class="text-sm">MXN</span></span>
          </div>
        @endif

        @if($entradaLibre)
          <div class="bg-emerald-500/10 border border-emerald-500/40 rounded-xl p-4 text-center">
            <p class="text-emerald-400 text-xs font-bold uppercase mb-3">✓ Entrada Libre — sin costo</p>
            <button type="button" id="btn-confirmar-cover-entrada-libre" onclick="confirmarCoverEntradaLibre()" class="w-full bg-emerald-500 hover:bg-emerald-400 text-black font-extrabold py-4 rounded-xl transition-all shadow-lg shadow-emerald-500/10">
              Confirmar y Obtener QR
            </button>
          </div>
        @else
        <div>
          <label class="block text-xs font-bold text-zinc-400 mb-2">MÉTODO DE PAGO</label>
          <div class="grid grid-cols-2 gap-4">
            <label class="border border-zinc-800 bg-zinc-950 rounded-xl p-3 cursor-pointer flex items-center gap-2">
              <input type="radio" name="metodo_pago_ui" value="tarjeta" checked onchange="cambiarMetodoPagoCover('tarjeta')" class="accent-amber-500">
              <span class="text-xs font-medium">Tarjeta</span>
            </label>
            <label class="border border-zinc-800 bg-zinc-950 rounded-xl p-3 cursor-pointer flex items-center gap-2">
              <input type="radio" name="metodo_pago_ui" value="paypal" onchange="cambiarMetodoPagoCover('paypal')" class="accent-amber-500">
              <span class="text-xs font-medium">PayPal</span>
            </label>
          </div>
        </div>

        <div id="panel-tarjeta-cover" class="space-y-3">
          <div id="conekta-error-cover" class="text-xs text-red-400"></div>
          <div id="conekta-checkout-target-cover" class="bg-zinc-950 border border-zinc-800 rounded-xl overflow-hidden"></div>
          <p id="conekta-cargando-cover" class="text-xs text-zinc-500 text-center hidden">Cargando pago con tarjeta...</p>
        </div>

        <div id="panel-paypal-cover" class="space-y-3 hidden">
          <div id="paypal-boton-cover"></div>
          <div id="paypal-error-cover" class="text-xs text-red-400"></div>
        </div>
        @endif

        <input type="hidden" name="metodo_pago" id="input_metodo_pago_cover" value="{{ $entradaLibre ? 'entrada_libre' : 'tarjeta' }}">
        <input type="hidden" name="referencia_pago" id="input_referencia_pago_cover">
        <input type="hidden" name="evento_id" value="{{ $eventoActivo->id ?? '' }}">
        <input type="hidden" name="fecha_general" value="{{ $fechaGeneral ?? '' }}">
      </div>
    </form>
  </div>

  <script>
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const PRECIO_COVER = {{ (float) $precioCover }};
    const CONEKTA_PUBLIC_KEY = "{{ config('services.conekta.public_key') }}";
    const EVENTO_ID = {{ $eventoActivo->id ?? 'null' }};
    const FECHA_GENERAL = {!! $fechaGeneral ? "'".$fechaGeneral."'" : 'null' !!};
    let paypalRenderedCover = false;

    function obtenerCantidad() {
      // Cada boleto de cover es para una sola persona (la del campo "Nombre").
      return 1;
    }

    function actualizarTotalCover() {
      const total = PRECIO_COVER * obtenerCantidad();
      document.getElementById('txt_total_cover').innerHTML = '$' + total.toFixed(2) + ' <span class="text-sm">MXN</span>';
      prepararPagoCoverSegunMetodo();
    }

    function cambiarMetodoPagoCover(metodo) {
      document.getElementById('input_metodo_pago_cover').value = metodo;

      const panelTarjeta = document.getElementById('panel-tarjeta-cover');
      const panelPaypal = document.getElementById('panel-paypal-cover');

      if (metodo === 'tarjeta') {
        panelTarjeta.classList.remove('hidden');
        panelPaypal.classList.add('hidden');
        prepararPagoCoverSegunMetodo();
      } else {
        panelTarjeta.classList.add('hidden');
        panelPaypal.classList.remove('hidden');
        renderizarBotonPaypalCover();
      }
    }

    function prepararPagoCoverSegunMetodo() {
      if (document.getElementById('input_metodo_pago_cover').value !== 'tarjeta') {
        return;
      }
      cargarCheckoutConektaCover();
    }

    async function cargarCheckoutConektaCover() {
      const errorEl = document.getElementById('conekta-error-cover');
      const cargandoEl = document.getElementById('conekta-cargando-cover');
      errorEl.textContent = '';

      if (!CONEKTA_PUBLIC_KEY) {
        errorEl.textContent = 'El pago con tarjeta no está disponible en este momento.';
        return;
      }

      cargandoEl.classList.remove('hidden');

      try {
        const respuesta = await fetch('{{ route('pago.cover.conekta.orden') }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json',
          },
          body: JSON.stringify({ cantidad: obtenerCantidad(), evento_id: EVENTO_ID, fecha_general: FECHA_GENERAL }),
        });

        const datos = await respuesta.json();

        if (!datos.success || !datos.checkout_id) {
          errorEl.textContent = datos.message || 'No se pudo iniciar el pago.';
          cargandoEl.classList.add('hidden');
          return;
        }

        cargandoEl.classList.add('hidden');
        document.getElementById('conekta-checkout-target-cover').innerHTML = '';

        window.ConektaCheckoutComponents.Integration({
          config: {
            locale: 'es',
            publicKey: CONEKTA_PUBLIC_KEY,
            targetIFrame: '#conekta-checkout-target-cover',
            checkoutRequestId: datos.checkout_id,
          },
          callbacks: {
            onFinalizePayment: function (orden) {
              document.getElementById('input_referencia_pago_cover').value = orden.id;
              document.getElementById('form-cover').submit();
            },
            onErrorPayment: function (error) {
              errorEl.textContent = typeof error === 'string' ? error : 'No se pudo procesar el pago.';
            },
          },
          options: {
            autoResize: true,
          },
        });
      } catch (error) {
        errorEl.textContent = 'No se pudo conectar con el procesador de pagos.';
        cargandoEl.classList.add('hidden');
      }
    }

    function renderizarBotonPaypalCover() {
      if (paypalRenderedCover || typeof paypal === 'undefined') {
        return;
      }
      paypalRenderedCover = true;

      paypal.Buttons({
        createOrder: async function () {
          const respuesta = await fetch('{{ route('pago.cover.paypal.orden') }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': CSRF_TOKEN,
              'Accept': 'application/json',
            },
            body: JSON.stringify({ cantidad: obtenerCantidad(), evento_id: EVENTO_ID, fecha_general: FECHA_GENERAL }),
          });

          const datos = await respuesta.json();

          if (!datos.success) {
            document.getElementById('paypal-error-cover').textContent = datos.message || 'No se pudo iniciar el pago con PayPal.';
            throw new Error(datos.message);
          }

          return datos.orden_id;
        },
        onApprove: async function (data) {
          const respuesta = await fetch('{{ route('pago.cover.paypal.capturar') }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': CSRF_TOKEN,
              'Accept': 'application/json',
            },
            body: JSON.stringify({ orden_id: data.orderID, cantidad: obtenerCantidad(), evento_id: EVENTO_ID, fecha_general: FECHA_GENERAL }),
          });

          const datos = await respuesta.json();

          if (!datos.success) {
            document.getElementById('paypal-error-cover').textContent = datos.message || 'No se pudo confirmar el pago.';
            return;
          }

          document.getElementById('input_metodo_pago_cover').value = 'paypal';
          document.getElementById('input_referencia_pago_cover').value = data.orderID;
          document.getElementById('form-cover').submit();
        },
        onError: function () {
          document.getElementById('paypal-error-cover').textContent = 'Ocurrió un error con PayPal. Intenta de nuevo.';
        },
      }).render('#paypal-boton-cover');
    }

    function confirmarCoverEntradaLibre() {
      const nombre = document.querySelector('input[name="nombre"]').value.trim();
      const fecha = document.getElementById('input_fecha_cover').value;

      if (!nombre || !fecha) {
        alert('Completa tu nombre y fecha antes de continuar.');
        return;
      }

      const boton = document.getElementById('btn-confirmar-cover-entrada-libre');
      boton.disabled = true;
      boton.textContent = 'Generando QR...';

      document.getElementById('input_metodo_pago_cover').value = 'entrada_libre';
      document.getElementById('input_referencia_pago_cover').value = 'ENTRADA-LIBRE-' + Date.now();
      document.getElementById('form-cover').submit();
    }

    document.getElementById('form-cover').addEventListener('submit', function (e) {
      if (!document.getElementById('input_referencia_pago_cover').value) {
        e.preventDefault();
        alert('Completa el pago antes de continuar.');
        return false;
      }
    });
  </script>

</body>
</html>
