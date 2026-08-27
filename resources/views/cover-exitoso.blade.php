<!DOCTYPE html>
<html lang="es" class="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cover Confirmado - Victoria Luxury Club</title>
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white antialiased font-sans relative overflow-x-hidden min-h-screen flex items-center justify-center p-4">

  <div class="fixed inset-0 pointer-events-none z-0">
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-amber-500/10 rounded-full blur-[140px]"></div>
  </div>

  <div class="relative z-10 max-w-md w-full bg-zinc-900/80 border border-zinc-800 rounded-2xl p-8 backdrop-blur-md text-center shadow-2xl">

    <div class="w-16 h-16 bg-amber-500/10 border border-amber-500/30 rounded-full flex items-center justify-center mx-auto mb-4">
      <svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
      </svg>
    </div>

    <span class="text-xs font-bold text-amber-400 uppercase tracking-widest">¡Cover Confirmado!</span>
    <h1 class="text-2xl font-black text-white mt-1">Victoria Luxury Club</h1>
    <p class="text-zinc-400 text-xs mt-1">Presenta este código QR al ingresar en recepción.</p>

    <div class="my-6 p-4 bg-white rounded-xl inline-block border-2 border-amber-500/40 shadow-lg">
      @if($qr_url)
        <img src="{{ $qr_url }}" alt="QR Cover" class="w-44 h-44">
      @else
        <div class="w-44 h-44 flex items-center justify-center text-black text-xs text-center px-2">
          El código QR se está generando, revisa "Mis Boletos" en unos segundos.
        </div>
      @endif
    </div>

    <div class="bg-zinc-950/60 border border-zinc-800 rounded-xl p-4 text-left space-y-2 text-xs mb-4">
      <div class="flex justify-between border-b border-zinc-800/80 pb-2">
        <span class="text-zinc-500">Código:</span>
        <span class="font-mono font-bold text-amber-400">{{ $boleto->codigo_boleto }}</span>
      </div>
      <div class="flex justify-between border-b border-zinc-800/80 pb-2">
        <span class="text-zinc-500">Titular:</span>
        <span class="font-semibold text-white">{{ $boleto->nombre }}</span>
      </div>
      <div class="flex justify-between border-b border-zinc-800/80 pb-2">
        <span class="text-zinc-500">Fecha:</span>
        <span class="font-semibold text-white">{{ $boleto->fecha->format('d/m/Y') }}</span>
      </div>
      <div class="flex justify-between border-b border-zinc-800/80 pb-2">
        <span class="text-zinc-500">Total Pagado:</span>
        <span class="font-semibold text-amber-400">${{ number_format((float) $boleto->precio_total, 2) }}</span>
      </div>
      <div class="flex justify-between">
        <span class="text-zinc-500">Método de Pago:</span>
        <span class="font-semibold text-white uppercase">{{ $boleto->metodo_pago }}</span>
      </div>
    </div>

    <div class="bg-amber-500/10 border border-amber-500/30 rounded-xl p-3 text-[11px] text-amber-300 mb-6 text-left">
      Este cover corresponde al acceso al club y no se aplica como consumo.
    </div>

    <div class="space-y-3">
      <a href="{{ route('cover.ticket', $boleto->codigo_boleto) }}" class="block w-full bg-amber-500 hover:bg-amber-400 text-black font-bold text-sm py-3 rounded-xl transition-colors">
        Descargar Ticket en PDF
      </a>
      <button onclick="window.print()" class="w-full bg-zinc-800 hover:bg-zinc-700 text-white font-bold text-sm py-3 rounded-xl transition-colors">
        Guardar o Imprimir QR
      </button>
      <a href="{{ url('/') }}" class="block text-xs text-zinc-400 hover:text-amber-400 transition-colors">
        Volver a la Página Principal
      </a>
    </div>

  </div>

</body>
</html>
