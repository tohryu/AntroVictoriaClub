<?php

namespace App\Services\Payments;

use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaypalPaymentService
{
    protected PayPalClient $client;

    public function __construct()
    {
        $this->client = new PayPalClient();
        $this->client->setApiCredentials(config('services.paypal'));
        $this->client->getAccessToken();
    }

    public function crearOrden(float $monto, string $moneda = 'MXN'): array
    {
        $respuesta = $this->client->createOrder([
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'amount' => [
                    'currency_code' => $moneda,
                    'value' => number_format($monto, 2, '.', ''),
                ],
            ]],
        ]);

        if (! isset($respuesta['id'])) {
            throw new PaymentException('No se pudo crear la orden de PayPal.');
        }

        return $respuesta;
    }

    public function capturarOrden(string $ordenId, float $montoEsperado, string $moneda = 'MXN'): array
    {
        $respuesta = $this->client->capturePaymentOrder($ordenId);

        $status = $respuesta['status'] ?? null;

        if ($status !== 'COMPLETED') {
            throw new PaymentException('El pago con PayPal no se completó.');
        }

        $captura = $respuesta['purchase_units'][0]['payments']['captures'][0] ?? null;
        $montoPagado = (float) ($captura['amount']['value'] ?? 0);
        $monedaPagada = $captura['amount']['currency_code'] ?? '';

        if (abs($montoPagado - $montoEsperado) > 0.01 || strtoupper($monedaPagada) !== strtoupper($moneda)) {
            throw new PaymentException('El monto pagado en PayPal no coincide con el total de la reserva.');
        }

        return $respuesta;
    }
}
