<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BoletoCover;
use App\Models\Reserva;
use App\Services\QrCodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EscanerController extends Controller
{
    public function index()
    {
        return view('admin.escaner');
    }

    public function verificar(Request $request, QrCodeService $qrCodeService): JsonResponse
    {
        $validado = $request->validate([
            'contenido_qr' => 'required|string|max:2000',
        ]);

        $payload = $qrCodeService->decodificarPayload($validado['contenido_qr']);

        if (! $payload) {
            return response()->json([
                'success' => false,
                'message' => 'El código QR no tiene un formato válido.',
            ], 422);
        }

        if ($payload['tipo'] === 'cover') {
            return $this->verificarCover($payload, $qrCodeService, $request);
        }

        return $this->verificarMesa($payload, $qrCodeService, $request);
    }

    protected function verificarMesa(array $payload, QrCodeService $qrCodeService, Request $request): JsonResponse
    {
        $reserva = Reserva::where('codigo_reserva', $payload['codigo'])
            ->with('mesas')
            ->first();

        if (! $reserva) {
            return response()->json(['success' => false, 'message' => 'Esta reserva de mesa no existe en la base de datos.'], 404);
        }

        if (! $qrCodeService->verificarFirma('mesa', $reserva->codigo_reserva, $payload['firma'])) {
            return response()->json(['success' => false, 'message' => 'El código QR de mesa no es auténtico.'], 422);
        }

        if ($reserva->estaEscaneada()) {
            return response()->json([
                'success' => false,
                'message' => 'QR ya utilizado: esta reserva de mesa ya fue aprobada el '.$reserva->escaneada_at->format('d/m/Y H:i').'. No puede aprobarse dos veces.',
            ], 409);
        }

        if ($reserva->estado === 'cancelada') {
            return response()->json(['success' => false, 'message' => 'Esta reserva fue cancelada y no es válida.'], 409);
        }

        $reserva->escaneada_at = now();
        $reserva->escaneada_por = $request->user()->id;
        $reserva->estado = 'escaneada';
        $reserva->save();

        return response()->json([
            'success' => true,
            'message' => '¡Reserva de mesa aprobada! Mesa(s): '.$reserva->mesa_id,
            'tipo' => 'mesa',
            'detalle' => [
                'codigo' => $reserva->codigo_reserva,
                'nombre' => $reserva->nombre,
                'mesas' => $reserva->mesas->pluck('numero'),
                'fecha' => $reserva->fecha->format('d/m/Y'),
            ],
        ]);
    }

    protected function verificarCover(array $payload, QrCodeService $qrCodeService, Request $request): JsonResponse
    {
        $boleto = BoletoCover::where('codigo_boleto', $payload['codigo'])->first();

        if (! $boleto) {
            return response()->json(['success' => false, 'message' => 'Este boleto de cover no existe en la base de datos.'], 404);
        }

        if (! $qrCodeService->verificarFirma('cover', $boleto->codigo_boleto, $payload['firma'])) {
            return response()->json(['success' => false, 'message' => 'El código QR de cover no es auténtico.'], 422);
        }

        if ($boleto->estaEscaneada()) {
            return response()->json([
                'success' => false,
                'message' => 'QR ya utilizado: este boleto de cover ya fue aprobado el '.$boleto->escaneada_at->format('d/m/Y H:i').'. No puede aprobarse dos veces.',
            ], 409);
        }

        if ($boleto->estado === 'cancelado') {
            return response()->json(['success' => false, 'message' => 'Este boleto fue cancelado y no es válido.'], 409);
        }

        $boleto->escaneada_at = now();
        $boleto->escaneada_por = $request->user()->id;
        $boleto->estado = 'escaneado';
        $boleto->save();

        return response()->json([
            'success' => true,
            'message' => '¡Cover aprobado! '.$boleto->nombre,
            'tipo' => 'cover',
            'detalle' => [
                'codigo' => $boleto->codigo_boleto,
                'nombre' => $boleto->nombre,
                'fecha' => $boleto->fecha->format('d/m/Y'),
            ],
        ]);
    }
}
