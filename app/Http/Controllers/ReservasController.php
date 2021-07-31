<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Mail\mails\MailReserva;
use App\Models\Acucitas;
use App\Models\Centro;
use App\Models\ComoNosConocio;
use App\Models\Comuna;
use App\Models\Region;
use App\Models\Reserva;
use App\Models\Token;
use DateInterval;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use stdClass;

/**
 * Esta clase Maneja todas las solicitudes del formulario de reservas
 * de la web.
 */
class ReservasController extends Controller
{

    /**
     * Devuelve si hay que validar o no el captcha según el archivo .env
     */
    public static function VerificarCaptcha(Request $request) {
        try {
            $resp = (Object)[
                'verificarCaptcha' => env('GOOGLE_CAPTCHA_ACTIVADO', false),
                'captchaPublicKey' => (env('GOOGLE_CAPTCHA_ACTIVADO', false) ? env('GOOGLE_API_PUBLIC', null) : null)
            ];
            return self::GetResponse($resp);
        } catch (Exception $e){
            Log::error('ReservasController::VerificarCaptcha', [$request, $e]);
            return self::GetResponse(
                null,
                'Se produjo un error al obtener los datos para validación de captcha.',
                500
            );
        }
    }

    /**
     * Devuelve las opciones para el combo box de regiones y de plantas
     * [GET]
     * Recibe: {}
     * Deuvelve: {regiones: [ { id: number, descripcion: string } ], plantas: [ { id: number, idRegion: number, descripcion: string } ]}
     */
    public static function ObtenerDataPaso1(Request $request){
        try{
            $centros = Centro::all();
            $regiones = Region::all();
            $responseData = [];

            foreach ($centros as $centro) {
                foreach ($regiones as $region) {
                    if ($region->CODIGO === $centro->zona) {
                        $region->addPlanta($centro);
                    }
                }
            }

            foreach ($regiones as $region) {
                array_push($responseData, $region->exportData());
            }

            return self::GetResponse(
                (Object)['regiones' => $responseData],
                'Opciones para regiones y plantas.'
            );
        }catch(Exception $e){
            Log::error('ReservasController::ObtenerDataPaso1', [$request, $e]);
            return self::GetResponse(
                null,
                'Se produjo un error al obtener regiones y plantas.',
                500
            );
        }
    }

    /**
     * Devuelve las opciones para el combo box de como nos conoció y comunas
     * [GET]
     * Recibe: {}
     * Deuvelve: {comunas: [ { id: number, descripcion: string } ], comoNosConocio: [ { id: number, descripcion: string } ]}
     */
    public static function ObtenerDataPaso3(Request $request){
        try{
            $resp = (Object)[
                'comunas' => [],
                'comoNosConocio' => [],
                'verificarCaptcha' => env('GOOGLE_CAPTCHA_ACTIVADO', false),
                'captchaPublicKey' => (env('GOOGLE_CAPTCHA_ACTIVADO', false) ? env('GOOGLE_API_PUBLIC', null) : null)
            ];
            
            // Obtiene las comunas y las carga en el array para mostrar en el front end
            $comunas = Comuna::orderBy('NOMCOMUNA', 'asc')->get();
            foreach ($comunas as $comuna) {
                array_push($resp->comunas, $comuna->exportData());
            }

            // Obtiene las opciones habilitadas de como nos conocio y las envía al front end
            $comoNosConocio = ComoNosConocio::where('activa', '=', 1)
                ->get();
            foreach ($comoNosConocio as $como) {
                array_push($resp->comoNosConocio, $como->exportData());
            }

            return self::GetResponse(
                $resp,
                'Opciones para como nos conoció y comunas.'
            );
        } catch (CustomException $e) {
            return $e->GetRespose();
        } catch(Exception $e){
            Log::error('ReservasController::ObtenerDataPaso3', [$request, $e]);
            return self::GetResponse(
                null,
                'Se produjo un error al obtener las opciones de como nos conoció y comunas.',
                500
            );
        }
    }

