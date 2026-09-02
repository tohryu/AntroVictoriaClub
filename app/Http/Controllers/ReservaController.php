<?php

namespace App\Http\Controllers;

use App\Models\Evento;
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

    public function mapa(Request $request)
    {
        $mesas = Mesa::all();

        $eventoId = $request->query('evento');
        $eventoActivo = null;

        if ($eventoId) {
            $eventoActivo = Evento::where('activo', true)
                ->where('fecha', '>=', now()->toDateString())
                ->find($eventoId);
        }

        // Si no mandaron evento_id, o el que mandaron ya no es válido
        // (vencido, borrado, etc.), cae al próximo evento por default.
        if (! $eventoActivo) {
            $eventoActivo = Evento::proximoEventoActivo();
        }

        $ventasActivas = $eventoActivo && $eventoActivo->ventas_activas;
        $mapaPrecios = $eventoActivo ? $eventoActivo->mapaPreciosMesa() : [];

        // La disponibilidad ahora es POR EVENTO: una mesa ocupada para
        // este evento no bloquea la misma mesa en otra fecha/evento.
        $mesasReservadasIds = $eventoActivo ? $eventoActivo->mesasOcupadasIds() : [];

        return view('reservar-mesa', compact('mesas', 'mesasReservadasIds', 'eventoActivo', 'ventasActivas', 'mapaPrecios'));
    }

    public function procesarReserva(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'evento_id' => 'required|integer|exists:eventos,id',
            'mesa_ids' => 'required|array|min:1',
            'mesa_ids.*' => 'integer|distinct|exists:mesas,id',
            'zona' => 'nullable|string|max:255',
            'metodo_pago' => 'required|string|in:tarjeta,paypal',
            'referencia_pago' => 'required|string|max:255',
        ]);

        // El evento y su fecha SIEMPRE se validan en el servidor — el
        // usuario ya no puede elegir ni manipular la fecha ni los precios
        // desde el formulario. Cada evento tiene sus propias ventas y sus
        // propios precios, independientes de los demás.
        $evento = Evento::find($validated['evento_id']);

        if (! $evento || ! $evento->estaEnVenta()) {
            return back()->withErrors(['mesa_ids' => 'Las reservaciones para ese evento no están abiertas en este momento.'])->withInput();
        }

        $validated['fecha'] = $evento->fecha;

        try {
            $reserva = DB::transaction(function () use ($validated, $request, $evento) {
                $mesas = Mesa::whereIn('id', $validated['mesa_ids'])
                    ->lockForUpdate()
                    ->get();

                if ($mesas->count() !== count($validated['mesa_ids'])) {
                    throw ValidationException::withMessages([
                        'mesa_ids' => 'Una de las mesas seleccionadas ya no existe.',
                    ]);
                }

                // Re-checar disponibilidad DENTRO de la transacción: como
                // lockForUpdate() ya puso en fila estas mesas, si dos
                // personas intentan reservar la misma mesa al mismo tiempo,
                // la segunda espera a que la primera termine y aquí ve la
                // reservación recién creada, evitando el doble apartado.
                $mesasOcupadasIds = $evento->mesasOcupadasIds();
                $mesaNoDisponible = $mesas->first(fn (Mesa $m) => in_array($m->id, $mesasOcupadasIds));
                if ($mesaNoDisponible) {
                    throw ValidationException::withMessages([
                        'mesa_ids' => 'La mesa '.$mesaNoDisponible->numero.' ya fue reservada por otra persona para este evento. Elige otra.',
                    ]);
                }

                $mapaPrecios = $evento->mapaPreciosMesa();
                $total = $mesas->sum(fn (Mesa $m) => $evento->precioMesa($m, $mapaPrecios));

                if ($validated['metodo_pago'] === 'tarjeta') {
                    (new ConektaPaymentService())->verificarPagado($validated['referencia_pago'], $total, 'MXN');
                } else {
                    (new PaypalPaymentService())->capturarOrden($validated['referencia_pago'], $total, 'MXN');
                }

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
                    $mesa->id => ['precio_al_momento' => $evento->precioMesa($mesa, $mapaPrecios)],
                ])->toArray();

                $reserva->mesas()->attach($pivotData);

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
            'qr_url' => $reserva->qr_path ? route('reservas.qr', $reserva->codigo_reserva) : null,
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
