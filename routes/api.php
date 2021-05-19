<?php

use App\Http\Controllers\ReservasController;
use App\Mail\mails\test;
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

        $message="BEGIN:VCALENDAR
        VERSION:2.0
        CALSCALE:GREGORIAN
        METHOD:REQUEST
        BEGIN:VEVENT
        DTSTART:20110718T121000Z
        DTEND:20110718T131000Z
        DTSTAMP:20110525T075116Z
        ORGANIZER;CN=From Name:mailto:from email id
        UID:12345678
        ATTENDEE;PARTSTAT=NEEDS-ACTION;RSVP= TRUE;CN=Sample:mailto:sample@test.com
        DESCRIPTION:This is a test of iCalendar event invitation.
        LOCATION: Kochi
        SEQUENCE:0
        STATUS:CONFIRMED
        SUMMARY:Test iCalendar
        TRANSP:OPAQUE
        END:VEVENT
        END:VCALENDAR";
    
        /*Setting the header part, this is important */
        $headers = "From: From Name <From Mail>\n";
        $headers .= "MIME-Version: 1.0\n";
        $headers .= "Content-Type: text/calendar; method=REQUEST;\n";
        $headers .= '        charset="UTF-8"';
        $headers .= "\n";
        $headers .= "Content-Transfer-Encoding: 7bit";
    
        /*mail content , attaching the ics detail in the mail as content*/
        $subject = "Meeting Subject";
        $subject = html_entity_decode($subject, ENT_QUOTES, 'UTF-8');
    
        /*mail send*/
    
        $mailable = new test("Lean");
    
        Mail::to('leandroccrs5@gmail.com')->send($mailable);
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