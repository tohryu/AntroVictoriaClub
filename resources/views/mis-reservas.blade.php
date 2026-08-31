<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Reservas - Victoria Club</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-2 text-yellow-500">Mis Reservas</h1>
        <p class="text-zinc-500 text-xs mb-6">Cuando el administrador escanea tu QR en la entrada, la reserva desaparece automáticamente de esta lista.</p>

        @if($reservas->isEmpty())
            <p class="text-gray-400">No hay reservas activas por el momento.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse border border-gray-800">
                    <thead>
                        <tr class="bg-gray-900 border-b border-gray-800 text-yellow-500">
                            <th class="p-3">Código</th>
                            <th class="p-3">Nombre</th>
                            <th class="p-3">Fecha</th>
                            <th class="p-3">Mesa(s)</th>
                            <th class="p-3">Zona</th>
                            <th class="p-3">Total (MXN)</th>
                            <th class="p-3">Estado</th>
                            <th class="p-3">Ticket</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reservas as $reserva)
                            <tr class="border-b border-gray-800 hover:bg-gray-900">
                                <td class="p-3 font-mono text-yellow-400">{{ $reserva->codigo_reserva }}</td>
                                <td class="p-3">{{ $reserva->nombre }}</td>
                                <td class="p-3">{{ $reserva->fecha->format('d/m/Y') }}</td>
                                <td class="p-3">{{ $reserva->mesas->pluck('numero')->implode(', ') ?: $reserva->mesa_id }}</td>
                                <td class="p-3">{{ $reserva->zona ?? 'General' }}</td>
                                <td class="p-3">${{ number_format((float) $reserva->precio, 2) }}</td>
                                <td class="p-3">
                                    <span class="px-2 py-1 text-xs rounded bg-green-900 text-green-300">
                                        {{ ucfirst($reserva->estado) }}
                                    </span>
                                </td>
                                <td class="p-3">
                                    <a href="{{ route('reservas.ticket', $reserva->codigo_reserva) }}" class="text-amber-400 hover:text-amber-300 text-xs font-bold underline">
                                        Descargar PDF
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="mt-6">
            <a href="{{ url('/') }}" class="text-yellow-500 underline">← Volver al inicio</a>
        </div>
    </div>
</body>
</html>
