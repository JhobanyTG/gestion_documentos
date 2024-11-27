<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Gerencia;
use App\Models\Subgerencia;
use App\Models\Subusuario; // Asegúrate de importar tu modelo de Subusuario

class CheckGerenciaOwnership
{
    public function handle($request, Closure $next)
    {
        $user = auth()->user();
        $gerenciaId = $request->route('gerencia');  // Asegúrate de obtener el ID de gerencia correctamente

        // Verificar si el usuario pertenece a la gerencia
        if ($user->gerencia_id == $gerenciaId || ($user->subgerencia && $user->subgerencia->gerencia_id == $gerenciaId)) {
            return $next($request);
        }

        // Si no tiene acceso, retornar un error 403
        abort(403, 'No tienes permiso para acceder a esta gerencia');
    }

}
