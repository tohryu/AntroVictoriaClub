<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #111; padding: 20px; }
        .marco { border: 2px solid #b8860b; border-radius: 12px; padding: 24px; }
        .titulo { text-align: center; font-size: 20px; font-weight: bold; color: #b8860b; letter-spacing: 2px; }
        .subtitulo { text-align: center; font-size: 10px; color: #666; margin-bottom: 16px; }
        .qr { text-align: center; margin: 16px 0; }
        .qr img { width: 160px; height: 160px; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; margin-top: 10px; }
        td { padding: 6px 4px; border-bottom: 1px solid #ddd; }
        td.label { color: #888; width: 40%; }
        td.valor { font-weight: bold; text-align: right; }
        .nota { margin-top: 16px; padding: 10px; background: #fdf3d9; border: 1px solid #e0c268; border-radius: 8px; font-size: 9px; color: #7a5c00; }
        .codigo { text-align: center; font-family: monospace; font-size: 13px; font-weight: bold; color: #b8860b; margin-top: 6px; }
    </style>
</head>
<body>
    <div class="marco">
        <div class="titulo">VICTORIA CLUB</div>
        <div class="subtitulo">Boleto Digital de Cover</div>

        <div class="qr">
            @if($qrAbsolutePath && file_exists($qrAbsolutePath))
                <img src="{{ $qrAbsolutePath }}" alt="QR">
            @endif
            <div class="codigo">{{ $boleto->codigo_boleto }}</div>
        </div>

        <table>
            <tr><td class="label">Titular</td><td class="valor">{{ $boleto->nombre }}</td></tr>
            <tr><td class="label">Fecha</td><td class="valor">{{ $boleto->fecha->format('d/m/Y') }}</td></tr>
            <tr><td class="label">Precio</td><td class="valor">${{ number_format((float) $boleto->precio_unitario, 2) }} MXN</td></tr>
            <tr><td class="label">Total Pagado</td><td class="valor">${{ number_format((float) $boleto->precio_total, 2) }} MXN</td></tr>
            <tr><td class="label">Método de Pago</td><td class="valor">{{ ucfirst($boleto->metodo_pago) }}</td></tr>
        </table>

        <div class="nota">
            Este es tu boleto de cover (acceso al club), no se aplica como consumo. Presenta este ticket y su código QR en recepción; el personal lo validará escaneándolo.
        </div>
    </div>
</body>
</html>
