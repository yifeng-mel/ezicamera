<?php

namespace App\Http\Middleware;
use Closure;
use App\Configuration;

class CheckInitalSetup
{
    public function handle($request, Closure $next, $guard = null)
    {
        if (!Configuration::where('key', 'initial_setup_done')->first()) {
            return redirect('/initial_setup');
        }

        return $next($request);
    }
}
