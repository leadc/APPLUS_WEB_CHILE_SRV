<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Esta clase Maneja todas las solicitudes del formulario de reservas
 * de la web.
 */
class ReservasController extends Controller
{

    /**
     * Devuelve las opciones para el combo box de regiones y de plantas
     * [GET]
     * Recibe: {}
     * Deuvelve: {regiones: [ { id: number, descripcion: string } ], plantas: [ { id: number, idRegion: number, descripcion: string } ]}
     */
    public static function ObtenerDataPaso1(Request $request){
        try{
            return self::GetResponse(
                null,
                'Opciones para regiones y plantas.'
            );
        }catch(Exception $e){
            Log::error('Controller::ObtenerDataPaso1', [$request, $e]);
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
            return self::GetResponse(
                null,
                'Opciones para como nos conoció y comunas.'
            );
        }catch(Exception $e){
            Log::error('Controller::ObtenerDataPaso3', [$request, $e]);
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
            return self::GetResponse(
                (object)[
                    "disponibilidad" => [
                        (object)[
                            'fecha' => '2021-05-30',
                            'horasDisponibles' => [
                                (object)['hora' => '11:00', 'cantidad' => 5],
                                (object)['hora' => '11:15', 'cantidad' => 5],
                                (object)['hora' => '11:30', 'cantidad' => 5]
                            ]
                        ],
                        (object)[
                            'fecha' => '2021-04-22',
                            'horasDisponibles' => [
                                (object)['hora' => '14:00', 'cantidad' => 5],
                                (object)['hora' => '14:15', 'cantidad' => 5],
                                (object)['hora' => '14:30', 'cantidad' => 5]
                            ]
                        ],
                        (object)[
                            'fecha' => '2021-04-24',
                            'horasDisponibles' => [
                                (object)['hora' => '12:00', 'cantidad' => 5],
                                (object)['hora' => '12:15', 'cantidad' => 5],
                                (object)['hora' => '12:30', 'cantidad' => 5]
                            ]
                        ],
                    ],
                    "fechaActual" => date('Y-m-d')

                ],
                'Disponibilidad.'
            );
        }catch(Exception $e){
            Log::error('Controller::ObtenerDisponibilidad', [$request, $e]);
            return self::GetResponse(
                null,
                'Se produjo un error al obtener la disponibilidad de turnos.',
                500
            );
        }
    }

    /**
     * Valida una patente
     * [GET]
     * Recibe: {patente: string}
     * Deuvelve: {patente: string}
     */
    public static function ValidarVehiculo(Request $request){
        try{
            return self::GetResponse(
                null,
                'Vehículo válido.'
            );
        }catch(Exception $e){
            Log::error('Controller::ValidarVehiculo', [$request, $e]);
            return self::GetResponse(
                null,
                'Se produjo un error al validar el vehículo.',
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
        try{
            return self::GetResponse(
                null,
                'Reserva confirmada.'
            );
        }catch(Exception $e){
            Log::error('Controller::RealizarReserva', [$request, $e]);
            return self::GetResponse(
                null,
                'Se produjo un error inesperado al tratar de realizar la reserva.',
                500
            );
        }
    }
}
