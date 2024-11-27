<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\BackupAccion;
use App\Models\User;
use App\Models\Rol;
use Symfony\Component\HttpFoundation\Response;

class RegisterAdminActions
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (auth()->check()) {
                $user = auth()->user();
                $tipoAdmin = $user->hasPrivilege('Acceso Total') ? 'ADMIN_TOTAL' : 'ADMIN_GERENCIA';

                $usuarioAfectado = $this->obtenerUsuarioAfectado($request);

                BackupAccion::create([
                    'admin_id' => $user->id,
                    'admin_nombre' => $user->persona->nombres . ' ' .
                                    $user->persona->apellido_p . ' ' .
                                    $user->persona->apellido_m,
                    'tipo_peticion' => $request->method(),
                    'accion' => $this->determinarAccion($request),
                    'descripcion' => $this->obtenerDescripcion($request, $tipoAdmin),
                    'usuario_afectado_id' => $usuarioAfectado ? $usuarioAfectado['id'] : null,
                    'usuario_afectado_nombre' => $usuarioAfectado ? $usuarioAfectado['nombre'] : null,
                    'detalles_cambios' => json_encode($this->obtenerDetallesCambios($request, $tipoAdmin))
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error en middleware: ' . $e->getMessage());
        }

        return $next($request);
    }

    private function obtenerUsuarioAfectado($request): ?array
    {
        if ($request->route('usuario')) {
            $afectado = $request->route('usuario');
            if (is_string($afectado)) {
                $afectado = User::find($afectado);
            }
        }
        elseif ($request->route('id')) {
            $afectado = User::find($request->route('id'));
        } else {
            $afectado = null;
        }

        if ($afectado) {
            return [
                'id' => $afectado->id,
                'nombre' => $afectado->persona->nombres . ' ' .
                        $afectado->persona->apellido_p . ' ' .
                        $afectado->persona->apellido_m
            ];
        }

        return null;
    }

    private function determinarAccion($request): string
    {
        $routeName = $request->route()->getName();

        if (str_contains($routeName, 'update')) return 'ACTUALIZAR_USUARIO';
        if (str_contains($routeName, 'destroy')) return 'ELIMINAR_USUARIO';
        if (str_contains($routeName, 'actualizarContrasena')) return 'ACTUALIZAR_CONTRASEÑA';

        return $request->method();
    }

    private function obtenerDescripcion($request, $tipoAdmin): string
    {
        $routeName = $request->route()->getName();
        $prefijo = "[$tipoAdmin] ";

        if (str_contains($routeName, 'update')) {
            return $prefijo . 'Actualización de datos de usuario';
        }
        if (str_contains($routeName, 'destroy')) {
            return $prefijo . 'Eliminación de usuario del sistema';
        }
        if (str_contains($routeName, 'actualizarContrasena')) {
            return $prefijo . 'Actualización de contraseña de usuario';
        }

        return $prefijo . "Acción {$request->method()} realizada en ruta {$routeName}";
    }

    // private function obtenerDetallesCambios($request, $tipoAdmin): array
    // {
    //     $details = [
    //         'tipo_admin' => $tipoAdmin,
    //         'url' => $request->fullUrl(),
    //         'ip' => $request->ip(),
    //         'user_agent' => $request->userAgent(),
    //         'timestamp' => now()->toString()
    //     ];

    //     if ($tipoAdmin === 'ADMIN_GERENCIA') {
    //         $details['gerencia_id'] = auth()->user()->gerencia_id ?? null;
    //     }

    //     if (str_contains($request->route()->getName(), 'actualizarContrasena')) {
    //         $details['datos_modificados'] = ['accion' => 'cambio_contraseña'];
    //     } else {
    //         $datosModificados = $request->except([
    //             'password',
    //             'password_confirmation',
    //             'current_password',
    //             '_token',
    //             '_method'
    //         ]);

    //         if (!empty($datosModificados)) {
    //             $details['datos_modificados'] = $datosModificados;
    //         }
    //     }

    //     return $details;
    // }

    private function obtenerDetallesCambios($request, $tipoAdmin): array
    {
    $details = [
        'tipo_admin' => $tipoAdmin,
        'url' => $request->fullUrl(),
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent(),
        'timestamp' => now()->toString()
    ];

    if ($tipoAdmin === 'ADMIN_GERENCIA') {
        $details['gerencia_id'] = auth()->user()->gerencia_id ?? null;
    }

    if (str_contains($request->route()->getName(), 'actualizarContrasena')) {
        $details['datos_modificados'] = ['accion' => 'cambio_contraseña'];
    } else {
        $datosModificados = $request->except([
            'password', 'password_confirmation', 'current_password',
            '_token', '_method'
        ]);

        if (!empty($datosModificados)) {
            // Guardar tanto ID como nombre del rol
            if (isset($datosModificados['rol_id'])) {
                $rol = \App\Models\Rol::find($datosModificados['rol_id']);
                $datosModificados['rol'] = [
                    'id' => $datosModificados['rol_id'],
                    'nombre' => $rol ? $rol->nombre : 'Desconocido'
                ];
                unset($datosModificados['rol_id']);
            }
            $details['datos_modificados'] = $datosModificados;
        }
    }

    return $details;
    }
}
