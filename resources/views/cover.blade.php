<!DOCTYPE html>
<html lang="es" class="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Boleto de Cover - Victoria Luxury Club</title>
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://js.stripe.com/v3/"></script>
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
      <p class="text-zinc-500 text-xs mt-2">Precio por persona: <span class="text-amber-400 font-bold">${{ number_format($precioCover, 2) }}</span> MXN</p>
    </div>

    @if ($precioCover <= 0)
      <div class="bg-amber-500/10 border border-amber-500/40 text-amber-300 text-sm rounded-xl p-4 text-center">
        La venta de cover todavía no está disponible. El administrador aún no ha configurado el precio.
      </div>
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

    <form id="form-cover" action="{{ route('cover.procesar') }}" method="POST" class="space-y-6 {{ $precioCover <= 0 ? 'opacity-40 pointer-events-none' : '' }}">
      @csrf

      <div class="bg-zinc-900/80 backdrop-blur-md border border-zinc-800 rounded-2xl p-6 space-y-6">
        <div>
          <label class="block text-xs font-bold text-zinc-400 mb-2">NOMBRE COMPLETO</label>
          <input type="text" name="nombre" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-amber-500">
        </div>

        <div>
          <label class="block text-xs font-bold text-zinc-400 mb-2">FECHA</label>
          <input type="date" name="fecha" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-amber-500">
        </div>

        <!-- Un boleto de cover = una persona (el nombre de arriba). -->
        <input type="hidden" id="input_cantidad" name="cantidad" value="1">

        <div class="bg-amber-500/10 border border-amber-500/40 rounded-xl p-4 flex justify-between items-center">
          <span class="text-xs text-amber-400 font-bold uppercase">Total a Pagar:</span>
          <span id="txt_total_cover" class="text-2xl font-black text-amber-400">${{ number_format($precioCover, 2) }}</span>
        </div>

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
          <div id="stripe-elemento-cover" class="bg-zinc-950 border border-zinc-800 rounded-xl p-4"></div>
          <div id="stripe-error-cover" class="text-xs text-red-400"></div>
          <button type="button" id="btn-pagar-tarjeta-cover" onclick="pagarConTarjetaCover()" class="w-full bg-amber-500 hover:bg-amber-400 text-black font-extrabold py-4 rounded-xl transition-all shadow-lg shadow-amber-500/10">
            Pagar y Obtener Boleto
          </button>
        </div>

        <div id="panel-paypal-cover" class="space-y-3 hidden">
          <div id="paypal-boton-cover"></div>
          <div id="paypal-error-cover" class="text-xs text-red-400"></div>
        </div>

        <input type="hidden" name="metodo_pago" id="input_metodo_pago_cover" value="tarjeta">
        <input type="hidden" name="referencia_pago" id="input_referencia_pago_cover">
      </div>
    </form>
  </div>

  <script>
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const PRECIO_COVER = {{ (float) $precioCover }};
    const STRIPE_PUBLIC_KEY = "{{ config('services.stripe.key') }}";
    const stripe = STRIPE_PUBLIC_KEY ? Stripe(STRIPE_PUBLIC_KEY) : null;
    let stripeElementsCover = null;
    let paypalRenderedCover = false;

    function obtenerCantidad() {
      // Cada boleto de cover es para una sola persona (la del campo "Nombre").
      return 1;
    }

    function actualizarTotalCover() {
      const total = PRECIO_COVER * obtenerCantidad();
      document.getElementById('txt_total_cover').textContent = '$' + total.toFixed(2);
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

    async function prepararPagoCoverSegunMetodo() {
      if (!stripe || document.getElementById('input_metodo_pago_cover').value !== 'tarjeta') {
        return;
      }

      try {
        const respuesta = await fetch('{{ route('pago.cover.stripe.intento') }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json',
          },
          body: JSON.stringify({ cantidad: obtenerCantidad() }),
        });

        const datos = await respuesta.json();

        if (!datos.success) {
          document.getElementById('stripe-error-cover').textContent = datos.message || 'No se pudo iniciar el pago.';
          return;
        }

        stripeElementsCover = stripe.elements({ clientSecret: datos.client_secret });
        const paymentElement = stripeElementsCover.create('payment');
        document.getElementById('stripe-elemento-cover').innerHTML = '';
        paymentElement.mount('#stripe-elemento-cover');
      } catch (error) {
        document.getElementById('stripe-error-cover').textContent = 'No se pudo conectar con el procesador de pagos.';
      }
    }

    async function pagarConTarjetaCover() {
      if (!stripe || !stripeElementsCover) {
        document.getElementById('stripe-error-cover').textContent = 'El pago con tarjeta no está disponible en este momento.';
        return;
      }

      const boton = document.getElementById('btn-pagar-tarjeta-cover');
      boton.disabled = true;
      boton.textContent = 'Procesando...';

      const { error, paymentIntent } = await stripe.confirmPayment({
        elements: stripeElementsCover,
        redirect: 'if_required',
      });

      if (error) {
        document.getElementById('stripe-error-cover').textContent = error.message || 'No se pudo procesar el pago.';
        boton.disabled = false;
        boton.textContent = 'Pagar y Obtener Boleto';
        return;
      }

      document.getElementById('input_referencia_pago_cover').value = paymentIntent.id;
      document.getElementById('form-cover').submit();
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
            body: JSON.stringify({ cantidad: obtenerCantidad() }),
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
            body: JSON.stringify({ orden_id: data.orderID, cantidad: obtenerCantidad() }),
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

    document.getElementById('form-cover').addEventListener('submit', function (e) {
      if (!document.getElementById('input_referencia_pago_cover').value) {
        e.preventDefault();
        alert('Completa el pago antes de continuar.');
        return false;
      }
    });

    prepararPagoCoverSegunMetodo();
  </script>

</body>
</html>
