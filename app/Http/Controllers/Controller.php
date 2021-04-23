<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Devuelve una respuesta del backend
     */
    protected static function GetResponse($data = null, $message = '', $statusCode = 200){
        return response()
            ->json(
                [
                    'data' => $data,
                    'mensaje' => $message
                ])
            ->setStatusCode($statusCode);
    }
}
