<?php

namespace App\Services\Payments;

use Stripe\PaymentIntent;
use Stripe\Stripe;

class StripePaymentService
{
    public function __construct()
    {
        Stripe::setApiKey((string) config('services.stripe.secret'));
    }

    public function crearIntento(float $monto, string $moneda = 'mxn', array $metadata = []): PaymentIntent
    {
        return PaymentIntent::create([
            'amount' => (int) round($monto * 100),
            'currency' => $moneda,
            'payment_method_types' => ['card'],
            'metadata' => $metadata,
        ]);
    }

    public function verificarPagado(string $paymentIntentId, float $montoEsperado, string $moneda = 'mxn'): PaymentIntent
    {
        $intento = PaymentIntent::retrieve($paymentIntentId);

        if ($intento->status !== 'succeeded') {
            throw new PaymentException('El pago con tarjeta no se ha completado.');
        }

        $montoEsperadoCentavos = (int) round($montoEsperado * 100);

        if ($intento->amount !== $montoEsperadoCentavos || strtolower($intento->currency) !== strtolower($moneda)) {
            throw new PaymentException('El monto pagado no coincide con el total de la reserva.');
        }

        return $intento;
    }
}
