<?php

namespace App\Models;

use App\Exceptions\CustomException;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'Reserva';

    /**
     * Lanza una excepción en caso de fallar alguna validación
     */
    public static function ValidarReservaRecibida($reserva) {
        if(!isset($reserva->idPlanta)
            || !isset($reserva->fecha)
            || !isset($reserva->hora)
            || !isset($reserva->patente)
            || !isset($reserva->nombre)
            || !isset($reserva->apellido)
            || !isset($reserva->rut)
            || !isset($reserva->telefono)
            || !isset($reserva->email)
            || !isset($reserva->idComoNosConocio)
            || !isset($reserva->idComuna)
        ){
            throw new CustomException('Parámetros recibidos incorrectos', 400, null);
        }

        if (!self::ValidarPatente($reserva->patente)) {
            throw new CustomException('Patente incorrecta: ' . $reserva->patente, 400, null);
        }

        if (DateTime::createFromFormat('Y-m-d', $reserva->fecha) === false) {
            throw new CustomException('Fecha de reserva incorrecta: ' . $reserva->fecha, 400, null);
        }

        if (preg_match('/^[0-9]{2}:[0-9]{2}$/', $reserva->hora) !== 1) {
            throw new CustomException('Hora de reserva incorrecta: ' . $reserva->hora, 400, null);
        }

        if (strlen($reserva->nombre) > 50) {
            throw new CustomException('El nombre del cliente no puede tener más de 50 caracteres', 400, null);
        }

        if (strlen($reserva->apellido) > 50) {
            throw new CustomException('El apellido del cliente no puede tener más de 50 caracteres', 400, null);
        }

        if (strlen($reserva->rut) > 40) {
            throw new CustomException('El RUT ingresado no es válido', 400, null);
        }

        if (strlen($reserva->telefono) > 15) {
            throw new CustomException('El teléfono del cliente no puede tener más de 15 caracteres', 400, null);
        }

        $mailRegEx = '/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/';
        if (preg_match($mailRegEx , $reserva->email) !== 1) {
            throw new CustomException('El email ingresado no es válido', 400, null);
        }

        if (!is_numeric($reserva->idComoNosConocio) || !ComoNosConocio::find($reserva->idComoNosConocio)) {
            throw new CustomException('La opción de como nos conoció seleccionada es inválida', 400, null);
        }

        if (!is_numeric($reserva->idComuna) || !Comuna::where('CODCOMUNA', '=', $reserva->idComuna)->get()->first()) {
            throw new CustomException('La comuna seleccionada es inválida', 400, null);
        }
    }

    /**
     * Valida una patente recibida
     */
    private static function ValidarPatente($patente) {
        $pattern = '/^(([A-Z]{2}[0-9]{4})|([A-Z]{3}[0-9]{3})|([A-Z]{4}[0-9]{2}))$/';
        return preg_match($pattern, $patente) > 0;
    }

    /**
     * Esta función fue obtenida de https://www.lawebdelprogramador.com/codigo/PHP/3828-Clase-que-Validar-Rut.html
     * Retorna true/false en caso de que un rut sea válido o no
     */
    private static function ValidadorRut($trut) {

        $rut=str_replace('.', '', $trut);
        if (preg_match('/^(\d{1,9})-((\d|k|K){1})$/',$rut,$d)) {
            $s=1;$r=$d[1];for($m=0;$r!=0;$r/=10)$s=($s+$r%10*(9-$m++%6))%11;
            return chr($s?$s+47:75)==strtoupper($d[2]);
        }

        // $dvt = substr($trut, strlen($trut) - 1, strlen($trut));
        // $rutt = substr($trut, 0, strlen($trut) - 1);
        // $rut = (($rutt) + 0);
        // $c = 2;
        // $sum = 0;
        // while ($rut > 0)
        // {
        //     $a1 = $rut % 10;
        //     $rut = floor($rut / 10);
        //     $sum = $sum + ($a1 * $c);
        //     $c = $c + 1;
        //     if ($c == 8)
        //     {
        //         $c = 2;
        //     }
        // }
        // $di = $sum % 11;
        // $digi = 11 - $di;
        // $digi1 = ((string )($digi));
        // if (($digi1 == '10'))
        // {
        //     $digi1 = 'K';
        // }
        // if (($digi1 == '11'))
        // {
        //     $digi1 = '0';
        // }
        // return $dvt == $digi1;
    }

    /**
     * Valida si una patente tiene una reserva vigente 
     * @return Reserva|False Devuelve la reserva vigente o false
     */
    public static function ReservaVigente($patente){

        $reserva = Reserva::where('patente', '=', $patente)
            ->where('fecha', '>=', (new DateTime())->format('Y-m-d') . 'T00:00:00')
            ->where('codestado', '=', 1)
            ->get()
            ->first();

        return $reserva ? $reserva : false;
    }

    /**
     * Carga los datos de la reserva en el objeto actual
     */
    public function CargarDatosRecibidos($reserva){
        $this->Empresa = '0001'; // Siempre el mismo por default, en caso de que cambie modificar la lógica para recibirlo desde el front o determinarlo acá
        $this->codestado = '1'; // Siempre se inicia en estado 1
        $this->fechalta = (new DateTime())->format('Y-m-d\TH:i:s');
        $this->centro = $reserva->idPlanta;
        $this->fecha = $reserva->fecha . 'T00:00:00';
        $this->hora = $reserva->hora;
        $this->patente = $reserva->patente;
        $this->nombre = $reserva->nombre;
        $this->apellido = $reserva->apellido;
        $this->email = $reserva->email;
        $this->telefono = $reserva->telefono;
        $this->como_nos_conocio = $reserva->idComoNosConocio;
        $this->comuna = $reserva->idComuna;
        $this->rut = $reserva->rut;
    }

    /**
     * Crea el código de reserva según la fecha y el código de planta
     */
    public function CrearCodigo(){
        $this->codigo = $this->centro . substr(sha1($this->fechalta), 0, 10);
    }
}
