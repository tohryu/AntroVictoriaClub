@php
    $mesas = $mesas ?? \App\Models\Mesa::all();
    $mesasReservadasIds = $mesasReservadasIds ?? [];
@endphp

<!DOCTYPE html>
<html lang="es" class="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Selección de Mesa - Victoria Luxury Club</title>
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://js.stripe.com/v3/"></script>
  <script src="https://www.paypal.com/sdk/js?client-id={{ config('services.paypal.mode') === 'live' ? config('services.paypal.live.client_id') : config('services.paypal.sandbox.client_id') }}&currency=MXN"></script>
</head>
<body class="bg-black text-white min-h-screen p-4 sm:p-8 relative overflow-x-hidden">

  <div class="fixed inset-0 pointer-events-none -z-10 overflow-hidden">
    <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[600px] h-[600px] bg-amber-500/10 rounded-full blur-[140px]"></div>
    <div class="absolute -bottom-40 -left-20 w-[500px] h-[500px] bg-amber-600/10 rounded-full blur-[120px]"></div>
    <div class="absolute top-1/3 -right-20 w-[400px] h-[400px] bg-zinc-800/20 rounded-full blur-[100px]"></div>
  </div>

  <div class="max-w-5xl mx-auto space-y-8 relative z-10">
    <div class="text-center">
      <span class="text-xs font-semibold text-amber-400 uppercase tracking-widest">Paso 2</span>
      <h1 class="text-3xl font-black text-white mt-1">Selecciona tu(s) Mesa(s) en el Croquis</h1>
      <p class="text-zinc-500 text-xs mt-2">Puedes seleccionar más de una mesa. Vuelve a hacer clic para deseleccionarla. Las mesas en negro ya están reservadas.</p>
    </div>

    @if ($errors->any())
      <div class="bg-red-950/60 border border-red-500/50 text-red-300 text-sm rounded-xl p-4">
        <ul class="list-disc list-inside space-y-1">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form id="form-reserva" action="{{ route('reserva.procesar') }}" method="POST" class="space-y-8">
      @csrf

      <div class="bg-amber-500/10 border border-amber-500/40 text-amber-300 text-sm rounded-xl p-4 text-center font-semibold">
        El precio es solo de consumo.
      </div>

      <div class="bg-zinc-900/80 backdrop-blur-md border border-zinc-800 rounded-2xl p-6 shadow-2xl">

        <div class="flex justify-center gap-4 mb-6">
          <button type="button" id="btn-piso1" onclick="switchPiso(1)" class="px-6 py-2.5 rounded-xl font-bold text-sm bg-amber-500 text-black shadow-lg transition-all">
            Planta Baja
          </button>
          <button type="button" id="btn-piso2" onclick="switchPiso(2)" class="px-6 py-2.5 rounded-xl font-bold text-sm bg-zinc-800 text-zinc-400 hover:text-white transition-all border border-zinc-700">
            Segundo Piso
          </button>
        </div>

        <div id="vista-piso1" class="bg-zinc-950/90 border border-zinc-800 rounded-xl p-4 sm:p-8 overflow-x-auto">

          <div class="w-32 h-10 mx-auto bg-zinc-800 border border-blue-500/50 rounded flex items-center justify-center text-xs font-bold text-blue-400 mb-6">
            BARRA
          </div>
          <div class="w-full max-w-md mx-auto h-12 bg-amber-950/40 border border-amber-500/50 rounded-lg flex items-center justify-center text-sm font-black text-amber-300 mb-8">
            ESCENARIO
          </div>

          <div class="max-w-4xl mx-auto grid grid-cols-12 gap-3 items-stretch min-w-[700px]">

            <div class="col-span-4 grid grid-cols-2 gap-3">
              <div class="flex flex-col justify-between gap-2">
                <div class="flex flex-col gap-2">
                  @foreach(['L1', 'L2', 'L3'] as $codigo)
                    @include('partials.mesa-boton', ['codigo' => $codigo, 'zona' => 'VIP Exterior Izq', 'mesas' => $mesas, 'mesasReservadasIds' => $mesasReservadasIds])
                  @endforeach
                </div>

                <div class="mt-4 flex flex-col gap-2">
                  @foreach(['L4', 'L5', 'L6', 'L7', 'L8'] as $codigo)
                    @include('partials.mesa-boton', ['codigo' => $codigo, 'zona' => 'VIP Exterior Izq', 'mesas' => $mesas, 'mesasReservadasIds' => $mesasReservadasIds])
                  @endforeach
                </div>
              </div>

              <div class="flex flex-col justify-between py-2">
                <div class="flex flex-col gap-2">
                  @foreach(['L9', 'L10', 'L11', 'L12'] as $codigo)
                    @include('partials.mesa-boton', ['codigo' => $codigo, 'zona' => 'VIP Pista Izq', 'mesas' => $mesas, 'mesasReservadasIds' => $mesasReservadasIds])
                  @endforeach
                </div>

                <div class="flex flex-col gap-2">
                  @foreach(['L13', 'L14', 'L15', 'L16'] as $codigo)
                    @include('partials.mesa-boton', ['codigo' => $codigo, 'zona' => 'VIP Pista Izq', 'mesas' => $mesas, 'mesasReservadasIds' => $mesasReservadasIds])
                  @endforeach
                </div>
              </div>
            </div>

            <div class="col-span-4 border border-amber-500/40 rounded-3xl p-4 flex flex-col justify-between items-center relative min-h-[400px]">
              <div class="w-full flex-1 flex items-center justify-center gap-3 my-auto">
                <!-- Mesas redondas: lado izquierdo de la pista -->
                <div class="flex flex-col gap-2">
                  @foreach(['R1', 'R2', 'R3', 'R4', 'R5', 'R6'] as $codigo)
                    @include('partials.mesa-boton-redonda', ['codigo' => $codigo, 'zona' => 'VIP Pista Izq', 'mesas' => $mesas, 'mesasReservadasIds' => $mesasReservadasIds])
                  @endforeach
                </div>

                <div class="w-28 h-40 border border-amber-500/60 bg-amber-950/20 rounded-lg flex items-center justify-center flex-shrink-0">
                  <span class="text-xs font-bold text-amber-400 tracking-wider uppercase">Pista</span>
                </div>

                <!-- Mesas redondas: lado derecho de la pista -->
                <div class="flex flex-col gap-2">
                  @foreach(['R7', 'R8', 'R9', 'R10', 'R11', 'R12'] as $codigo)
                    @include('partials.mesa-boton-redonda', ['codigo' => $codigo, 'zona' => 'VIP Pista Der', 'mesas' => $mesas, 'mesasReservadasIds' => $mesasReservadasIds])
                  @endforeach
                </div>
              </div>
              <div class="w-full flex justify-between px-2">
                <div class="w-8 h-6 border border-amber-500/50 rounded"></div>
                <div class="w-8 h-6 border border-amber-500/50 rounded"></div>
              </div>
            </div>

            <div class="col-span-4 grid grid-cols-2 gap-3">
              <div class="flex flex-col justify-between py-2">
                <div class="flex flex-col gap-2 -mt-4 transform -rotate-12">
                  @foreach(['D1', 'D2'] as $codigo)
                    @include('partials.mesa-boton', ['codigo' => $codigo, 'zona' => 'VIP Superior Der', 'mesas' => $mesas, 'mesasReservadasIds' => $mesasReservadasIds])
                  @endforeach
                </div>
              </div>

              <div class="flex flex-col justify-center py-2">
                @foreach(['R13'] as $codigo)
                  @include('partials.mesa-boton', ['codigo' => $codigo, 'zona' => 'VIP Exterior Der', 'mesas' => $mesas, 'mesasReservadasIds' => $mesasReservadasIds])
                @endforeach
              </div>
            </div>

          </div>
        </div>

        <div id="vista-piso2" class="bg-zinc-950/90 border border-zinc-800 rounded-xl p-4 sm:p-8 overflow-x-auto hidden">
          <h3 class="text-xs font-bold text-amber-400 uppercase text-center mb-6 tracking-widest">Plano del Segundo Piso</h3>

          <div class="max-w-4xl mx-auto flex flex-col justify-between min-w-[700px] min-h-[500px]">

            <div class="flex-1 flex gap-6">
              <div class="flex-1 border border-amber-500/40 rounded-3xl p-4 flex flex-col justify-between items-center relative min-h-[400px]">
                <div class="w-full flex-1 flex items-center justify-center my-auto">
                  <div class="w-28 h-40 border border-amber-500/60 bg-amber-950/20 rounded-lg flex items-center justify-center">
                    <span class="text-xs font-bold text-amber-400 tracking-wider uppercase">Pista</span>
                  </div>
                </div>
                <div class="w-full flex justify-between px-2">
                  <div class="w-8 h-6 border border-amber-500/50 rounded"></div>
                  <div class="w-8 h-6 border border-amber-500/50 rounded"></div>
                </div>
              </div>

              <div class="w-36 flex flex-col gap-1.5 justify-between py-1">
                @foreach(range(1, 16) as $i)
                  @php($codigo = 'F' . $i)
                  @include('partials.mesa-boton', ['codigo' => $codigo, 'zona' => '2do Piso - Pared', 'mesas' => $mesas, 'mesasReservadasIds' => $mesasReservadasIds])
                @endforeach
              </div>
            </div>

            <div class="pt-6 border-t border-zinc-800/60 mt-4">
              <div class="grid grid-cols-4 gap-4 max-w-xl mx-auto">
                @foreach(range(1, 4) as $i)
                  @php($codigo = 'A' . $i)
                  @include('partials.mesa-boton', ['codigo' => $codigo, 'zona' => '2do Piso - Centro', 'mesas' => $mesas, 'mesasReservadasIds' => $mesasReservadasIds])
                @endforeach
              </div>
            </div>

          </div>
        </div>

        <div id="resumen_mesa" class="mt-6 bg-amber-500/10 border border-amber-500/40 rounded-xl p-4 hidden">
          <div class="flex justify-between items-center">
            <span class="text-xs text-amber-400 font-bold uppercase">Mesas Seleccionadas:</span>
            <span id="txt_mesa_total" class="text-2xl font-black text-amber-400">$0</span>
          </div>
          <div id="txt_mesa_lista" class="text-sm text-white mt-1 font-semibold"></div>
        </div>
      </div>

      <div class="bg-zinc-900/80 backdrop-blur-md border border-zinc-800 rounded-2xl p-6 space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
          <div class="sm:col-span-3">
            <label class="block text-xs font-bold text-zinc-400 mb-2">NOMBRE COMPLETO</label>
            <input type="text" name="nombre" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-amber-500">
          </div>
          <div>
            <label class="block text-xs font-bold text-zinc-400 mb-2">FECHA</label>
            <input type="date" name="fecha" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-amber-500">
          </div>
          <div class="sm:col-span-2">
            <label class="block text-xs font-bold text-zinc-400 mb-2">PERSONAS</label>
            <select name="personas" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-amber-500">
              <option value="2-4">2 a 4 Personas</option>
              <option value="5-8">5 a 8 Personas</option>
            </select>
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-zinc-400 mb-2">MÉTODO DE PAGO</label>
          <div class="grid grid-cols-2 gap-4">
            <label class="border border-zinc-800 bg-zinc-950 rounded-xl p-3 cursor-pointer flex items-center gap-2">
              <input type="radio" name="metodo_pago_ui" value="tarjeta" checked onchange="cambiarMetodoPago('tarjeta')" class="accent-amber-500">
              <span class="text-xs font-medium">Tarjeta</span>
            </label>
            <label class="border border-zinc-800 bg-zinc-950 rounded-xl p-3 cursor-pointer flex items-center gap-2">
              <input type="radio" name="metodo_pago_ui" value="paypal" onchange="cambiarMetodoPago('paypal')" class="accent-amber-500">
              <span class="text-xs font-medium">PayPal</span>
            </label>
          </div>
        </div>

        <div id="panel-tarjeta" class="space-y-3">
          <div id="stripe-elemento" class="bg-zinc-950 border border-zinc-800 rounded-xl p-4"></div>
          <div id="stripe-error" class="text-xs text-red-400"></div>
          <button type="button" id="btn-pagar-tarjeta" onclick="pagarConTarjeta()" class="w-full bg-amber-500 hover:bg-amber-400 text-black font-extrabold py-4 rounded-xl transition-all shadow-lg shadow-amber-500/10">
            Pagar y Finalizar Reserva
          </button>
        </div>

        <div id="panel-paypal" class="space-y-3 hidden">
          <div id="paypal-boton"></div>
          <div id="paypal-error" class="text-xs text-red-400"></div>
        </div>

        <input type="hidden" name="metodo_pago" id="input_metodo_pago" value="tarjeta">
        <input type="hidden" name="referencia_pago" id="input_referencia_pago">
        <input type="hidden" name="zona" id="input_zona">
        <div id="mesa_ids_hidden"></div>
      </div>

    </form>
  </div>

  <script>
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const STRIPE_PUBLIC_KEY = "{{ config('services.stripe.key') }}";
    const stripe = STRIPE_PUBLIC_KEY ? Stripe(STRIPE_PUBLIC_KEY) : null;
    let stripeElements = null;
    let paypalRendered = false;

    const mesasSeleccionadas = new Map();

    function switchPiso(piso) {
      const v1 = document.getElementById('vista-piso1');
      const v2 = document.getElementById('vista-piso2');
      const b1 = document.getElementById('btn-piso1');
      const b2 = document.getElementById('btn-piso2');

      if (piso === 1) {
        v1.classList.remove('hidden');
        v2.classList.add('hidden');
        b1.className = "px-6 py-2.5 rounded-xl font-bold text-sm bg-amber-500 text-black shadow-lg transition-all";
        b2.className = "px-6 py-2.5 rounded-xl font-bold text-sm bg-zinc-800 text-zinc-400 hover:text-white transition-all border border-zinc-700";
      } else {
        v2.classList.remove('hidden');
        v1.classList.add('hidden');
        b2.className = "px-6 py-2.5 rounded-xl font-bold text-sm bg-amber-500 text-black shadow-lg transition-all";
        b1.className = "px-6 py-2.5 rounded-xl font-bold text-sm bg-zinc-800 text-zinc-400 hover:text-white transition-all border border-zinc-700";
      }
    }

    function toggleMesa(e, id, nombre, precio, zona) {
      const btn = e.currentTarget;

      if (btn.dataset.disponible === '0') {
        return;
      }

      const baseClasses = (btn.dataset.baseClass || 'bg-blue-950/30').split(' ');

      if (mesasSeleccionadas.has(id)) {
        mesasSeleccionadas.delete(id);
        btn.classList.remove('bg-amber-500', 'text-black');
        btn.classList.add(...baseClasses);
      } else {
        mesasSeleccionadas.set(id, { nombre, precio, zona });
        btn.classList.remove(...baseClasses);
        btn.classList.add('bg-amber-500', 'text-black');
      }

      actualizarResumen();
    }

    function actualizarResumen() {
      const resumen = document.getElementById('resumen_mesa');
      const lista = document.getElementById('txt_mesa_lista');
      const total = document.getElementById('txt_mesa_total');
      const hiddenContainer = document.getElementById('mesa_ids_hidden');

      hiddenContainer.innerHTML = '';
      let suma = 0;
      const nombres = [];
      let zona = '';

      mesasSeleccionadas.forEach((datos, id) => {
        suma += datos.precio;
        nombres.push(datos.nombre);
        zona = datos.zona;

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'mesa_ids[]';
        input.value = id;
        hiddenContainer.appendChild(input);
      });

      document.getElementById('input_zona').value = zona;

      if (mesasSeleccionadas.size > 0) {
        resumen.classList.remove('hidden');
        lista.textContent = nombres.join(', ');
        total.textContent = '$' + suma.toFixed(2);
      } else {
        resumen.classList.add('hidden');
      }

      prepararPagoSegunTotal(suma);
    }

    function cambiarMetodoPago(metodo) {
      document.getElementById('input_metodo_pago').value = metodo;

      const panelTarjeta = document.getElementById('panel-tarjeta');
      const panelPaypal = document.getElementById('panel-paypal');

      if (metodo === 'tarjeta') {
        panelTarjeta.classList.remove('hidden');
        panelPaypal.classList.add('hidden');
      } else {
        panelTarjeta.classList.add('hidden');
        panelPaypal.classList.remove('hidden');
        renderizarBotonPaypal();
      }
    }

    async function prepararPagoSegunTotal(total) {
      if (total <= 0 || !stripe) {
        return;
      }

      if (document.getElementById('input_metodo_pago').value !== 'tarjeta') {
        return;
      }

      try {
        const mesaIds = Array.from(mesasSeleccionadas.keys());

        const respuesta = await fetch('{{ route('pago.stripe.intento') }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json',
          },
          body: JSON.stringify({ mesa_ids: mesaIds }),
        });

        const datos = await respuesta.json();

        if (!datos.success) {
          document.getElementById('stripe-error').textContent = datos.message || 'No se pudo iniciar el pago.';
          return;
        }

        stripeElements = stripe.elements({ clientSecret: datos.client_secret });
        const paymentElement = stripeElements.create('payment');
        document.getElementById('stripe-elemento').innerHTML = '';
        paymentElement.mount('#stripe-elemento');
      } catch (error) {
        document.getElementById('stripe-error').textContent = 'No se pudo conectar con el procesador de pagos.';
      }
    }

    async function pagarConTarjeta() {
      if (mesasSeleccionadas.size === 0) {
        alert('Selecciona al menos una mesa antes de continuar.');
        return;
      }

      if (!stripe || !stripeElements) {
        document.getElementById('stripe-error').textContent = 'El pago con tarjeta no está disponible en este momento.';
        return;
      }

      const boton = document.getElementById('btn-pagar-tarjeta');
      boton.disabled = true;
      boton.textContent = 'Procesando...';

      const { error, paymentIntent } = await stripe.confirmPayment({
        elements: stripeElements,
        redirect: 'if_required',
      });

      if (error) {
        document.getElementById('stripe-error').textContent = error.message || 'No se pudo procesar el pago.';
        boton.disabled = false;
        boton.textContent = 'Pagar y Finalizar Reserva';
        return;
      }

      document.getElementById('input_referencia_pago').value = paymentIntent.id;
      document.getElementById('form-reserva').submit();
    }

    function renderizarBotonPaypal() {
      if (paypalRendered || typeof paypal === 'undefined') {
        return;
      }
      paypalRendered = true;

      paypal.Buttons({
        createOrder: async function () {
          const mesaIds = Array.from(mesasSeleccionadas.keys());

          const respuesta = await fetch('{{ route('pago.paypal.orden') }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': CSRF_TOKEN,
              'Accept': 'application/json',
            },
            body: JSON.stringify({ mesa_ids: mesaIds }),
          });

          const datos = await respuesta.json();

          if (!datos.success) {
            document.getElementById('paypal-error').textContent = datos.message || 'No se pudo iniciar el pago con PayPal.';
            throw new Error(datos.message);
          }

          return datos.orden_id;
        },
        onApprove: async function (data) {
          const mesaIds = Array.from(mesasSeleccionadas.keys());

          const respuesta = await fetch('{{ route('pago.paypal.capturar') }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': CSRF_TOKEN,
              'Accept': 'application/json',
            },
            body: JSON.stringify({ orden_id: data.orderID, mesa_ids: mesaIds }),
          });

          const datos = await respuesta.json();

          if (!datos.success) {
            document.getElementById('paypal-error').textContent = datos.message || 'No se pudo confirmar el pago.';
            return;
          }

          document.getElementById('input_metodo_pago').value = 'paypal';
          document.getElementById('input_referencia_pago').value = data.orderID;
          document.getElementById('form-reserva').submit();
        },
        onError: function () {
          document.getElementById('paypal-error').textContent = 'Ocurrió un error con PayPal. Intenta de nuevo.';
        },
      }).render('#paypal-boton');
    }

    document.getElementById('form-reserva').addEventListener('submit', function (e) {
      if (mesasSeleccionadas.size === 0) {
        e.preventDefault();
        alert('Por favor selecciona al menos una mesa en el mapa antes de continuar.');
        return false;
      }

      if (!document.getElementById('input_referencia_pago').value) {
        e.preventDefault();
        alert('Completa el pago antes de finalizar la reserva.');
        return false;
      }
    });
  </script>

</body>
</html>