    /**
     * Devuelve la disponibilidad
     * [GET]
     * Recibe: {fechaDesde: string, fechaHasta: string, centro: number}
     * Deuvelve: {disponibilidad: [ { fecha: string, horasDisponibles:[ { hora: string, cantidad: number } ] } ]}
     */
    public static function ObtenerDisponibilidad(Request $request){
        try{
            $data = (Object)$request->all();

            if (!isset($data->mesesAdelantados)) {
                return self::GetResponse(null, 'Falta enviar fecha desde', 400);
            }

            if (!isset($data->centro)) {
                return self::GetResponse(null, 'Falta enviar centro', 400);
            }

            $mesesAdelantados = is_numeric($data->mesesAdelantados) && $data->mesesAdelantados > 0 ? intval($data->mesesAdelantados) : 0;

            $fechaActual = new DateTime();
            $fechaInicio = new DateTime();
            $fechaFin = new DateTime();

            $fechaInicio->add(new DateInterval( 'P'.$mesesAdelantados.'M' ));
            $fechaFin->add(new DateInterval( 'P'.$mesesAdelantados.'M' ));

            $fechaInicio->setDate(
                $fechaInicio->format('Y'),
                $fechaInicio->format('m'),
                1
            );

            $fechaFin->setDate(
                $fechaFin->format('Y'),
                $fechaFin->format('m') + 1,
                0
            );

            $acucitas = Acucitas::where('fecha', '>=', $fechaInicio->format('Y-m-d') . 'T00:00:00')
                ->where('fecha', '<=', $fechaFin->format('Y-m-d') . 'T23:59:59')
                ->where('fecha', '>', $fechaActual->format('Y-m-d') . 'T00:00:00')
                ->where('centro', '=', $data->centro)
                ->orderBy('fecha', 'asc')
                ->get();

            $respuesta = (Object)[
                'fechaActual' => $fechaFin->format('Y-m-d'),
                'disponibilidad' => []
            ];

            foreach($acucitas as $acucita) {
                array_push($respuesta->disponibilidad, $acucita->exportData());
            }

            return self::GetResponse($respuesta);
        }catch(Exception $e){
            Log::error('ReservasController::ObtenerDisponibilidad', [$request, $e]);
            return self::GetResponse(
                null,
                'Se produjo un error al obtener la disponibilidad de turnos.',
                500
            );
        }
    }

    /**
     * Registra una reserva
     * [POST]
     * Recibe: {reserva: Reserva}
     * Deuvelve: {reserva: Reserva}
     */
    public static function RealizarReserva(Request $request){
        $reservaConsumida = false;
        $reserva = new Reserva();
        try{

            $data = (Object)$request->all();

            if (!isset($data->reserva)) {
                return self::GetResponse(null, 'Falta enviar reserva', 400);
            }
            $data->reserva = (Object)$data->reserva;

            $verificarCaptcha = env('GOOGLE_CAPTCHA_ACTIVADO', false);

            if ($verificarCaptcha == 'true' && !isset($data->captcha)) {
                return self::GetResponse(null, 'Debe resolver el captcha para poder continuar', 400);
            }

            if ($verificarCaptcha == 'true' && !self::verificarToken($data->captcha)){
                return self::GetResponse(null, 'Se produjo un error al validar el captcha', 400);
            }

            Reserva::ValidarReservaRecibida($data->reserva);

            if (Reserva::ReservaVigente($data->reserva->patente)){
                return self::GetResponse(null, 'La patente ingresada tiene un turno vigente', 409);
            }

            $reservaConsumida = Acucitas::ConsumirDisponibilidad($data->reserva->idPlanta, $data->reserva->fecha, $data->reserva->hora);
            if (!$reservaConsumida) {
                return self::GetResponse(null, 'El turno seleccionado ya no se encuentra disponible', 410);
            }

            $reserva->CargarDatosRecibidos($data->reserva);
            $reserva->CrearCodigo();
            $reserva->ip = $request->ip();

            if (!$reserva->save()) {
                throw new Exception('No se pudo guardar la reserva');
            }

            try{
                $mail = new MailReserva(
                    $reserva->nombre,
                    $reserva->apellido,
                    $reserva->patente,
                    $reserva->codigo,
                    (new DateTime($reserva->fecha))->format('d/m/Y'),
                    $reserva->hora,
                    $data->reserva->descripcionPlanta,
                    $data->reserva->observacionPlanta
                );
                Mail::to($reserva->email)->send($mail);
            } catch(Exception $e){
                Log::error('ReservasController::RealizarReserva ERROR DE ENVIO DE MAIL', [$e]);
            }
            return self::GetResponse(
                $reserva->codigo,
                'Reserva confirmada.'
            );
        } catch (CustomException $e) {
            if ($reservaConsumida) {
                Acucitas::RevertirDisponibilidadConsumida($data->reserva->idPlanta, $data->reserva->fecha, $data->reserva->hora);
            }
            return $e->GetRespose();
        } catch(Exception $e){
            Log::error('ReservasController::RealizarReserva', [$request, $e]);
            if ($reservaConsumida) {
                Acucitas::RevertirDisponibilidadConsumida($data->reserva->idPlanta, $data->reserva->fecha, $data->reserva->hora);
            }
            return self::GetResponse(
                null,
                'Se produjo un error al tratar de realizar la reserva.',
                500
            );
        }
    }

