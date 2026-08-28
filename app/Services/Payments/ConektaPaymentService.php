<?php

namespace App\Services\Payments;

use Illuminate\Support\Facades\Http;

class ConektaPaymentService
{
    protected string $baseUrl = 'https://api.conekta.io';

    protected string $apiVersion;

    protected string $privateKey;

    public function __construct()
    {
        $this->privateKey = (string) config('services.conekta.private_key');
        $this->apiVersion = (string) config('services.conekta.api_version', '2.3.0');
    }

    protected function cliente()
    {
        if ($this->privateKey === '') {
            throw new PaymentException('Conekta no está configurado (falta la llave privada).');
        }

        return Http::withToken($this->privateKey)
            ->withHeaders([
                'Accept' => 'application/vnd.conekta-v'.$this->apiVersion.'+json',
                'Content-Type' => 'application/json',
                'Accept-Language' => 'es',
            ]);
    }

    /**
     * Crea una orden en Conekta con un Checkout embebido (tipo "Integration").
     * La respuesta trae checkout.id, que el frontend usa como checkoutRequestId
     * para inicializar el widget de pago de Conekta.
     */
    public function crearOrdenCheckout(float $monto, string $nombre, string $email, string $moneda = 'MXN'): array
    {
        $montoCentavos = (int) round($monto * 100);

        $respuesta = $this->cliente()->post($this->baseUrl.'/orders', [
            'checkout' => [
                'type' => 'Integration',
                'name' => 'Victoria Luxury Club',
            ],
            'currency' => $moneda,
            'customer_info' => [
                'name' => $nombre,
                'email' => $email,
            ],
            'line_items' => [
                [
                    'name' => 'Reserva Victoria Club',
                    'unit_price' => $montoCentavos,
                    'quantity' => 1,
                ],
            ],
        ]);

        if ($respuesta->failed()) {
            $mensaje = $respuesta->json('details.0.message')
                ?? $respuesta->json('message')
                ?? 'No se pudo iniciar el pago con Conekta.';

            throw new PaymentException($mensaje);
        }

        return $respuesta->json();
    }

    /**
     * Vuelve a consultar la orden en Conekta (nunca confía solo en lo que
     * mande el navegador) y valida que el monto y la moneda pagados
     * coincidan exactamente con lo que se le va a cobrar al cliente.
     *
     * Nota: Conekta recomienda confirmar el pago vía webhook (evento
     * "order.paid") como mecanismo definitivo, ya que el estado puede
     * cambiar de forma asíncrona. Esta verificación síncrona contra su
     * API es una capa de seguridad adicional, pero si en el futuro se
     * agrega el webhook, es la fuente de verdad más confiable.
     */
    public function verificarPagado(string $ordenId, float $montoEsperado, string $moneda = 'MXN'): array
    {
        $respuesta = $this->cliente()->get($this->baseUrl.'/orders/'.$ordenId);

        if ($respuesta->failed()) {
            throw new PaymentException('No se pudo verificar el pago con Conekta.');
        }

        $orden = $respuesta->json();

        if (($orden['payment_status'] ?? null) !== 'paid') {
            throw new PaymentException('El pago con tarjeta no se ha completado.');
        }

        $montoEsperadoCentavos = (int) round($montoEsperado * 100);
        $montoPagado = (int) ($orden['amount'] ?? 0);
        $monedaPagada = $orden['currency'] ?? '';

        if ($montoPagado !== $montoEsperadoCentavos || strtoupper($monedaPagada) !== strtoupper($moneda)) {
            throw new PaymentException('El monto pagado no coincide con el total de la reserva.');
        }

        return $orden;
    }
}

