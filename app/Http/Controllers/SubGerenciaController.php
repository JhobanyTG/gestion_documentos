<?php

namespace App\Http\Controllers;

use App\Models\Gerencia;
use App\Models\Subgerencia;
use App\Models\Subusuario;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Rol;

class SubgerenciaController extends Controller
{

    public function create(Gerencia $gerencia)
    {
        if (auth()->user()->rol->privilegios->contains('nombre', 'Acceso Total') ||  auth()->user()->rol->nombre === 'Gerente') {

            // Obtener el ID del usuario autenticado
            $usuarioId = Auth::id();

            // Obtener el usuario de la gerencia y usuarios con rol SuperAdmin
            $users = User::where('id', $gerencia->usuario_id)
                ->orWhereHas('rol', function ($query) {
                    $query->where('nombre', 'SuperAdmin');
                })
                ->with('persona')
                ->get();

            // Verificar si el usuario autenticado es el propietario de la gerencia
            if ($gerencia->usuario_id === $usuarioId || auth()->user()->rol->privilegios->contains('nombre', 'Acceso Total')) {
                return view('subgerencias.create', compact('gerencia', 'users'));
            }

            // Verificar si el usuario es un subusuario relacionado con alguna subgerencia de la gerencia
            $subusuario = Subusuario::whereHas('subgerencia', function ($query) use ($gerencia) {
                $query->where('gerencia_id', $gerencia->id);
            })->where('user_id', $usuarioId)->first();

            if ($subusuario) {
                return view('subgerencias.create', compact('gerencia', 'users'));
            }

            // Si no pertenece ni a la gerencia ni a una subgerencia, denegar acceso
            abort(403, 'No tienes permiso para acceder a esta gerencia.');
        } else {
            // Si no tiene los permisos, bloquea el acceso
            abort(403, 'No tienes permiso para realizar esta acción');
        }
    }