    /**
    * Verifica el token del captcha y regresa true o false
    * true en caso de que el usuario haya pasado la prueba
    * false en caso contrario
    */
    private static function verificarToken($token){
        # La API en donde verificamos el token
        $url = env('GOOGLE_CAPTCHA_API');
        # Clave secreta del sitio de google (se obtiene en https://www.google.com/recaptcha/admin)
        $claveSecreta = env('GOOGLE_API_SECRET');
        # Petición
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "secret=$claveSecreta&response=$token");
        $headers = array(
            "Content-Type: application/x-www-form-urlencoded",
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $resultado = curl_exec($ch);
        $resultado = json_decode($resultado);
        # La variable que nos interesa para saber si el usuario pasó o no la prueba
        # está en success
        return $resultado && isset($resultado->success) ? $resultado->success : false;
   }

   public static function LocalizarReserva(Request $request) {
        $data = (Object)$request->all();
        try{
            if (!isset($data->codigo)) {
                return self::GetResponse(null, 'Debe enviar un código de reserva', 400);
            }

            if (!isset($data->patente) || !Reserva::ValidarPatente($data->patente)) {
                return self::GetResponse(null, 'Debe enviar una patente válida', 400);
            }

            $verificarCaptcha = env('GOOGLE_CAPTCHA_ACTIVADO', false);
            if ($verificarCaptcha && (!isset($data->captchaToken) || !self::verificarToken($data->captchaToken))) {
                return self::GetResponse(null, 'Debe resolver el captcha', 400);
            }

            $currentDate = new DateTime();

            $reserva = Reserva::where('patente', '=', $data->patente)
                ->where('codigo', '=', $data->codigo)
                ->where('codestado', '=', '1')
                ->where('fecha', '>', $currentDate->format('Y-m-d') . 'T00:00:00')
                ->orderBy('fecha', 'desc')
                ->get()
                ->first();

            if (!$reserva) {
                return self::GetResponse(null, 'Reserva no encontrada', 404);
            }

            $reservaResp = new stdClass();
            $reservaResp->id = $reserva->numero;
            $reservaResp->fecha = $reserva->fecha;
            $reservaResp->hora = trim($reserva->hora);
            $reservaResp->codigo = $reserva->codigo;
            $reservaResp->patente = trim($reserva->patente);
            $reservaResp->nombre = trim($reserva->nombre);
            $reservaResp->apellido = trim($reserva->apellido);
            $reservaResp->idPlanta = trim($reserva->centro);

            $centro = Centro::where('centro', '=', $reservaResp->idPlanta)->get()->first();

            if ($centro) {
                $reservaResp->descripcionPlanta = trim($centro->Nombre);
                $reservaResp->observacionPlanta = trim($centro->Observacion);
            }

            return self::GetResponse($reservaResp, 'Reserva localizada')
                ->header('Authorization', Token::CreateToken(trim($reservaResp->patente).trim($reservaResp->codigo)));
        } catch(CustomException $e) {
            return $e->GetRespose();
        } catch(Exception $e) {
            Log::error('ReservasController::LocalizarReserva', [$request, $e]);
            return self::GetResponse(
                null,
                'Se produjo un error al tratar de localizar la reserva.',
                500
            );
        }
   }

   /**
    * Endpoint que elimina una reserva
    */
   public static function CancelarReserva(Request $request) {
        $data = (Object)$request->all();
        try{
            if (!isset($data->id)) {
                return self::GetResponse(null, 'Debe enviar un id de reserva', 400);
            }
            if (!isset($data->patente)) {
                return self::GetResponse(null, 'Debe enviar una patente', 400);
            }
            if (!isset($data->codigo)) {
                return self::GetResponse(null, 'Debe enviar un código de reserva', 400);
            }

            $reserva = Reserva::where('numero', '=', $data->id)
                ->where('patente', '=', $data->patente)
                ->where('codigo', '=', $data->codigo)
                ->where('codestado', '=', '1')
                ->get()
                ->first();

            if (!$reserva) {
                return self::GetResponse(null, 'Reserva no encontrada', 404);
            }

            $reserva->codestado = 2;

            $reserva->save();

            Acucitas::RevertirDisponibilidadConsumida($reserva->centro, substr($reserva->fecha, 0, 10), $reserva->hora);

            return self::GetResponse(null, 'Reserva cancelada');
        } catch(CustomException $e) {
            return $e->GetRespose();
        } catch(Exception $e) {
            Log::error('ReservasController::CancelarReserva', [$request, $e]);
            return self::GetResponse(
                null,
                'Se produjo un error al tratar de cancelar la reserva.',
                500
            );
        }
   }
}
