<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPrivilege
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$privileges  Lista de privilegios requeridos
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$privileges)
    {
        // Verifica si el usuario está autenticado
        if (!auth()->check()) {
            return redirect('/login');
        }

        $user = auth()->user();
        $userPrivileges = $user->rol->privilegios->pluck('nombre')->toArray();

        // Verifica si el usuario tiene alguno de los privilegios requeridos
        foreach ($privileges as $privilege) {
            if (in_array($privilege, $userPrivileges)) {
                return $next($request);
            }
        }

        // Si no tiene privilegios suficientes, redirige
        return redirect('/documentos')->with('error', 'No tienes el privilegio necesario para acceder a esta página.');
    }

}


