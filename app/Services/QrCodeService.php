<?php

namespace App\Services;

use App\Models\BoletoCover;
use App\Models\Reserva;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;

class QrCodeService
{
    public function firmar(string $tipo, string $codigo): string
    {
        return hash_hmac('sha256', $tipo.'|'.$codigo, config('app.key'));
    }

    public function verificarFirma(string $tipo, string $codigo, string $firma): bool
    {
        return hash_equals($this->firmar($tipo, $codigo), $firma);
    }

    protected function generarPng(string $tipo, string $codigo, string $rutaRelativa): string
    {
        $firma = $this->firmar($tipo, $codigo);

        $payload = json_encode([
            'tipo' => $tipo,
            'codigo' => $codigo,
            'firma' => $firma,
        ]);

        Storage::disk('public')->makeDirectory(dirname($rutaRelativa));

        $qrCode = new QrCode(
            data: $payload,
            size: 320,
            margin: 10,
        );

        $writer = new PngWriter();
        $resultado = $writer->write($qrCode);
        $resultado->saveToFile(Storage::disk('public')->path($rutaRelativa));

        return $firma;
    }

    public function generarParaReserva(Reserva $reserva): Reserva
    {
        $rutaRelativa = 'qrcodes/mesa-'.$reserva->codigo_reserva.'.png';

        $firma = $this->generarPng('mesa', $reserva->codigo_reserva, $rutaRelativa);

        $reserva->qr_path = $rutaRelativa;
        $reserva->qr_firma = $firma;
        $reserva->save();

        return $reserva;
    }

    public function generarParaBoletoCover(BoletoCover $boleto): BoletoCover
    {
        $rutaRelativa = 'qrcodes/cover-'.$boleto->codigo_boleto.'.png';

        $firma = $this->generarPng('cover', $boleto->codigo_boleto, $rutaRelativa);

        $boleto->qr_path = $rutaRelativa;
        $boleto->qr_firma = $firma;
        $boleto->save();

        return $boleto;
    }

    public function decodificarPayload(string $contenido): ?array
    {
        $datos = json_decode($contenido, true);

        if (! is_array($datos) || empty($datos['codigo']) || empty($datos['firma']) || empty($datos['tipo'])) {
            return null;
        }

        return $datos;
    }
}
