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

        .marca-wrap { text-align: center; padding: 16px 18px 2px; }
        .marca { font-size: 11px; letter-spacing: 5px; color: #c9a24b; text-transform: uppercase; font-weight: bold; }
        .marca-sub { font-size: 7px; letter-spacing: 3px; color: #6b5a35; margin-top: 3px; text-transform: uppercase; }

        .evento-wrap { text-align: center; padding: 10px 18px 4px; }
        .evento-titulo { font-size: 24px; font-weight: 900; color: #f7ecd0; line-height: 1.1; text-transform: uppercase; }
        .evento-subtitulo { font-size: 11px; color: #c9a24b; margin-top: 2px; font-style: italic; }

        .fecha-wrap { text-align: center; padding: 6px 18px 4px; }
        .fecha { font-size: 12px; font-weight: bold; color: #f2e6c9; letter-spacing: 1px; }

        .qr-wrap { text-align: center; padding: 14px 0 6px; }
        .qr-marco { display: inline-block; background: #f7ecd0; padding: 10px; border-radius: 10px; }
        .qr-marco img { width: 140px; height: 140px; display: block; }
        .codigo { font-family: 'Courier New', monospace; font-size: 10.5px; font-weight: bold; color: #d9b45f; letter-spacing: 1.5px; margin-top: 8px; }

        .divisor-punteado { border-top: 1.5px dashed #4a3814; margin: 10px 18px 0; }

        table.info { width: 100%; border-collapse: collapse; font-size: 8px; margin-top: 12px; }
        table.info td { padding: 0 8px 12px; text-align: center; }
        table.info .label { color: #8a7a52; text-transform: uppercase; letter-spacing: 1px; font-size: 7px; display: block; padding-bottom: 3px; }
        table.info .valor { color: #f2e6c9; font-weight: bold; font-size: 11px; display: block; }
        table.info .valor-oro { color: #d9b45f; }

        table.info2 { width: 100%; border-collapse: collapse; font-size: 8px; margin-top: 4px; }
        table.info2 td { padding: 0 18px 14px; }
        table.info2 .label { color: #8a7a52; text-transform: uppercase; letter-spacing: 1px; font-size: 7px; padding-bottom: 3px; display: block; }
        table.info2 .valor { color: #f2e6c9; font-weight: bold; font-size: 11px; display: block; }

        .franja-web { background: #14100a; border-top: 1px solid #2a2313; border-bottom: 1px solid #2a2313; text-align: center; padding: 12px 14px; }
        .franja-web .sitio { font-size: 11px; font-weight: bold; color: #d9b45f; letter-spacing: 0.5px; }
        .franja-web .direccion { font-size: 8px; color: #9c8c6a; margin-top: 4px; line-height: 1.4; text-transform: uppercase; letter-spacing: 0.5px; }

        .nota { margin: 14px 18px; padding: 10px 12px; background: #14100a; border: 1px solid #2a2313; border-radius: 8px; font-size: 7.5px; color: #a8946a; line-height: 1.5; text-align: center; }

        .footer-wrap { text-align: center; padding: 4px 0 16px; }
        .footer-marca { font-size: 7px; letter-spacing: 3px; color: #4a3814; text-transform: uppercase; }
    </style>
</head>
<body>
    @php
        $evento = \App\Models\Evento::where('fecha', $reserva->fecha)->where('activo', true)->first();
        $tituloEvento = $evento->titulo ?? 'Reservación de Mesa';
        $subtituloEvento = $evento->subtitulo ?? null;

        $diasSemana = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
        $meses = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $fechaObj = $reserva->fecha;
        $fechaFormateada = strtoupper($diasSemana[$fechaObj->dayOfWeekIso - 1].' '.$fechaObj->day.' DE '.$meses[$fechaObj->month].' '.$fechaObj->year);

        $mesasTexto = $reserva->mesas->pluck('numero')->implode(', ') ?: $reserva->mesa_id;
        $sitio = preg_replace('#^https?://#', '', config('app.url'));
    @endphp
    <div class="ticket">
        <div class="borde-ext">
            <div class="borde-int">
                <div class="barra-top"></div>

                <div class="marca-wrap">
                    <div class="marca">Victoria</div>
                    <div class="marca-sub">Ticket Electrónico</div>
                </div>

                <div class="evento-wrap">
                    <div class="evento-titulo">{{ $tituloEvento }}</div>
                    @if($subtituloEvento)
                        <div class="evento-subtitulo">{{ $subtituloEvento }}</div>
                    @endif
                </div>

                <div class="fecha-wrap">
                    <span class="fecha">{{ $fechaFormateada }}</span>
                </div>

                <div class="qr-wrap">
                    @if($qrAbsolutePath && file_exists($qrAbsolutePath))
                        <div class="qr-marco"><img src="{{ $qrAbsolutePath }}" alt="QR"></div>
                    @endif
                    <div class="codigo">{{ $reserva->codigo_reserva }}</div>
                </div>

                <div class="divisor-punteado"></div>

                <table class="info">
                    <tr>
                        <td><span class="label">Precio</span><span class="valor">${{ number_format((float) $reserva->precio, 2) }}</span></td>
                        <td><span class="label">Cargo Servicio</span><span class="valor">$0.00</span></td>
                        <td><span class="label">Cantidad</span><span class="valor">1</span></td>
                    </tr>
                    <tr>
                        <td><span class="label">Tipo</span><span class="valor valor-oro">Mesa</span></td>
                        <td><span class="label">Zona</span><span class="valor">{{ $reserva->zona }}</span></td>
                        <td><span class="label">Mesa(s)</span><span class="valor">{{ $mesasTexto }}</span></td>
                    </tr>
                </table>

                <table class="info2">
                    <tr>
                        <td><span class="label">Titular</span><span class="valor">{{ $reserva->nombre }}</span></td>
                        <td><span class="label">Método de Pago</span><span class="valor">{{ ucfirst($reserva->metodo_pago) }}</span></td>
                    </tr>
                    <tr>
                        <td><span class="label">Total Pagado</span><span class="valor" style="color:#d9b45f;">${{ number_format((float) $reserva->precio, 2) }} MXN</span></td>
                        <td></td>
                    </tr>
                </table>

                <div class="franja-web">
                    <div class="sitio">www.{{ $sitio }}</div>
                    <div class="direccion">Boulevard Europa #12, Puebla, México, 72160</div>
                </div>

                <div class="nota">
                    El monto pagado por esta mesa se aplica como consumo dentro del club la noche de tu reserva. Este ticket es de uso único, preséntalo en recepción.
                </div>

                <div class="footer-wrap">
                    <div class="footer-marca">✦ Victoria Luxury Club ✦</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
