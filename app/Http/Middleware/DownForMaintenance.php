<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DownForMaintenance
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
        if (!!env('DOWN_FOR_MAINTENANCE', false) && (!isset($_COOKIE['debug']) || $_COOKIE['debug'] !== 'develop') ) {
            return response('', 503);
        }
        return $next($request);
    }
}
