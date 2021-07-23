<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            .container{
                max-width: 720px;
                margin-left: auto;
                margin-right: auto;
                padding-top: 50px;
                padding-left: 25px;
                padding-right: 25px;
                padding-bottom: 50px;
                background-color: #e9ecef;
            }
            .fc-orange{
                color: #FC6500;
            }
            .font-weight-bold{
                font-weight: bold;
            }
            .text-center{
                text-align: center;
            }
            .d-none{
                display: none;
            }
        </style>
        <title>Applus PRT</title>
    </head>
    <body>
        <span class="d-none">Confirmación de su reserva de turno para realizar la revisión técnica de su veículo. <br></span>
        <div class="container">
            <p>
                Estimado {{ $nombre }} {{ $apellido }}:
            </p>
            <p>
                Su reserva fue confirmada con éxito, a continuación le enviamos los detalles de la misma
            </p>
            <ul>
                <li>Patente: <span class="font-weight-bold">{{ $patente }}</span></li>
                <li>Código de reserva: <span class="font-weight-bold">{{ $codigo }}</span></li>
                <li>Día: <span class="font-weight-bold">{{ $fecha }}</span></li>
                <li>Horario: <span class="font-weight-bold">{{ $hora }}</span></li>
                <li>Centro: <span class="font-weight-bold">{{ $centro }}</span></li>
                <li>Dirección: <span class="font-weight-bold">{{ $direccion }}</span></li>
            </div>
        </div>
    </body>
</html>
