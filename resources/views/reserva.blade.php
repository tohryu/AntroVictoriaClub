<!DOCTYPE html>
<html lang="es" class="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Victoria Luxury Club - Reservaciones</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white antialiased font-sans relative overflow-x-hidden min-h-screen flex flex-col items-center justify-center p-4">

  <div class="fixed inset-0 pointer-events-none z-0">
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-amber-500/10 rounded-full blur-[160px]"></div>
  </div>

  <div class="relative z-10 text-center space-y-6 max-w-xl">
    <span class="text-xs font-semibold text-amber-400 uppercase tracking-widest">Experiencia Exclusiva</span>
    <h1 class="text-4xl sm:text-6xl font-black text-white tracking-tight">Victoria Luxury Club</h1>
    <p class="text-zinc-400 text-sm sm:text-base">Asegura tu lugar en las mejores zonas VIP, pista principal o el segundo piso.</p>

    <button onclick="openModal()" class="inline-flex items-center gap-3 bg-gradient-to-r from-amber-500 via-yellow-500 to-amber-600 hover:from-amber-400 hover:to-yellow-500 text-black font-extrabold text-lg px-8 py-4 rounded-xl shadow-2xl shadow-amber-500/20 transition-all transform hover:-translate-y-1">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
      </svg>
      Reservar Mesa
    </button>
  </div>

  <div id="modalReserva" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 hidden">
    <div onclick="closeModal()" class="fixed inset-0 bg-black/80 backdrop-blur-md transition-opacity"></div>
    <div class="relative w-full max-w-5xl bg-zinc-900 border border-zinc-800 rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col z-10">

      <div class="p-6 border-b border-zinc-800 flex justify-between items-center bg-zinc-950/50">
        <div>
          <span class="text-xs font-bold text-amber-400 uppercase tracking-widest">Paso 1 de 2</span>
          <h2 class="text-2xl font-black text-white">Selecciona tu Mesa y Datos</h2>
        </div>
        <button onclick="closeModal()" class="text-zinc-400 hover:text-white p-2 rounded-lg bg-zinc-800/50 hover:bg-zinc-800 transition-colors">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <div class="p-6 overflow-y-auto space-y-8">

        <form action="{{ route('mesas.procesar') }}" method="POST" class="space-y-8">
          @csrf

          <div class="bg-zinc-950/60 border border-zinc-800/80 rounded-xl p-6">

            <div class="flex flex-wrap items-center justify-center gap-6 mb-6 text-xs text-zinc-400 border-b border-zinc-800 pb-4">
              <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded bg-amber-500/20 border border-amber-500"></span>
                <span>Disponible</span>
              </div>
              <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded bg-amber-500 text-black font-bold flex items-center justify-center text-[10px]">✓</span>
                <span>Seleccionada</span>
              </div>
              <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded bg-zinc-800 border border-zinc-700 opacity-50"></span>
                <span>Ocupada</span>
              </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">

              <div class="lg:col-span-3 bg-zinc-900/50 border border-zinc-800/80 rounded-xl p-6 overflow-x-auto min-w-[300px]">

                <div class="w-32 h-10 mx-auto bg-zinc-800 border border-blue-500/50 rounded flex items-center justify-center text-xs font-bold text-blue-400 tracking-wider mb-6">
                  BARRA
                </div>

                <div class="w-full max-w-md mx-auto h-12 bg-amber-950/40 border border-amber-500/50 rounded-lg flex items-center justify-center text-sm font-black text-amber-300 tracking-widest mb-8 uppercase">
                  ESCENARIO
                </div>

                <div class="grid grid-cols-3 gap-4 items-center max-w-xl mx-auto">

                  <div class="space-y-3 flex flex-col items-end">
                    <button type="button" onclick="selectMesa('Mesa L1', '$13,000', 'VIP Lateral Izq')" class="mesa-btn w-20 h-10 rounded border border-amber-500/40 bg-zinc-950 hover:border-amber-400 text-xs font-bold transition-all flex flex-col items-center justify-center">
                      <span>L1</span>
                      <span class="text-[9px] text-amber-400">$13k</span>
                    </button>
                    <button type="button" onclick="selectMesa('Mesa L2', '$12,000', 'VIP Lateral Izq')" class="mesa-btn w-20 h-10 rounded border border-amber-500/40 bg-zinc-950 hover:border-amber-400 text-xs font-bold transition-all flex flex-col items-center justify-center">
                      <span>L2</span>
                      <span class="text-[9px] text-amber-400">$12k</span>
                    </button>
                    <button type="button" onclick="selectMesa('Mesa L3', '$10,000', 'VIP Lateral Izq')" class="mesa-btn w-20 h-10 rounded border border-amber-500/40 bg-zinc-950 hover:border-amber-400 text-xs font-bold transition-all flex flex-col items-center justify-center">
                      <span>L3</span>
                      <span class="text-[9px] text-amber-400">$10k</span>
                    </button>
                  </div>

                  <div class="bg-amber-500/5 border border-amber-500/20 rounded-2xl py-12 text-center flex flex-col items-center justify-center">
                    <div class="border border-amber-500/40 px-4 py-8 rounded-xl bg-black/60">
                      <span class="block text-base font-black tracking-widest text-amber-400 rotate-90 sm:rotate-0">PISTA</span>
                    </div>
                  </div>

                  <div class="space-y-3 flex flex-col items-start">
                    <button type="button" onclick="selectMesa('Mesa R1', '$15,000', 'VIP Pista')" class="mesa-btn w-20 h-10 rounded border border-amber-500/40 bg-zinc-950 hover:border-amber-400 text-xs font-bold transition-all flex flex-col items-center justify-center">
                      <span>R1</span>
                      <span class="text-[9px] text-amber-400">$15k</span>
                    </button>
                    <button type="button" onclick="selectMesa('Mesa R2', '$15,000', 'VIP Pista')" class="mesa-btn w-20 h-10 rounded border border-amber-500/40 bg-zinc-950 hover:border-amber-400 text-xs font-bold transition-all flex flex-col items-center justify-center">
                      <span>R2</span>
                      <span class="text-[9px] text-amber-400">$15k</span>
                    </button>
                    <button type="button" onclick="selectMesa('Mesa R3', '$10,000', 'VIP Pista')" class="mesa-btn w-20 h-10 rounded border border-amber-500/40 bg-zinc-950 hover:border-amber-400 text-xs font-bold transition-all flex flex-col items-center justify-center">
                      <span>R3</span>
                      <span class="text-[9px] text-amber-400">$10k</span>
                    </button>
                  </div>

                </div>

                <div class="mt-8 text-center">
                  <span class="inline-block border-t-2 border-zinc-700 pt-2 text-xs font-bold tracking-widest text-zinc-500 uppercase">
                    ENTRADA PRINCIPAL
                  </span>
                </div>

              </div>

              <div class="bg-zinc-900/50 border border-zinc-800/80 rounded-xl p-4">
                <div class="border-b border-zinc-800 pb-2 mb-4 text-center">
                  <h3 class="text-xs font-bold text-amber-400 uppercase tracking-wider">SEGUNDO PISO</h3>
                  <p class="text-[10px] text-zinc-500">Consumo $3,000 - $5,000</p>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-1 gap-2 max-h-80 overflow-y-auto pr-1">
                  <button type="button" onclick="selectMesa('Piso 2 - M1', '$3,000', 'Segundo Piso')" class="mesa-btn w-full p-2 rounded border border-amber-500/30 bg-zinc-950 hover:border-amber-400 text-xs flex justify-between items-center transition-all">
                    <span>Mesa 201</span>
                    <span class="text-amber-400 font-bold">$3,000</span>
                  </button>
                  <button type="button" onclick="selectMesa('Piso 2 - M2', '$3,000', 'Segundo Piso')" class="mesa-btn w-full p-2 rounded border border-amber-500/30 bg-zinc-950 hover:border-amber-400 text-xs flex justify-between items-center transition-all">
                    <span>Mesa 202</span>
                    <span class="text-amber-400 font-bold">$3,000</span>
                  </button>
                  <button type="button" onclick="selectMesa('Piso 2 - M3', '$5,000', 'Segundo Piso')" class="mesa-btn w-full p-2 rounded border border-amber-500/30 bg-zinc-950 hover:border-amber-400 text-xs flex justify-between items-center transition-all">
                    <span>Mesa 203 VIP</span>
                    <span class="text-amber-400 font-bold">$5,000</span>
                  </button>
                </div>
              </div>

            </div>

            <input type="hidden" name="mesa_id" id="input_mesa_id" required>
            <input type="hidden" name="zona" id="input_zona" required>

            <div id="resumen_mesa" class="mt-6 bg-amber-500/10 border border-amber-500/40 rounded-xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4 hidden">
              <div>
                <span class="text-xs text-amber-400 font-bold uppercase">Mesa Seleccionada:</span>
                <h4 id="txt_mesa_nombre" class="text-lg font-black text-white">---</h4>
                <p id="txt_mesa_zona" class="text-xs text-zinc-400">---</p>
              </div>
              <div class="text-right">
                <span class="text-xs text-zinc-400 block">Consumo Mínimo:</span>
                <span id="txt_mesa_precio" class="text-2xl font-black text-amber-400">$0</span>
              </div>
            </div>

          </div>

          <div class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
              <div class="sm:col-span-3">
                <label class="block text-xs font-bold uppercase text-zinc-400 mb-2">Nombre Completo</label>
                <input type="text" name="nombre" required placeholder="Tu nombre" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-500 text-sm">
              </div>

              <div>
                <label class="block text-xs font-bold uppercase text-zinc-400 mb-2">Fecha</label>
                <input type="date" name="fecha" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-500 text-sm">
              </div>

              <div class="sm:col-span-2">
                <label class="block text-xs font-bold uppercase text-zinc-400 mb-2">Invitados</label>
                <select name="personas" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-500 text-sm">
                  <option value="2-4">2 a 4 Personas</option>
                  <option value="5-8">5 a 8 Personas</option>
                  <option value="8+">8+ Personas (VIP)</option>
                </select>
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold uppercase text-zinc-400 mb-2">Método de Pago</label>
              <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <label class="border border-zinc-800 bg-zinc-950 rounded-xl p-4 cursor-pointer hover:border-amber-500/50 flex items-center gap-3">
                  <input type="radio" name="metodo_pago" value="tarjeta" checked class="accent-amber-500">
                  <span class="text-sm font-medium">Tarjeta Cr/Déb</span>
                </label>
                <label class="border border-zinc-800 bg-zinc-950 rounded-xl p-4 cursor-pointer hover:border-amber-500/50 flex items-center gap-3">
                  <input type="radio" name="metodo_pago" value="paypal" class="accent-amber-500">
                  <span class="text-sm font-medium">PayPal</span>
                </label>
                <label class="border border-zinc-800 bg-zinc-950 rounded-xl p-4 cursor-pointer hover:border-amber-500/50 flex items-center gap-3">
                  <input type="radio" name="metodo_pago" value="transferencia" class="accent-amber-500">
                  <span class="text-sm font-medium">Transferencia</span>
                </label>
              </div>
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-amber-500 via-yellow-500 to-amber-600 text-black font-extrabold py-4 rounded-xl shadow-xl hover:opacity-95 transition-all text-base">
              Proceder al Pago y Generar Código QR
            </button>
          </div>

        </form>

      </div>

    </div>
  </div>

  <script>
    function openModal() {
      document.getElementById('modalReserva').classList.remove('hidden');
      document.body.classList.add('overflow-hidden');
    }

    function closeModal() {
      document.getElementById('modalReserva').classList.add('hidden');
      document.body.classList.remove('overflow-hidden');
    }

    function selectMesa(nombre, precio, zona) {
      document.querySelectorAll('.mesa-btn').forEach(btn => {
        btn.classList.remove('bg-amber-500', 'text-black', 'border-amber-400');
        btn.classList.add('bg-zinc-950');
      });

      event.currentTarget.classList.remove('bg-zinc-950');
      event.currentTarget.classList.add('bg-amber-500', 'text-black', 'border-amber-400');

      document.getElementById('input_mesa_id').value = nombre;
      document.getElementById('input_zona').value = zona;

      document.getElementById('txt_mesa_nombre').innerText = nombre;
      document.getElementById('txt_mesa_zona').innerText = 'Zona: ' + zona;
      document.getElementById('txt_mesa_precio').innerText = precio;

      document.getElementById('resumen_mesa').classList.remove('hidden');
    }
  </script>

</body>
</html>