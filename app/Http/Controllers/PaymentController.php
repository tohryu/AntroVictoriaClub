<?php

namespace App\Http\Controllers;

use App\Models\CoverConfiguracion;
use App\Models\Mesa;
use App\Services\Payments\ConektaPaymentService;
use App\Services\Payments\PaymentException;
use App\Services\Payments\PaypalPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function calcularTotalMesas(Request $request): float
    {
        $validado = $request->validate([
            'mesa_ids' => 'required|array|min:1',
            'mesa_ids.*' => 'integer|exists:mesas,id',
        ]);

        $mesas = Mesa::whereIn('id', $validado['mesa_ids'])->where('disponible', true)->get();

        if ($mesas->count() !== count($validado['mesa_ids'])) {
            abort(422, 'Una o más mesas seleccionadas ya no están disponibles.');
        }

        return (float) $mesas->sum('precio');
    }

    public function calcularTotalCover(Request $request): float
    {
        $validado = $request->validate([
            'cantidad' => 'required|integer|min:1|max:20',
        ]);

        $precio = CoverConfiguracion::precioActual();

        if ($precio <= 0) {
            abort(422, 'El precio del cover todavía no ha sido configurado por el administrador.');
        }

        return round($precio * $validado['cantidad'], 2);
    }

    public function crearOrdenConektaMesas(Request $request): JsonResponse
    {
        return $this->manejarCreacionConekta($request, fn () => $this->calcularTotalMesas($request));
    }

    public function crearOrdenConektaCover(Request $request): JsonResponse
    {
        return $this->manejarCreacionConekta($request, fn () => $this->calcularTotalCover($request));
    }

    protected function manejarCreacionConekta(Request $request, callable $calcularTotal): JsonResponse
    {
        try {
            $total = $calcularTotal();

            $servicio = new ConektaPaymentService();
            $orden = $servicio->crearOrdenCheckout(
                $total,
                (string) $request->user()->name,
                (string) $request->user()->email,
                'MXN'
            );

            return response()->json([
                'success' => true,
                'orden_id' => $orden['id'],
                'checkout_id' => $orden['checkout']['id'] ?? null,
                'total' => $total,
            ]);
        } catch (PaymentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            Log::error('Error creando orden de Conekta: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'No se pudo iniciar el pago con tarjeta.'], 500);
        }
    }

    public function crearOrdenPaypal(Request $request): JsonResponse
    {
        return $this->manejarCreacionPaypal(fn () => $this->calcularTotalMesas($request));
    }

    public function crearOrdenPaypalCover(Request $request): JsonResponse
    {
        return $this->manejarCreacionPaypal(fn () => $this->calcularTotalCover($request));
    }

    protected function manejarCreacionPaypal(callable $calcularTotal): JsonResponse
    {
        try {
            $total = $calcularTotal();

            $servicio = new PaypalPaymentService();
            $orden = $servicio->crearOrden($total);

            return response()->json([
                'success' => true,
                'orden_id' => $orden['id'],
                'total' => $total,
            ]);
        } catch (PaymentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            Log::error('Error creando orden de PayPal: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'No se pudo iniciar el pago con PayPal.'], 500);
        }
    }

    public function capturarOrdenPaypal(Request $request): JsonResponse
    {
        return $this->manejarCapturaPaypal($request, fn () => $this->calcularTotalMesas($request));
    }

    public function capturarOrdenPaypalCover(Request $request): JsonResponse
    {
        return $this->manejarCapturaPaypal($request, fn () => $this->calcularTotalCover($request));
    }

    protected function manejarCapturaPaypal(Request $request, callable $calcularTotal): JsonResponse
    {
        $validado = $request->validate([
            'orden_id' => 'required|string',
        ]);

        try {
            $total = $calcularTotal();

            $servicio = new PaypalPaymentService();
            $captura = $servicio->capturarOrden($validado['orden_id'], $total);

            return response()->json([
                'success' => true,
                'captura' => $captura,
            ]);
        } catch (PaymentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            Log::error('Error capturando orden de PayPal: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'No se pudo confirmar el pago con PayPal.'], 500);
        }
    }
}

