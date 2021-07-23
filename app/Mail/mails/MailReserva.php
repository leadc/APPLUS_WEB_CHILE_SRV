<?php

namespace App\Mail\mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailReserva extends Mailable
{
    use Queueable, SerializesModels;

    public $nombre;
    public $apellido;
    public $patente;
    public $codigo;
    public $fecha;
    public $hora;
    public $centro;
    public $direccion;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        $nombre,
        $apellido,
        $patente,
        $codigo,
        $fecha,
        $hora,
        $centro,
        $direccion
    )
    {
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->patente = $patente;
        $this->codigo = $codigo;
        $this->fecha = $fecha;
        $this->hora = $hora;
        $this->centro = $centro;
        $this->direccion = $direccion;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject('Confirmación de Reserva');
        return $this->view('mailReserva');
    }
}
