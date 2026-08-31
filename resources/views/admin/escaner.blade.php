<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Escáner de QR - Victoria Club</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
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

    <div class="max-w-2xl mx-auto relative z-10">
        <div class="mb-6 border-b border-gray-800 pb-4">
            <h1 class="text-3xl font-extrabold text-white tracking-wide drop-shadow-md">Escáner de QR</h1>
            <p class="text-gray-400 text-sm mt-1">Escanea el código QR de una reserva de mesa o de un boleto de cover para aprobarlo en la entrada.</p>
        </div>

        <div class="bg-gray-900/70 p-6 rounded-2xl shadow-2xl border border-gray-800 backdrop-blur-md">
            <div id="lector-qr" class="w-full rounded-xl overflow-hidden border border-amber-500/40"></div>

            <div id="resultado-escaneo" class="mt-6 hidden p-4 rounded-xl border text-sm">
                <p id="resultado-titulo" class="font-bold mb-1"></p>
                <p id="resultado-detalle" class="text-xs opacity-80"></p>
            </div>

            <button type="button" onclick="reiniciarEscaneo()" class="mt-4 w-full bg-amber-500 hover:bg-amber-400 text-black font-bold py-2.5 rounded-lg transition text-sm">
                Escanear otro código
            </button>
        </div>

        <a href="{{ url('/') }}" class="block text-center mt-6 text-xs text-amber-400 hover:text-amber-300">← Volver al inicio</a>
    </div>

    <script>
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let escanerActivo = null;
        let procesando = false;

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
            clearTimeout(window.toastTimeout);
            window.toastTimeout = setTimeout(() => {
                toast.classList.add('translate-x-[120%]', 'opacity-0');
            }, 5000);
        }

        function formatearDetalle(detalle, tipo) {
            if (!detalle) {
                return '';
            }

            if (tipo === 'cover') {
                return `Código: ${detalle.codigo} · ${detalle.nombre} · ${detalle.fecha}`;
            }

            const mesas = Array.isArray(detalle.mesas) ? detalle.mesas.join(', ') : '';
            return `Código: ${detalle.codigo} · ${detalle.nombre} · Mesa(s): ${mesas} · ${detalle.fecha}`;
        }

        async function alDetectarQr(contenidoDecodificado) {
            if (procesando) {
                return;
            }
            procesando = true;

            try {
                const respuesta = await fetch('{{ route('admin.escaner.verificar') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ contenido_qr: contenidoDecodificado }),
                });

                const datos = await respuesta.json();

                const resultado = document.getElementById('resultado-escaneo');
                const titulo = document.getElementById('resultado-titulo');
                const detalle = document.getElementById('resultado-detalle');

                resultado.classList.remove('hidden');

                if (datos.success) {
                    resultado.className = 'mt-6 p-4 rounded-xl border text-sm bg-emerald-950/60 border-emerald-500 text-emerald-300';
                    titulo.textContent = datos.message;
                    detalle.textContent = formatearDetalle(datos.detalle, datos.tipo);
                    mostrarToast(datos.message, 'success');
                } else {
                    resultado.className = 'mt-6 p-4 rounded-xl border text-sm bg-red-950/60 border-red-500 text-red-300';
                    titulo.textContent = datos.message || 'No se pudo validar el código QR.';
                    detalle.textContent = '';
                    mostrarToast(datos.message || 'No se pudo validar el código QR.', 'error');
                }

                if (escanerActivo) {
                    await escanerActivo.pause(true);
                }
            } catch (error) {
                mostrarToast('No se pudo conectar con el servidor.', 'error');
            } finally {
                procesando = false;
            }
        }

        function iniciarEscaner() {
            escanerActivo = new Html5Qrcode('lector-qr');
            escanerActivo.start(
                { facingMode: 'environment' },
                { fps: 10, qrbox: { width: 250, height: 250 } },
                alDetectarQr,
                () => {}
            ).catch(() => {
                mostrarToast('No se pudo acceder a la cámara. Revisa los permisos del navegador.', 'error');
            });
        }

        function reiniciarEscaneo() {
            document.getElementById('resultado-escaneo').classList.add('hidden');
            if (escanerActivo) {
                escanerActivo.resume();
            }
        }

        iniciarEscaner();

        const canvas = document.getElementById('starfield');
        const ctx = canvas.getContext('2d');
        let stars = [];
        const numStars = 220;

        function resizeCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }

        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();

        class Star {
            constructor() { this.reset(); }
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
                if (this.alpha <= 0.1 || this.alpha >= 1) this.alphaChange = -this.alphaChange;
                if (this.x < 0 || this.x > canvas.width || this.y < 0 || this.y > canvas.height) this.reset();
            }
            draw() {
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(255, 255, 255, ${this.alpha})`;
                ctx.fill();
            }
        }

        for (let i = 0; i < numStars; i++) stars.push(new Star());

        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            stars.forEach(star => { star.update(); star.draw(); });
            requestAnimationFrame(animate);
        }
        animate();
    </script>
</body>
</html>
