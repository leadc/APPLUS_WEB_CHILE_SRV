<?php

namespace App\Exceptions;

use Exception;

class CustomException extends Exception
{
    public $responseMessage;
    public $statusCode;
    public $body;

    public function __construct($responseMessage, $statusCode, $body = null) {
        $this->responseMessage = $responseMessage;
        $this->statusCode = $statusCode;
        $this->body = $body;
    }

    /**
     * Returns an instance of response from the current data in the exception
     */
    public function GetRespose() {
        return response()
            ->json(
                [
                    'data' => $this->body,
                    'mensaje' => $this->responseMessage
                ])
            ->setStatusCode($this->statusCode);
    }
}
