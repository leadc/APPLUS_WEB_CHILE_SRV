<?php

namespace App\Http\Middleware;

use App\Models\Token;
use Closure;
use Illuminate\Http\Request;

class VerifyTokenWeb
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $accessToken = $request->header('Authorization');
        if (!$accessToken) {
            return response()
                ->json(
                    [
                        'data' => null,
                        'mensaje' => 'Se requiere estar autenticado para procesar su solicitud.'
                    ]
                )->setStatusCode(403);
        }
        $data = (Object)$request->all();
        $tokenModel = Token::ValidateWebToken($accessToken, $data->patente, $data->codigo);
        if (!$tokenModel) {
            return response()
                ->json(
                    [
                        'data' => null,
                        'mensaje' => 'Su sesión expiró.'
                    ]
                )->setStatusCode(403);
        }
        $response = $next($request);
        return $response->header('Authorization', $accessToken);
    }
}
