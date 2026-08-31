<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #f2e6c9; background: #0a0a0a; margin: 0; padding: 0; }
        .ticket { background: #0a0a0a; border: 2px solid #c9a24b; border-radius: 14px; margin: 14px; padding: 0; overflow: hidden; }
        .header { background: #14100a; padding: 18px 20px 14px; border-bottom: 1px solid #3a2f14; }
        .marca { font-size: 9px; letter-spacing: 3px; color: #c9a24b; text-transform: uppercase; margin-bottom: 4px; }
        .evento-titulo { font-size: 24px; font-weight: bold; color: #f2e6c9; line-height: 1.15; }
        .evento-subtitulo { font-size: 10px; color: #9c8c6a; margin-top: 3px; }
        .cuerpo { padding: 16px 20px; }
        .fila-superior { width: 100%; }
        .fila-superior td { vertical-align: top; }
        .qr-caja { width: 110px; text-align: center; }
        .qr-caja img { width: 100px; height: 100px; background: #f2e6c9; padding: 6px; border-radius: 8px; }
        .codigo { font-family: monospace; font-size: 10px; color: #c9a24b; margin-top: 4px; letter-spacing: 1px; }
        table.datos { width: 100%; border-collapse: collapse; font-size: 10px; margin-top: 4px; }
        table.datos td { padding: 5px 4px; border-bottom: 1px solid #2a2313; }
        table.datos td.label { color: #9c8c6a; text-transform: uppercase; font-size: 8px; letter-spacing: 1px; }
        table.datos td.valor { font-weight: bold; text-align: right; color: #f2e6c9; }
        .precio-total { color: #c9a24b !important; font-size: 13px; }
        .franja { background: #c9a24b; color: #0a0a0a; text-align: center; font-weight: bold; font-size: 10px; letter-spacing: 2px; padding: 6px; text-transform: uppercase; }
        .nota { margin: 14px 20px 18px; padding: 10px 12px; background: #1a1408; border: 1px solid #3a2f14; border-radius: 8px; font-size: 8.5px; color: #c9a24b; line-height: 1.4; }
    </style>
</head>
<body>
    @php
        $evento = \App\Models\Evento::where('fecha', $boleto->fecha)->where('activo', true)->first();
        $tituloEvento = $evento->titulo ?? 'Victoria Luxury Club';
        $subtituloEvento = $evento->subtitulo ?? null;
    @endphp
    <div class="ticket">
        <div class="header">
            <div class="marca">Victoria Luxury Club</div>
            <div class="evento-titulo">{{ $tituloEvento }}</div>
            @if($subtituloEvento)
                <div class="evento-subtitulo">{{ $subtituloEvento }}</div>
            @endif
        </div>

        <div class="franja">Boleto Electrónico de Cover</div>

        <div class="cuerpo">
            <table class="fila-superior">
                <tr>
                    <td>
                        <table class="datos">
                            <tr><td class="label">Titular</td><td class="valor">{{ $boleto->nombre }}</td></tr>
                            <tr><td class="label">Fecha</td><td class="valor">{{ $boleto->fecha->format('d/m/Y') }}</td></tr>
                            <tr><td class="label">Precio</td><td class="valor">${{ number_format((float) $boleto->precio_unitario, 2) }} MXN</td></tr>
                            <tr><td class="label">Total Pagado</td><td class="valor precio-total">${{ number_format((float) $boleto->precio_total, 2) }} MXN</td></tr>
                            <tr><td class="label">Método de Pago</td><td class="valor">{{ ucfirst($boleto->metodo_pago) }}</td></tr>
                        </table>
                    </td>
                    <td class="qr-caja">
                        @if($qrAbsolutePath && file_exists($qrAbsolutePath))
                            <img src="{{ $qrAbsolutePath }}" alt="QR">
                        @endif
                        <div class="codigo">{{ $boleto->codigo_boleto }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="nota">
            Este es tu boleto de cover (acceso al club), no se aplica como consumo. Presenta este ticket y su código QR en recepción; el personal lo validará escaneándolo. Este boleto es de uso único.
        </div>
    </div>
</body>
</html>
