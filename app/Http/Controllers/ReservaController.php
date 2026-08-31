<?php

namespace App\Http\Controllers;

use App\Models\Mesa;
use App\Models\Promocion;
use App\Models\Reserva;
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

class ReservaController extends Controller
{
    public function index()
    {
        $promociones = Promocion::where('activo', true)->get();

        return view('welcome', compact('promociones'));
    }

    public function mapa()
    {
        $mesas = Mesa::all();
        $mesasReservadasIds = Mesa::where('disponible', false)->pluck('id')->toArray();

        return view('reservar-mesa', compact('mesas', 'mesasReservadasIds'));
    }

    public function procesarReserva(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'fecha' => 'required|date|after_or_equal:today',
            'mesa_ids' => 'required|array|min:1',
            'mesa_ids.*' => 'integer|distinct|exists:mesas,id',
            'zona' => 'nullable|string|max:255',
            'metodo_pago' => 'required|string|in:tarjeta,paypal',
            'referencia_pago' => 'required|string|max:255',
        ]);

        try {
            $reserva = DB::transaction(function () use ($validated, $request) {
                $mesas = Mesa::whereIn('id', $validated['mesa_ids'])
                    ->lockForUpdate()
                    ->get();

                if ($mesas->count() !== count($validated['mesa_ids'])) {
                    throw ValidationException::withMessages([
                        'mesa_ids' => 'Una de las mesas seleccionadas ya no existe.',
                    ]);
                }

                $mesaNoDisponible = $mesas->firstWhere('disponible', false);
                if ($mesaNoDisponible) {
                    throw ValidationException::withMessages([
                        'mesa_ids' => 'La mesa '.$mesaNoDisponible->numero.' ya fue reservada por otra persona. Elige otra.',
                    ]);
                }

                $total = (float) $mesas->sum('precio');

                // ===================================================================
                // 🧪 MODO PRUEBA — verificación de pago DESACTIVADA TEMPORALMENTE
                // ===================================================================
                // Se comentó la llamada real a Conekta/PayPal para poder probar
                // el flujo completo (reserva -> QR -> escáner) sin cobrar de verdad.
                //
                // ANTES DE SUBIR A PRODUCCIÓN:
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

                $codigoReserva = $this->generarCodigoReservaUnico();

                $reserva = Reserva::create([
                    'user_id' => $request->user()->id,
                    'codigo_reserva' => $codigoReserva,
                    'nombre' => $validated['nombre'],
                    'fecha' => $validated['fecha'],
                    'mesa_id' => $mesas->pluck('numero')->implode(', '),
                    'zona' => $validated['zona'] ?? 'General',
                    'precio' => $total,
                    'metodo_pago' => $validated['metodo_pago'],
                    'pago_estado' => 'pagado',
                    'pago_referencia' => $validated['referencia_pago'],
                    'estado' => 'confirmada',
                ]);

                $pivotData = $mesas->mapWithKeys(fn (Mesa $mesa) => [
                    $mesa->id => ['precio_al_momento' => $mesa->precio],
                ])->toArray();

                $reserva->mesas()->attach($pivotData);

                Mesa::whereIn('id', $mesas->pluck('id'))->update(['disponible' => false]);

                return $reserva;
            });
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (PaymentException $e) {
            return back()->withErrors(['pago' => $e->getMessage()])->withInput();
        } catch (\Throwable $e) {
            Log::error('Error procesando reserva: '.$e->getMessage());

            return back()->withErrors(['pago' => 'Ocurrió un error al procesar tu reserva. Intenta de nuevo.'])->withInput();
        }

        try {
            (new QrCodeService())->generarParaReserva($reserva);
            (new TicketPdfService())->generarParaReserva($reserva);
        } catch (\Throwable $e) {
            Log::error('Error generando QR/PDF de la reserva '.$reserva->codigo_reserva.': '.$e->getMessage());
        }

        return redirect()->route('reserva.exitosa', $reserva->codigo_reserva);
    }

    public function exitosa(string $codigo, Request $request)
    {
        $reserva = Reserva::where('codigo_reserva', $codigo)
            ->where('user_id', $request->user()->id)
            ->with('mesas')
            ->firstOrFail();

        return view('reserva-exitosa', [
            'codigo_reserva' => $reserva->codigo_reserva,
            'nombre' => $reserva->nombre,
            'fecha' => $reserva->fecha,
            'mesa_id' => $reserva->mesa_id,
            'zona' => $reserva->zona,
            'precio' => $reserva->precio,
            'metodo_pago' => $reserva->metodo_pago,
            'qr_url' => $reserva->qr_path ? Storage::disk('public')->url($reserva->qr_path) : null,
        ]);
    }

    private function generarCodigoReservaUnico(): string
    {
        do {
            $codigo = 'VIC-'.strtoupper(Str::random(10));
        } while (Reserva::where('codigo_reserva', $codigo)->exists());

        return $codigo;
    }

    public function misReservas(Request $request)
    {
        $reservas = Reserva::where('user_id', $request->user()->id)
            ->noEscaneadas()
            ->with('mesas')
            ->orderBy('fecha', 'desc')
            ->get();

        return view('mis-reservas', compact('reservas'));
    }

    public function descargarTicket(string $codigo, Request $request)
    {
        $reserva = Reserva::where('codigo_reserva', $codigo)->firstOrFail();

        if ($reserva->user_id !== $request->user()->id && ! $request->user()->es_admin) {
            abort(403);
        }

        if (! $reserva->pdf_path || ! Storage::disk('public')->exists($reserva->pdf_path)) {
            abort(404, 'El ticket todavía no está disponible.');
        }

        return Storage::disk('public')->download($reserva->pdf_path, $reserva->codigo_reserva.'.pdf');
    }

    public function verQr(string $codigo, Request $request)
    {
        $reserva = Reserva::where('codigo_reserva', $codigo)->firstOrFail();

        if ($reserva->user_id !== $request->user()->id && ! $request->user()->es_admin) {
            abort(403);
        }

        if (! $reserva->qr_path || ! Storage::disk('public')->exists($reserva->qr_path)) {
            abort(404, 'El código QR todavía no está disponible.');
        }

        // Se sirve directo desde storage (no depende del symlink public/storage).
        return Storage::disk('public')->response($reserva->qr_path);
    }
}
