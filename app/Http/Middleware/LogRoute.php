<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogRoute
{
    public function handle(Request $request, Closure $next)
    {
        Log::info('=== RUTA EJECUTÁNDOSE ===');
        Log::info('Método: ' . $request->method());
        Log::info('Ruta: ' . $request->route()->getName());

        return $next($request);
    }
}
