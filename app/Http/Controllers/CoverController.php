<?php

namespace App\Http\Controllers;

use App\Models\BoletoCover;
use App\Models\CoverConfiguracion;
use App\Services\Payments\ConektaPaymentService;
use App\Services\Payments\PaymentException;
use App\Services\Payments\PaypalPaymentService;
use App\Services\QrCodeService;
use App\Services\TicketPdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CoverController extends Controller
{
    public function formulario()
    {
        $precioCover = CoverConfiguracion::precioActual();

        return view('cover', compact('precioCover'));
    }

    public function procesar(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'fecha' => 'required|date|after_or_equal:today',
            'cantidad' => 'required|integer|min:1|max:20',
            'metodo_pago' => 'required|string|in:tarjeta,paypal',
            'referencia_pago' => 'required|string|max:255',
        ]);

        try {
            $boleto = DB::transaction(function () use ($validated, $request) {
                $precioUnitario = CoverConfiguracion::precioActual();

                if ($precioUnitario <= 0) {
                    throw ValidationException::withMessages([
                        'cantidad' => 'El precio del cover todavía no ha sido configurado por el administrador.',
                    ]);
                }

                $total = round($precioUnitario * $validated['cantidad'], 2);

                // ===================================================================
                // 🧪 MODO PRUEBA — verificación de pago DESACTIVADA TEMPORALMENTE
                // ===================================================================
                // Mismo bypass que en ReservaController. Antes de producción:
                // 1) Borra o comenta el bloque "BYPASS TEMPORAL" de abajo.
                // 2) Descomenta el bloque original que sí verifica el pago.
                // -------------------------------------------------------------------
                // if ($validated['metodo_pago'] === 'tarjeta') {
                //     (new ConektaPaymentService())->verificarPagado($validated['referencia_pago'], $total, 'MXN');
                // } else {
                //     (new PaypalPaymentService())->capturarOrden($validated['referencia_pago'], $total, 'MXN');
                // }

                // --- BYPASS TEMPORAL: acepta cualquier referencia_pago sin cobrar ---
                if (empty($validated['referencia_pago'])) {
                    throw ValidationException::withMessages([
                        'pago' => 'Falta la referencia de pago.',
                    ]);
                }
                // ===================================================================

                $codigoBoleto = $this->generarCodigoBoletoUnico();

                return BoletoCover::create([
                    'user_id' => $request->user()->id,
                    'codigo_boleto' => $codigoBoleto,
                    'nombre' => $validated['nombre'],
                    'fecha' => $validated['fecha'],
                    'cantidad' => $validated['cantidad'],
                    'precio_unitario' => $precioUnitario,
                    'precio_total' => $total,
                    'metodo_pago' => $validated['metodo_pago'],
                    'pago_estado' => 'pagado',
                    'pago_referencia' => $validated['referencia_pago'],
                    'estado' => 'confirmado',
                ]);
            });
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (PaymentException $e) {
            return back()->withErrors(['pago' => $e->getMessage()])->withInput();
        } catch (\Throwable $e) {
            Log::error('Error procesando boleto de cover: '.$e->getMessage());

            return back()->withErrors(['pago' => 'Ocurrió un error al procesar tu boleto. Intenta de nuevo.'])->withInput();
        }

        try {
            (new QrCodeService())->generarParaBoletoCover($boleto);
            (new TicketPdfService())->generarParaBoletoCover($boleto);
        } catch (\Throwable $e) {
            Log::error('Error generando QR/PDF del boleto '.$boleto->codigo_boleto.': '.$e->getMessage());
        }

        return redirect()->route('cover.exitoso', $boleto->codigo_boleto);
    }

    public function exitoso(string $codigo, Request $request)
    {
        $boleto = BoletoCover::where('codigo_boleto', $codigo)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return view('cover-exitoso', [
            'boleto' => $boleto,
            'qr_url' => $boleto->qr_path ? Storage::disk('public')->url($boleto->qr_path) : null,
        ]);
    }

    private function generarCodigoBoletoUnico(): string
    {
        do {
            $codigo = 'COVER-'.strtoupper(Str::random(10));
        } while (BoletoCover::where('codigo_boleto', $codigo)->exists());

        return $codigo;
    }

    public function misBoletos(Request $request)
    {
        $boletos = BoletoCover::where('user_id', $request->user()->id)
            ->noEscaneadas()
            ->orderBy('fecha', 'desc')
            ->get();

        return view('mis-boletos-cover', compact('boletos'));
    }

    public function descargarTicket(string $codigo, Request $request)
    {
        $boleto = BoletoCover::where('codigo_boleto', $codigo)->firstOrFail();

        if ($boleto->user_id !== $request->user()->id && ! $request->user()->es_admin) {
            abort(403);
        }

        if (! $boleto->pdf_path || ! Storage::disk('public')->exists($boleto->pdf_path)) {
            abort(404, 'El ticket todavía no está disponible.');
        }

        return Storage::disk('public')->download($boleto->pdf_path, $boleto->codigo_boleto.'.pdf');
    }
}
