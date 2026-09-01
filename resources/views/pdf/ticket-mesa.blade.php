<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 0; }
        body { font-family: 'Helvetica', sans-serif; color: #1a1a1a; margin: 0; padding: 0; }

        .ticket { width: 100%; box-sizing: border-box; background: #ffffff; }
        .borde-ext { border: 1.5px solid #a3134f; margin: 8px; border-radius: 10px; overflow: hidden; }
        .borde-int { border: 1px solid #d8a3ba; margin: 3px; border-radius: 7px; overflow: hidden; }

        /* Banda clara: header, evento, fecha, QR, tabla de precio/tipo */
        .banda-clara { background: #fce4ec; }

        .marca-wrap { text-align: center; padding: 18px 18px 2px; }
        .marca { font-size: 13px; letter-spacing: 5px; color: #a3134f; text-transform: uppercase; font-weight: 900; }
        .marca-sub { font-size: 7px; letter-spacing: 3px; color: #8a2249; margin-top: 3px; text-transform: uppercase; font-weight: bold; }

        .evento-wrap { text-align: center; padding: 10px 16px 4px; }
        .evento-titulo { font-size: 24px; font-weight: 900; color: #1a1a1a; line-height: 1.1; text-transform: uppercase; }
        .evento-subtitulo { font-size: 12px; color: #a3134f; margin-top: 2px; font-style: italic; font-weight: bold; }

        .fecha-wrap { text-align: center; padding: 6px 18px 4px; }
        .fecha { font-size: 12px; font-weight: bold; color: #1a1a1a; letter-spacing: 1px; }

        .qr-wrap { text-align: center; padding: 14px 0 6px; }
        .qr-marco { display: inline-block; background: #ffffff; padding: 10px; border-radius: 10px; border: 1px solid #f0b8cd; }
        .qr-marco img { width: 140px; height: 140px; display: block; }
        .codigo { font-family: 'Courier New', monospace; font-size: 10.5px; font-weight: bold; color: #a3134f; letter-spacing: 1.5px; margin-top: 8px; }

        .divisor-punteado { border-top: 1.5px dashed #d8a3ba; margin: 10px 18px 0; }

        table.info { width: 100%; border-collapse: collapse; font-size: 8px; margin-top: 12px; }
        table.info td { padding: 0 8px 14px; text-align: center; }
        table.info .label { color: #a3134f; text-transform: uppercase; letter-spacing: 1px; font-size: 7px; display: block; padding-bottom: 3px; font-weight: bold; }
        table.info .valor { color: #1a1a1a; font-weight: 900; font-size: 12px; display: block; }

        /* Banda media: titular / método / total */
        .banda-media { background: #d81b60; padding-top: 2px; }
        table.info2 { width: 100%; border-collapse: collapse; font-size: 8px; }
        table.info2 td { padding: 14px 18px 14px; }
        table.info2 .label { color: #fbdce6; text-transform: uppercase; letter-spacing: 1px; font-size: 7px; padding-bottom: 3px; display: block; font-weight: bold; }
        table.info2 .valor { color: #ffffff; font-weight: 900; font-size: 12px; display: block; }

        /* Banda oscura: sitio, dirección, nota, footer */
        .banda-oscura { background: #7a0e3a; }
        .franja-web { text-align: center; padding: 12px 14px; border-top: 1px solid rgba(255,255,255,0.25); }
        .franja-web .sitio { font-size: 11px; font-weight: 900; color: #ffffff; letter-spacing: 0.5px; }
        .franja-web .direccion { font-size: 8px; color: #f5c3d6; margin-top: 4px; line-height: 1.4; text-transform: uppercase; letter-spacing: 0.5px; font-weight: bold; }

        .nota { margin: 14px 18px; padding: 10px 12px; background: #ffffff; border-radius: 8px; font-size: 7.5px; color: #7a0e3a; line-height: 1.5; text-align: center; font-weight: bold; }

        .footer-wrap { text-align: center; padding: 4px 0 16px; }
        .footer-marca { font-size: 7px; letter-spacing: 3px; color: #ffffff; text-transform: uppercase; font-weight: bold; }
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
    @endphp
    <div class="ticket">
        <div class="borde-ext">
            <div class="borde-int">

                <div class="banda-clara">
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
                            <td><span class="label">Tipo</span><span class="valor">Mesa</span></td>
                            <td><span class="label">Zona</span><span class="valor">{{ $reserva->zona }}</span></td>
                            <td><span class="label">Mesa(s)</span><span class="valor">{{ $mesasTexto }}</span></td>
                        </tr>
                    </table>
                </div>

                <div class="banda-media">
                    <table class="info2">
                        <tr>
                            <td><span class="label">Titular</span><span class="valor">{{ $reserva->nombre }}</span></td>
                            <td><span class="label">Método de Pago</span><span class="valor">{{ ucfirst($reserva->metodo_pago) }}</span></td>
                        </tr>
                        <tr>
                            <td colspan="2"><span class="label">Total Pagado</span><span class="valor">${{ number_format((float) $reserva->precio, 2) }} MXN</span></td>
                        </tr>
                    </table>
                </div>

                <div class="banda-oscura">
                    <div class="franja-web">
                        <div class="sitio">www.victorialuxury.net</div>
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
    </div>
</body>
</html>