    public function store(Request $request, Gerencia $gerencia)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'required|string|max:500',
            'telefono' => 'required|string',
            'direccion' => 'required|string|max:100',
            'estado' => 'required|string|max:20',
            'usuario_id' => 'required|exists:users,id',
        ]);

        // Verificar si el usuario seleccionado es un subusuario
        $esSubusuario = Subusuario::where('user_id', $request->usuario_id)
            ->whereHas('subgerencia', function ($query) use ($gerencia) {
                $query->where('gerencia_id', $gerencia->id);
            })->exists();

        // Crear la subgerencia
        $subgerencia = $gerencia->subgerencias()->create([
            'usuario_id' => $request->usuario_id,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'estado' => $request->estado,
        ]);

        // Solo si es subusuario, actualizar su rol a Subgerente
        if ($esSubusuario) {
            $usuario = User::find($request->usuario_id);
            $rolSubgerente = Rol::where('nombre', 'Subgerente')->first();

            if ($rolSubgerente) {
                // Actualizar el rol del usuario
                $usuario->update(['rol_id' => $rolSubgerente->id]);
            }

            // Actualizar o crear el registro en subusuarios
            Subusuario::updateOrCreate(
                ['user_id' => $usuario->id],
                [
                    'subgerencia_id' => $subgerencia->id,
                    'cargo' => 'Subgerente'
                ]
            );
        }

        return redirect()->route('gerencias.show', $gerencia->id)
            ->with('success', 'Subgerencia creada correctamente.');
    }

    // public function edit(Gerencia $gerencia)
    // {
    //     // Consulta para obtener todos los usuarios que pueden ser seleccionados como gerentes
    //     $users = User::with('persona')->get();

    //     // Retornamos la vista de edición pasando la gerencia y la lista de usuarios
    //     return view('gerencias.edit', compact('gerencia', 'users'));
    // }

    public function edit(Gerencia $gerencia, Subgerencia $subgerencia)
    {
        // Primero verificar que la subgerencia pertenezca a la gerencia
        if ($subgerencia->gerencia_id !== $gerencia->id) {
            abort(404, 'La subgerencia no pertenece a esta gerencia.');
        }

        $usuario = auth()->user();

        // Verificar si el usuario tiene acceso
        $tieneAcceso =
            $usuario->rol->nombre === 'SuperAdmin' || // Es SuperAdmin
            $gerencia->usuario_id === $usuario->id || // Es el gerente asignado
            $subgerencia->usuario_id === $usuario->id; // Es el subgerente asignado

        if (!$tieneAcceso) {
            abort(403, 'No tienes permiso para editar esta subgerencia.');
        }

        // Obtener los usuarios que pueden ser asignados como subgerentes
        $users = User::where(function ($query) use ($gerencia, $subgerencia) {
            $query->where('id', $gerencia->usuario_id) // El gerente
                ->orWhereHas('subusuario', function ($subQuery) use ($subgerencia) {
                    $subQuery->where('subgerencia_id', $subgerencia->id);
                })
                ->orWhere('id', $subgerencia->usuario_id) // El subgerente actual
                ->orWhereHas('rol', function ($query) {
                    $query->where('nombre', 'SuperAdmin');
                });
        })
            ->with('persona')
            ->distinct()
            ->get()
            ->sortByDesc(function ($user) use ($subgerencia) {
                return $user->id === $subgerencia->usuario_id ? 1 : 0;
            });

        return view('subgerencias.edit', compact('gerencia', 'subgerencia', 'users'));
    }

    public function update(Request $request, Gerencia $gerencia, Subgerencia $subgerencia)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'required|string|max:500',
            'telefono' => 'required|string',
            'direccion' => 'required|string|max:100',
            'estado' => 'required|string|max:20',
            'usuario_id' => 'required|exists:users,id',
        ]);

        // Si el usuario asignado está cambiando
        if ($subgerencia->usuario_id != $request->usuario_id) {
            // Manejar el usuario anterior si es un subusuario
            $anteriorUsuario = User::find($subgerencia->usuario_id);
            $esAnteriorSubusuario = Subusuario::where('user_id', $subgerencia->usuario_id)
                ->whereHas('subgerencia', function ($query) use ($gerencia) {
                    $query->where('gerencia_id', $gerencia->id);
                })->exists();

            if ($esAnteriorSubusuario && $anteriorUsuario) {
                // Cambiar el rol del usuario anterior a Subusuario
                $rolSubusuario = Rol::where('nombre', 'SubUsuario')->first();
                if ($rolSubusuario) {
                    $anteriorUsuario->update(['rol_id' => $rolSubusuario->id]);
                }

                // Actualizar su registro en subusuarios
                Subusuario::where('user_id', $anteriorUsuario->id)
                    ->where('subgerencia_id', $subgerencia->id)
                    ->update(['cargo' => 'SubUsuario']);
            }

            // Manejar el nuevo usuario si es un subusuario
            $esNuevoSubusuario = Subusuario::where('user_id', $request->usuario_id)
                ->whereHas('subgerencia', function ($query) use ($gerencia) {
                    $query->where('gerencia_id', $gerencia->id);
                })->exists();

            if ($esNuevoSubusuario) {
                // Actualizar el rol del nuevo usuario a Subgerente
                $nuevoUsuario = User::find($request->usuario_id);
                $rolSubgerente = Rol::where('nombre', 'SubGerente')->first();

                if ($rolSubgerente) {
                    $nuevoUsuario->update(['rol_id' => $rolSubgerente->id]);
                }

                // Actualizar o crear el registro en subusuarios
                Subusuario::updateOrCreate(
                    ['user_id' => $request->usuario_id],
                    [
                        'subgerencia_id' => $subgerencia->id,
                        'cargo' => 'SubGerente'
                    ]
                );
            }
        }

        // Actualizar la subgerencia
        $subgerencia->update($request->all());

        return redirect()->route('gerencias.show', $gerencia->id)
            ->with('success', 'Subgerencia actualizada correctamente.');
    }

    public function destroy(Gerencia $gerencia, Subgerencia $subgerencia)
    {
        if (auth()->user()->rol->privilegios->contains('nombre', 'Acceso Total') || auth()->user()->rol->privilegios->contains('nombre', 'Acceso a Gerencia')) {
            $subgerencia->delete();
            return redirect()->route('gerencias.show', $gerencia->id)->with('success', 'Subgerencia eliminada correctamente.');
        } else {
            // Si no tiene los permisos, bloquea el acceso
            abort(403, 'No tienes permiso para realizar esta acción');
        }
    }

    public function show($id)
    {
        // Obtener la gerencia
        $gerencia = Gerencia::find($id);

        // Verificar si la gerencia existe
        if (!$gerencia) {
            abort(404, 'Gerencia no encontrada.');
        }

        // Obtener el ID del usuario autenticado
        $usuarioId = auth()->id();

        // Verificar si el usuario es propietario o subgerente
        $isOwner = $gerencia->usuario_id === $usuarioId;
        $isSubgerente = Subusuario::whereHas('subgerencia', function ($query) use ($gerencia) {
            $query->where('gerencia_id', $gerencia->id);
        })->where('usuario_id', $usuarioId)->exists();

        if (!$isOwner && !$isSubgerente) {
            return redirect()->back()->with('error', 'No tienes gerencias asignadas.');
        }

        // Si es propietario o subgerente, mostrar la vista
        return view('gerencias.show', compact('gerencia'));
    }
}
