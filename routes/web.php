<?php

use App\Http\Controllers\PromocionController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\CoverController;
use App\Http\Controllers\PaymentController;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Admin\MesaAdminController;
use App\Http\Controllers\Admin\CoverAdminController;
use App\Http\Controllers\Admin\EscanerController;

Route::get('/', [PromocionController::class, 'publicIndex'])->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/reservar-mesa', [ReservaController::class, 'mapa'])->name('reserva.mapa');
    Route::post('/procesar-reserva', [ReservaController::class, 'procesarReserva'])
        ->middleware('throttle:10,1')
        ->name('reserva.procesar');
    Route::get('/reserva-exitosa/{codigo}', [ReservaController::class, 'exitosa'])->name('reserva.exitosa');
    Route::get('/mis-reservas', [ReservaController::class, 'misReservas'])->name('reservas.mis_reservas');
    Route::get('/mis-reservas/{codigo}/ticket', [ReservaController::class, 'descargarTicket'])->name('reservas.ticket');
    Route::get('/mis-reservas/{codigo}/qr', [ReservaController::class, 'verQr'])->name('reservas.qr');

    Route::get('/cover', [CoverController::class, 'formulario'])->name('cover.formulario');
    Route::post('/cover/procesar', [CoverController::class, 'procesar'])
        ->middleware('throttle:10,1')
        ->name('cover.procesar');
    Route::get('/cover-exitoso/{codigo}', [CoverController::class, 'exitoso'])->name('cover.exitoso');
    Route::get('/mis-boletos', [CoverController::class, 'misBoletos'])->name('cover.mis_boletos');
    Route::get('/mis-boletos/{codigo}/ticket', [CoverController::class, 'descargarTicket'])->name('cover.ticket');
    Route::get('/mis-boletos/{codigo}/qr', [CoverController::class, 'verQr'])->name('cover.qr');

    Route::prefix('pago')->name('pago.')->middleware('throttle:20,1')->group(function () {
        Route::post('/conekta/orden', [PaymentController::class, 'crearOrdenConektaMesas'])->name('conekta.orden');
        Route::post('/paypal/orden', [PaymentController::class, 'crearOrdenPaypal'])->name('paypal.orden');
        Route::post('/paypal/orden/capturar', [PaymentController::class, 'capturarOrdenPaypal'])->name('paypal.capturar');

        Route::post('/cover/conekta/orden', [PaymentController::class, 'crearOrdenConektaCover'])->name('cover.conekta.orden');
        Route::post('/cover/paypal/orden', [PaymentController::class, 'crearOrdenPaypalCover'])->name('cover.paypal.orden');
        Route::post('/cover/paypal/orden/capturar', [PaymentController::class, 'capturarOrdenPaypalCover'])->name('cover.paypal.capturar');
    });
});

Route::prefix('admin/promociones')->name('admin.promociones.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [PromocionController::class, 'index'])->name('index');

    Route::post('/', [PromocionController::class, 'store'])->name('store');
    Route::patch('/{id}/toggle', [PromocionController::class, 'toggleStatus'])->name('toggle');
    Route::delete('/{id}', [PromocionController::class, 'destroy'])->name('destroy');

    Route::post('/eventos', [PromocionController::class, 'storeEvento'])->name('eventos.store');
    Route::patch('/eventos/{id}/toggle', [PromocionController::class, 'toggleStatusEvento'])->name('eventos.toggle');
    Route::delete('/eventos/{id}', [PromocionController::class, 'destroyEvento'])->name('eventos.destroy');
});

Route::prefix('admin/mesas')->name('admin.mesas.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [MesaAdminController::class, 'index'])->name('index');
    Route::patch('/cover/precio', [CoverAdminController::class, 'updatePrecio'])->name('cover.update_precio');
    Route::patch('/cover/entrada-libre', [CoverAdminController::class, 'activarEntradaLibre'])->name('cover.entrada_libre');
    Route::patch('/{id}/precio', [MesaAdminController::class, 'updatePrecio'])->name('update_precio');
    Route::patch('/{id}/disponibilidad', [MesaAdminController::class, 'toggleDisponibilidad'])->name('toggle_disponible');
    Route::patch('/evento-activo/ventas', [MesaAdminController::class, 'toggleVentasEvento'])->name('evento_activo.toggle_ventas');
});

Route::prefix('admin/escaner')->name('admin.escaner.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [EscanerController::class, 'index'])->name('index');
    Route::post('/verificar', [EscanerController::class, 'verificar'])
        ->middleware('throttle:60,1')
        ->name('verificar');
});

Route::prefix('{current_team}')
    ->where(['current_team' => '[a-zA-Z0-9\-]+'])
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::view('dashboard', 'dashboard')->name('dashboard');
    });

Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

require __DIR__.'/settings.php';
