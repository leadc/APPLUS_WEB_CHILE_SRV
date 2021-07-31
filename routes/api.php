<?php

use App\Http\Controllers\ReservasController;
use App\Mail\mails\MailReserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('/reservas')->group(function(){
    Route::get('obtenerDataPaso1', [ReservasController::class, 'ObtenerDataPaso1']);
    Route::get('obtenerDataPaso2', [ReservasController::class, 'ObtenerDataPaso2']);
    Route::get('obtenerDataPaso3', [ReservasController::class, 'ObtenerDataPaso3']);
    Route::get('obtenerDisponibilidad', [ReservasController::class, 'ObtenerDisponibilidad']);
    Route::get('validarVehiculo', [ReservasController::class, 'ValidarVehiculo']);
    Route::post('reservar', [ReservasController::class, 'RealizarReserva']);
    Route::get('localizar', [ReservasController::class, 'LocalizarReserva']);
    Route::middleware('verifyTokenWeb')->delete('reserva', [ReservasController::class, 'CancelarReserva']);
});

Route::get('verificarCaptcha', [ReservasController::class, 'VerificarCaptcha']);