<!DOCTYPE html>
<html lang="es" class="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Victoria Luxury Club - Cartelera de Eventos</title>
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <link rel="preconnect" href="https://cdn.tailwindcss.com">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white antialiased selection:bg-amber-500 selection:text-black font-sans relative overflow-x-hidden">

  <canvas id="fluidCanvas" class="fixed inset-0 pointer-events-none z-0"></canvas>

  @if (session('error'))
    <div class="relative z-50 max-w-2xl mx-auto mt-4 px-4">
      <div class="bg-red-950/80 border border-red-500/50 text-red-300 text-sm rounded-xl p-4 text-center">
        {{ session('error') }}
      </div>
    </div>
  @endif

  @if (session('success'))
    <div class="relative z-50 max-w-2xl mx-auto mt-4 px-4">
      <div class="bg-emerald-950/80 border border-emerald-500/50 text-emerald-300 text-sm rounded-xl p-4 text-center">
        {{ session('success') }}
      </div>
    </div>
  @endif

  <div class="relative z-10">

    <nav class="sticky top-0 z-50 backdrop-blur-md bg-black/80 border-b border-zinc-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 min-h-20 flex flex-wrap items-center justify-between gap-y-3 gap-x-4">

        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-full border border-amber-500/40 bg-zinc-900 p-1 flex items-center justify-center overflow-hidden shrink-0 shadow-lg shadow-amber-500/10">
            <img src="{{ asset('images/logo.png') }}" alt="Logo Victoria Luxury Club" class="w-full h-full object-contain rounded-full">
          </div>
          <span class="text-lg sm:text-2xl font-black tracking-widest text-transparent bg-clip-text bg-gradient-to-r from-amber-200 via-yellow-400 to-amber-600 uppercase">
            Victoria <span class="font-light text-white">Luxury Club</span>
          </span>
        </div>

        <div class="hidden md:flex items-center gap-8 font-medium text-sm text-zinc-400">
          <a href="#eventos" class="hover:text-amber-400 transition-colors">Eventos</a>
          @auth
            <a href="{{ route('reserva.mapa') }}" class="hover:text-amber-400 transition-colors">Mesas</a>
            <a href="{{ route('cover.formulario') }}" class="hover:text-amber-400 transition-colors">Cover</a>
          @else
            <a href="{{ route('login.google') }}" class="hover:text-amber-400 transition-colors">Mesas</a>
            <a href="{{ route('login.google') }}" class="hover:text-amber-400 transition-colors">Cover</a>
          @endauth
        </div>

        <div class="flex flex-wrap items-center gap-2 sm:gap-3 w-full sm:w-auto justify-end">
          @auth

            @if(auth()->user()->es_admin)
              <a href="{{ route('admin.promociones.index') }}" class="inline-flex items-center whitespace-nowrap bg-amber-500/10 text-amber-400 border border-amber-500/40 hover:bg-amber-500 hover:text-black font-bold text-[11px] sm:text-xs px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-full transition-all">
                 Panel Admin
              </a>
              <a href="{{ route('admin.mesas.index') }}" class="inline-flex items-center whitespace-nowrap bg-amber-500/10 text-amber-400 border border-amber-500/40 hover:bg-amber-500 hover:text-black font-bold text-[11px] sm:text-xs px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-full transition-all">
                 Modificar Precios
              </a>
              <a href="{{ route('admin.escaner.index') }}" class="inline-flex items-center whitespace-nowrap bg-amber-500/10 text-amber-400 border border-amber-500/40 hover:bg-amber-500 hover:text-black font-bold text-[11px] sm:text-xs px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-full transition-all">
                 Escanear QR
              </a>
            @endif

            <a href="{{ route('reservas.mis_reservas') }}" class="inline-block bg-gradient-to-r from-amber-500 via-yellow-500 to-amber-600 hover:from-amber-400 hover:to-yellow-500 text-black font-bold text-xs sm:text-sm px-4 sm:px-5 py-2 sm:py-2.5 rounded-full shadow-lg shadow-amber-500/20 transition-all transform hover:scale-105 whitespace-nowrap">
              Mis Reservas
            </a>
          @else

            <a href="{{ route('login.google') }}" class="inline-flex items-center gap-2 bg-zinc-900 border border-amber-500/30 hover:border-amber-400 text-amber-400 font-bold text-xs sm:text-sm px-4 py-2 rounded-full transition-all whitespace-nowrap">
              <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24">
                <path d="M12.545,10.239v3.821h5.445c-0.712,2.315-2.647,3.972-5.445,3.972c-3.332,0-6.033-2.701-6.033-6.032s2.701-6.032,6.033-6.032c1.498,0,2.866,0.549,3.921,1.453l2.814-2.814C17.503,2.988,15.139,2,12.545,2C7.021,2,2.545,6.477,2.545,12s4.476,10,10,10c5.772,0,9.654-4.058,9.654-9.825c0-0.718-0.076-1.39-0.203-2.031H12.545z"/>
              </svg>
              Iniciar Sesión con Google
            </a>
          @endauth
        </div>

      </div>
    </nav>

    <header class="relative min-h-[360px] flex items-center justify-center overflow-hidden border-b border-zinc-800">
      <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=1920')] bg-cover bg-center opacity-30 mix-blend-luminosity"></div>
      <div class="absolute inset-0 bg-gradient-to-t from-black via-black/70 to-transparent"></div>

      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 text-center z-10">
        <span class="inline-block bg-amber-950/60 text-amber-300 border border-amber-500/40 text-[10px] sm:text-xs uppercase font-bold tracking-widest px-3 py-1 rounded-full mb-3 backdrop-blur-md">
          ★Club Nocturno★
        </span>
        <h1 class="text-3xl sm:text-5xl font-black tracking-tight text-white mb-2 uppercase">
          Victoria <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-200 via-yellow-400 to-amber-500">Luxury</span>
        </h1>
        <p class="text-zinc-400 text-sm sm:text-base max-w-xl mx-auto mb-1 font-light">
          Eventos • De jueves a Sábado • Abiertos desde las 10:00 PM
        </p>

        <p class="text-zinc-500 text-xs sm:text-sm max-w-xl mx-auto mb-6 font-light">
          📍 Boulevard Europa #12, Puebla, Mexico, 72160
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center items-center">
          @auth
            <a href="{{ route('reserva.mapa') }}" class="w-full sm:w-auto bg-zinc-900/90 hover:bg-zinc-800 text-amber-400 border border-amber-500/30 font-bold text-sm px-6 py-3 rounded-xl backdrop-blur-md transition-all flex items-center justify-center">
              Reservar Mesa
            </a>
            <a href="{{ route('cover.formulario') }}" class="w-full sm:w-auto bg-zinc-900/90 hover:bg-zinc-800 text-amber-400 border border-amber-500/30 font-bold text-sm px-6 py-3 rounded-xl backdrop-blur-md transition-all flex items-center justify-center">
              Comprar Cover
            </a>
          @else
            <a href="{{ route('login.google') }}" class="w-full sm:w-auto bg-zinc-900/90 hover:bg-zinc-800 text-amber-400/90 hover:text-amber-300 border border-amber-500/30 font-bold text-sm px-6 py-3 rounded-xl backdrop-blur-md transition-all flex items-center justify-center gap-2">
              Inicia sesión para Reservar
            </a>
          @endauth
        </div>
      </div>
    </header>

    <main id="eventos" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
        <div>
          <h2 class="text-3xl font-extrabold tracking-tight uppercase">Próximos Eventos</h2>
          <p class="text-zinc-400 text-sm mt-1">Asegura tus botellas y mesa antes de que se agoten</p>
        </div>

        <div class="flex flex-wrap gap-2">
          <button class="bg-gradient-to-r from-amber-500 to-yellow-500 text-black font-bold text-sm px-4 py-2 rounded-lg transition-all">Todos</button>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($eventos as $evento)
          @php
            $fecha = \Carbon\Carbon::parse($evento->fecha)->locale('es');
            $esPasado = $fecha->lt(now()->startOfDay());
          @endphp
          <article class="bg-zinc-900/60 border border-zinc-800 rounded-2xl overflow-hidden hover:border-amber-500/50 transition-all duration-300 group flex flex-col backdrop-blur-sm {{ $esPasado ? 'grayscale hover:grayscale-0' : '' }}">
            <div class="relative h-64 overflow-hidden bg-zinc-950 flex items-center justify-center">
              <img
                src="{{ Storage::url($evento->imagen) }}"
                alt="{{ $evento->titulo }}"
                class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-500"
                @if($loop->first) loading="eager" fetchpriority="high" @else loading="lazy" @endif
                decoding="async"
                width="576"
                height="1024"
              >
              <div class="absolute inset-0 bg-gradient-to-t from-zinc-900 via-transparent to-transparent pointer-events-none"></div>

              <div class="absolute top-4 left-4 bg-black/80 backdrop-blur-md border border-amber-500/40 text-center px-3 py-1.5 rounded-xl">
                <span class="block text-xs uppercase text-amber-400 font-bold">
                  {{ $fecha->shortDayName }}
                </span>
                <span class="block text-lg font-black leading-none text-white">
                  {{ $fecha->format('d') }}
                </span>
              </div>
            </div>

            <div class="p-6 flex-1 flex flex-col justify-between">
              <div>
                @if($evento->subtitulo)
                  <span class="text-xs font-semibold text-amber-400 uppercase tracking-wider">
                    {{ $evento->subtitulo }}
                  </span>
                @endif
                <h3 class="text-xl font-bold mt-1 text-white group-hover:text-amber-300 transition-colors">
                  {{ $evento->titulo }}
                </h3>
                @if($evento->descripcion)
                  <p class="text-zinc-400 text-sm mt-2 line-clamp-2">
                    {{ $evento->descripcion }}
                  </p>
                @endif
              </div>

              <div class="mt-6 pt-4 border-t border-zinc-800/80 flex items-center justify-between">
                <div>
                  <span class="block text-xs text-zinc-500 uppercase">Reserva desde</span>
                  <span class="text-lg font-extrabold text-amber-400">
                    {{ $evento->precio_etiqueta }}
                  </span>
                </div>
                @if($esPasado)
                  <a href="{{ Storage::url($evento->imagen) }}" target="_blank" rel="noopener noreferrer" class="bg-zinc-800 hover:bg-amber-500 hover:text-black text-white text-sm font-bold px-4 py-2 rounded-lg transition-colors">
                    Ver Galería
                  </a>
                @elseif($loop->first)
                  @auth
                    <a href="{{ route('reserva.mapa') }}" class="bg-zinc-800 hover:bg-amber-500 hover:text-black text-white text-sm font-bold px-4 py-2 rounded-lg transition-colors">
                      Reservar Mesa
                    </a>
                  @else
                    <a href="{{ route('login.google') }}" class="bg-zinc-800 hover:bg-amber-500 hover:text-black text-white text-sm font-bold px-4 py-2 rounded-lg transition-colors">
                      Reservar Mesa
                    </a>
                  @endauth
                @else
                  <span class="bg-zinc-900 text-zinc-500 text-sm font-bold px-4 py-2 rounded-lg border border-zinc-800 cursor-not-allowed select-none">
                    Próximamente
                  </span>
                @endif
              </div>
            </div>
          </article>
        @empty
          <p class="text-zinc-500 col-span-3 text-center py-8">No hay próximos eventos programados por el momento.</p>
        @endforelse
      </div>
    </main>

    <footer class="border-t border-zinc-800 bg-zinc-950/80 text-zinc-500 py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center gap-6 text-center text-sm">

        @php
          $instagramUrl = $instagramUrl ?? 'https://www.instagram.com/victoria_luxurygroup?igsi=ZDNlZDc0MzIxNw==';
          $tiktokUrl    = $tiktokUrl ?? 'https://www.tiktok.com/@luxuri564?is_from_webapp=1&sender_device=pc';
          $facebookUrl  = $facebookUrl ?? 'https://www.facebook.com/share/184yeRxTyd/';
        @endphp

        <div class="flex items-center gap-6">

          <a href="{{ $instagramUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="w-10 h-10 rounded-full border border-zinc-800 bg-zinc-900 flex items-center justify-center text-zinc-400 hover:text-amber-400 hover:border-amber-500/50 hover:bg-amber-500/10 transition-all transform hover:scale-110">
            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
              <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
            </svg>
          </a>

          <a href="{{ $tiktokUrl }}" target="_blank" rel="noopener noreferrer" aria-label="TikTok" class="w-10 h-10 rounded-full border border-zinc-800 bg-zinc-900 flex items-center justify-center text-zinc-400 hover:text-amber-400 hover:border-amber-500/50 hover:bg-amber-500/10 transition-all transform hover:scale-110">
            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
              <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.98-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.29-2.58.7-5.16 2.62-6.84 1.42-1.25 3.32-1.89 5.21-1.8.1.02.2.03.3.05v4.06c-.84-.11-1.71.07-2.43.51-.83.5-1.39 1.37-1.52 2.33-.19 1.12.18 2.28.95 3.09.8.84 1.99 1.25 3.14 1.08.97-.13 1.86-.71 2.38-1.54.5-.78.71-1.72.69-2.65V.02z"/>
            </svg>
          </a>

          <a href="{{ $facebookUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="w-10 h-10 rounded-full border border-zinc-800 bg-zinc-900 flex items-center justify-center text-zinc-400 hover:text-amber-400 hover:border-amber-500/50 hover:bg-amber-500/10 transition-all transform hover:scale-110">
            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
              <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
          </a>
        </div>

        <p>© 2026 Victoria Luxury Club. Todos los derechos reservados. Reservados los derechos de admisión +18.</p>
      </div>
    </footer>

  </div>

  <script>
    const canvas = document.getElementById('fluidCanvas');
    const ctx = canvas.getContext('2d');

    let particles = [];
    let time = 0;

    function resizeCanvas() {
      canvas.width = window.innerWidth;
      canvas.height = window.innerHeight;
    }

    function initParticles() {
      particles = [];
      const numParticles = 120;
      for (let i = 0; i < numParticles; i++) {
        particles.push({
          x: Math.random() * canvas.width,
          y: Math.random() * canvas.height,
          radius: Math.random() * 1.8 + 0.4,
          vx: (Math.random() - 0.5) * 0.4,
          vy: -Math.random() * 0.5 - 0.2,
          alpha: Math.random() * 0.8 + 0.2
        });
      }
    }

    function drawDividedBackground() {
      const w = canvas.width;
      const h = canvas.height;

      ctx.fillStyle = '#000000';
      ctx.fillRect(0, 0, w, h);

      ctx.save();
      ctx.beginPath();

      ctx.moveTo(0, h * 0.55);

      for (let x = 0; x <= w; x += 20) {
        const waveY = Math.sin(x * 0.003 + time * 0.01) * 35 +
                      Math.cos(x * 0.0015 + time * 0.008) * 20 + (h * 0.55);
        ctx.lineTo(x, waveY);
      }

      ctx.lineTo(w, h);
      ctx.lineTo(0, h);
      ctx.closePath();

      const goldGradient = ctx.createLinearGradient(0, h * 0.5, w, h);
      goldGradient.addColorStop(0, 'rgba(180, 120, 20, 0.25)');
      goldGradient.addColorStop(0.5, 'rgba(245, 158, 11, 0.18)');
      goldGradient.addColorStop(1, 'rgba(120, 70, 10, 0.35)');

      ctx.fillStyle = goldGradient;
      ctx.fill();

      ctx.strokeStyle = 'rgba(245, 158, 11, 0.4)';
      ctx.lineWidth = 2;
      ctx.stroke();

      ctx.restore();
    }

    function drawGoldParticles() {
      particles.forEach(p => {
        ctx.save();
        ctx.globalAlpha = p.alpha;
        ctx.fillStyle = '#fbbf24';
        ctx.beginPath();
        ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
        ctx.fill();
        ctx.restore();

        p.x += p.vx;
        p.y += p.vy;

        if (p.y < 0) {
          p.y = canvas.height;
          p.x = Math.random() * canvas.width;
        }
        if (p.x < 0) p.x = canvas.width;
        if (p.x > canvas.width) p.x = 0;
      });
    }

    function animate() {
      ctx.clearRect(0, 0, canvas.width, canvas.height);

      drawDividedBackground();
      drawGoldParticles();

      time += 1;
      requestAnimationFrame(animate);
    }

    window.addEventListener('resize', () => {
      resizeCanvas();
      initParticles();
    });

    resizeCanvas();
    initParticles();
    animate();
  </script>

</body>
</html>