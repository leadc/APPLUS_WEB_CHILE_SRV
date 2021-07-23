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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/test', function (Request $request) {
    try{    
        $mailable = new MailReserva("Lean", 'Caceres', 'AAA123', 'CODIGO', '2021-05-02', '15:30', 'QUINTA NORMAL', 'DIRECCIóN');
        Mail::to('leandrodamian@hotmail.com')->send($mailable);
        return 'sent';
    }catch (Exception $e){
        return $e->getMessage();
    }

});

Route::prefix('/reservas')->group(function(){
    Route::get('obtenerDataPaso1', [ReservasController::class, 'ObtenerDataPaso1']);
    Route::get('obtenerDataPaso2', [ReservasController::class, 'ObtenerDataPaso2']);
    Route::get('obtenerDataPaso3', [ReservasController::class, 'ObtenerDataPaso3']);
    Route::get('obtenerDisponibilidad', [ReservasController::class, 'ObtenerDisponibilidad']);
    Route::get('validarVehiculo', [ReservasController::class, 'ValidarVehiculo']);
    Route::post('reservar', [ReservasController::class, 'RealizarReserva']);
});