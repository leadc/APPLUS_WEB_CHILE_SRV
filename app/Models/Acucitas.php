<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use stdClass;

class Acucitas extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'acucitas';

    private static $HORAS = [
        '07:00', '07:15', '07:30', '07:45',
        '08:00', '08:15', '08:30', '08:45',
        '09:00', '09:15', '09:30', '09:45',
        '10:00', '10:15', '10:30', '10:45',
        '11:00', '11:15', '11:30', '11:45',
        '12:00', '12:15', '12:30', '12:45',
        '13:00', '13:15', '13:30', '13:45',
        '14:00', '14:15', '14:30', '14:45',
        '15:00', '15:15', '15:30', '15:45',
        '16:00', '16:15', '16:30', '16:45',
        '17:00', '17:15', '17:30', '17:45',
        '18:00', '18:15', '18:30', '18:45',
        '19:00', '19:15', '19:30', '19:45',
        '20:00', '20:15', '20:30', '20:45',
    ];


    public function exportData() {
        $export = new stdClass();
        $export->fecha = substr($this->Fecha, 0, 10);
        $export->horasDisponibles = [];
        foreach(self::$HORAS as $hora) {
            $key = 'DP' . implode('', explode(':', $hora));
            if ($this->$key > 0) {
                $disp = new stdClass();
                $disp->hora = $hora;
                $disp->cantidad = $this->$key;
                array_push($export->horasDisponibles, $disp);
            }
        }
        return $export;
    }

    /**
     * Consume un número de disponibilidad y devuelve true en caso de exito
     */
    public static function ConsumirDisponibilidad($centro, $fecha, $hora) {
        try{
            $fecha = strlen($fecha) === 10 ? $fecha : substr($fecha, 0, 10);
            $fechaArray = explode('-', $fecha);
            $fechaDMY = $fechaArray[2] . '-' . $fechaArray[1] . '-' . $fechaArray[0];
            $key = 'DP' . implode('', explode(':', $hora));
            DB::statement("UPDATE acucitas SET $key = $key - 1 WHERE centro = $centro and (CONVERT(varchar, Fecha, 23) = '$fecha' or CONVERT(varchar, Fecha, 23) = '$fechaDMY')");
            /*
            DB::table('acucitas')
                ->where('centro', '=', $centro)
                ->where('Fecha', '=', $fecha)
                ->update(["$key" => ($acucita->$key - 1)]);
            */
            return true;
        }catch(Exception $e){
            Log::error('Acucitas::ConsumirDisponibilidad', [$e->getMessage()]);
            throw new Exception('Se produjo un error al consumir la disponibilidad');
        }
    }

    /**
     * Vuelve a sumar 1 a la disponibilidad
     * Este método se usará para manejar los errores al guardar reservas
     */
    public static function RevertirDisponibilidadConsumida($centro, $fecha, $hora) {
        try{
            $fecha = strlen($fecha) === 10 ? $fecha : substr($fecha, 0, 10);
            $fechaArray = explode('-', $fecha);
            $fechaDMY = $fechaArray[2] . '-' . $fechaArray[1] . '-' . $fechaArray[0];
            $key = 'DP' . implode('', explode(':', $hora));
            DB::statement("UPDATE acucitas SET $key = $key + 1 WHERE centro = $centro and (CONVERT(varchar, Fecha, 23) = '$fecha' or CONVERT(varchar, Fecha, 23) = '$fechaDMY')");
            /*
            DB::table('acucitas')
                ->where('centro', '=', $centro)
                ->where('Fecha', '=', $fecha)
                ->update(["$key" => ($acucita->$key + 1)]);
            */
        }catch(Exception $e){
            Log::error('RevertirDisponibilidadConsumida::ConsumirDisponibilidad', [$e->getMessage()]);
        }
    }
}
