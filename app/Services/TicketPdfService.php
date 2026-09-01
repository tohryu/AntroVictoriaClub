<?php

namespace App\Services;

use App\Models\BoletoCover;
use App\Models\Reserva;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class TicketPdfService
{
    public function generarParaReserva(Reserva $reserva): Reserva
    {
        $reserva->loadMissing('mesas');

        $rutaRelativa = 'tickets/mesa-'.$reserva->codigo_reserva.'.pdf';

        Storage::disk('public')->makeDirectory('tickets');

        $pdf = Pdf::loadView('pdf.ticket-mesa', [
            'reserva' => $reserva,
            'qrAbsolutePath' => $reserva->qr_path ? Storage::disk('public')->path($reserva->qr_path) : null,
        ])->setPaper([0, 0, 288, 560], 'portrait');

        $pdf->save(Storage::disk('public')->path($rutaRelativa));

        $reserva->pdf_path = $rutaRelativa;
        $reserva->save();

        return $reserva;
    }

    public function generarParaBoletoCover(BoletoCover $boleto): BoletoCover
    {
        $rutaRelativa = 'tickets/cover-'.$boleto->codigo_boleto.'.pdf';

        Storage::disk('public')->makeDirectory('tickets');

        $pdf = Pdf::loadView('pdf.ticket-cover', [
            'boleto' => $boleto,
            'qrAbsolutePath' => $boleto->qr_path ? Storage::disk('public')->path($boleto->qr_path) : null,
        ])->setPaper([0, 0, 288, 560], 'portrait');

        $pdf->save(Storage::disk('public')->path($rutaRelativa));

        $boleto->pdf_path = $rutaRelativa;
        $boleto->save();

        return $boleto;
    }
}
