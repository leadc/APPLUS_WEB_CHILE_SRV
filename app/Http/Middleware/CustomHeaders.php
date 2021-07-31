<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CustomHeaders
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
        $response = $next($request);
        $response->header('access-control-expose-headers', 'Authorization');
        return $response;
    }
}
