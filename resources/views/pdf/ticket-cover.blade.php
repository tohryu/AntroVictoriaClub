<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 0; }
        body { font-family: 'Helvetica', sans-serif; color: #f2e6c9; background: #0a0908; margin: 0; padding: 0; }

        .ticket { background: #0a0908; width: 100%; box-sizing: border-box; }

        .borde-ext { border: 1.5px solid #7a5c1e; margin: 8px; border-radius: 10px; overflow: hidden; }
        .borde-int { border: 1px solid #4a3814; margin: 3px; border-radius: 7px; overflow: hidden; }

        .barra-top { height: 5px; background: #c9a24b; }

        .marca-wrap { text-align: center; padding: 16px 18px 4px; }
        .marca { font-size: 8px; letter-spacing: 4px; color: #c9a24b; text-transform: uppercase; }
        .marca-sub { font-size: 7px; letter-spacing: 2px; color: #6b5a35; margin-top: 2px; text-transform: uppercase; }

        .pill-wrap { text-align: center; padding: 10px 0 6px; }
        .pill { display: inline-block; background: #c9a24b; color: #0a0908; font-size: 8.5px; font-weight: bold; letter-spacing: 2px; padding: 4px 14px; border-radius: 20px; text-transform: uppercase; }

        .evento-wrap { text-align: center; padding: 6px 20px 14px; }
        .evento-titulo { font-size: 21px; font-weight: bold; color: #f7ecd0; line-height: 1.2; }
        .evento-subtitulo { font-size: 9px; color: #9c8c6a; margin-top: 4px; letter-spacing: 1px; }

        .divisor { border-top: 1px dashed #4a3814; margin: 0 20px; }

        table.datos { width: 100%; border-collapse: collapse; font-size: 9.5px; margin: 14px 0; padding: 0 20px; }
        table.datos td { padding: 7px 20px; border-bottom: 1px solid #211a0d; }
        table.datos td.label { color: #8a7a52; text-transform: uppercase; font-size: 7.5px; letter-spacing: 1.2px; width: 45%; }
        table.datos td.valor { font-weight: bold; text-align: right; color: #f2e6c9; font-size: 10.5px; }
        .valor-oro { color: #d9b45f !important; }

        .perforacion { text-align: center; padding: 4px 0; }
        .perforacion .linea { border-top: 1.5px dashed #6b5a35; margin: 0 14px; position: relative; top: 0; }
        .perforacion .tijera { display: inline-block; background: #0a0908; color: #6b5a35; font-size: 9px; padding: 0 6px; position: relative; top: -7px; }

        .qr-wrap { text-align: center; padding: 16px 0 6px; }
        .qr-marco { display: inline-block; background: #f7ecd0; padding: 10px; border-radius: 10px; }
        .qr-marco img { width: 118px; height: 118px; display: block; }
        .codigo { font-family: 'Courier New', monospace; font-size: 10.5px; font-weight: bold; color: #d9b45f; letter-spacing: 1.5px; margin-top: 8px; }
        .uso-unico { font-size: 7px; color: #6b5a35; letter-spacing: 1.5px; text-transform: uppercase; margin-top: 3px; }

        .nota { margin: 16px 18px 6px; padding: 10px 12px; background: #14100a; border: 1px solid #2a2313; border-radius: 8px; font-size: 7.5px; color: #a8946a; line-height: 1.5; text-align: center; }

        .footer-wrap { text-align: center; padding: 10px 0 16px; }
        .footer-marca { font-size: 7px; letter-spacing: 3px; color: #4a3814; text-transform: uppercase; }
    </style>
</head>
<body>
    @php
        $evento = \App\Models\Evento::where('fecha', $boleto->fecha)->where('activo', true)->first();
        $tituloEvento = $evento->titulo ?? 'Victoria Luxury Club';
        $subtituloEvento = $evento->subtitulo ?? null;
    @endphp
    <div class="ticket">
        <div class="borde-ext">
            <div class="borde-int">
                <div class="barra-top"></div>

                <div class="marca-wrap">
                    <div class="marca">Victoria Luxury Club</div>
                    <div class="marca-sub">Ticket Electrónico</div>
                </div>

                <div class="pill-wrap">
                    <span class="pill">Boleto de Cover</span>
                </div>

                <div class="evento-wrap">
                    <div class="evento-titulo">{{ $tituloEvento }}</div>
                    @if($subtituloEvento)
                        <div class="evento-subtitulo">{{ $subtituloEvento }}</div>
                    @endif
                </div>

                <div class="divisor"></div>

                <table class="datos">
                    <tr><td class="label">Titular</td><td class="valor">{{ $boleto->nombre }}</td></tr>
                    <tr><td class="label">Fecha</td><td class="valor">{{ $boleto->fecha->format('d/m/Y') }}</td></tr>
                    <tr><td class="label">Precio</td><td class="valor">${{ number_format((float) $boleto->precio_unitario, 2) }} MXN</td></tr>
                    <tr><td class="label">Total Pagado</td><td class="valor valor-oro">${{ number_format((float) $boleto->precio_total, 2) }} MXN</td></tr>
                    <tr><td class="label">Método de Pago</td><td class="valor">{{ ucfirst($boleto->metodo_pago) }}</td></tr>
                </table>

                <div class="perforacion">
                    <div class="linea"><span class="tijera">&#9986;</span></div>
                </div>

                <div class="qr-wrap">
                    @if($qrAbsolutePath && file_exists($qrAbsolutePath))
                        <div class="qr-marco"><img src="{{ $qrAbsolutePath }}" alt="QR"></div>
                    @endif
                    <div class="codigo">{{ $boleto->codigo_boleto }}</div>
                    <div class="uso-unico">Uso único · Preséntalo en recepción</div>
                </div>

                <div class="nota">
                    Este boleto corresponde al acceso al club y no se aplica como consumo.
                </div>

                <div class="footer-wrap">
                    <div class="footer-marca">✦ Victoria Luxury Club ✦</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
