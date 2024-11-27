<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Rol;
use App\Models\Gerencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = User::with('persona', 'rol');

        // Verificar si el usuario tiene el rol de 'SuperAdmin'
        if ($user->rol->nombre == 'SuperAdmin') {
            // Si es 'SuperAdmin', mostrar todos los usuarios sin restricciones
            $users = $query->get();
        } else {
            // Filtrar usuarios según la gerencia o subgerencia del usuario autenticado
            if ($user->subusuario) {
                // Si es un subusuario, obtener su subgerencia y gerencia
                $subgerencia = $user->subusuario->subgerencia;
                $gerencia = $subgerencia->gerencia;

                // Filtrar usuarios que pertenezcan a la misma subgerencia o gerencia
                $query->whereHas('subusuario.subgerencia', function ($q) use ($subgerencia) {
                    $q->where('id', $subgerencia->id);
                })->orWhereHas('gerencia', function ($q) use ($gerencia) {
                    $q->where('id', $gerencia->id);
                });
            } elseif ($user->gerencia) {
                // Si el usuario está directamente asociado a una gerencia
                $gerencia = $user->gerencia;

                // Filtrar usuarios que pertenezcan a la misma gerencia o subgerencias relacionadas
                $query->whereHas('gerencia', function ($q) use ($gerencia) {
                    $q->where('id', $gerencia->id);
                })
                    ->orWhereHas('subusuario.subgerencia', function ($q) use ($gerencia) {
                        $q->where('gerencia_id', $gerencia->id);
                    });
            } else {
                // Si no tiene una gerencia ni subgerencia asociada, mostrar solo su propio usuario
                $query->where('id', $user->id);
            }

            $users = $query->get();
        }

        return view('user.index', compact('users'));
    }


    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Rol::all();
        $usuarioAutenticado = auth()->user();

        // Verificar si el usuario es SubUsuario
        if ($usuarioAutenticado->rol->nombre === 'SubUsuario') {
            // Solo permitir editar su propio registro
            if ($usuarioAutenticado->id == $user->id) {
                return view('user.edit', compact('user', 'roles'));
            }
            // Si no es su propio registro, denegar acceso
            abort(403, 'No tienes permiso para editar este registro.');
        }

        // Verificar si el usuario tiene el privilegio de "Acceso Total"
        if ($usuarioAutenticado->rol->privilegios->contains('nombre', 'Acceso Total')) {
            return view('user.edit', compact('user', 'roles'));
        }

        // Verificar si el usuario está editando su propio registro
        if ($usuarioAutenticado->id === $user->id) {
            return view('user.edit', compact('user', 'roles'));
        }

        // Verificar si el usuario autenticado es gerente de alguna gerencia
        $gerenciaDelGerente = Gerencia::where('usuario_id', $usuarioAutenticado->id)->first();

        if ($gerenciaDelGerente) {
            // Verificar usuarios en su gerencia
            if ($user->gerencia && $user->gerencia->id === $gerenciaDelGerente->id) {
                return view('user.edit', compact('user', 'roles'));
            }

            // Verificar usuarios en las subgerencias de su gerencia
            if (
                $user->subusuario &&
                $user->subusuario->subgerencia &&
                $user->subusuario->subgerencia->gerencia_id === $gerenciaDelGerente->id
            ) {
                return view('user.edit', compact('user', 'roles'));
            }
        }

        // Verificar si el usuario autenticado es subgerente de la misma subgerencia del usuario a editar
        if (
            $user->subusuario &&
            $usuarioAutenticado->subusuario &&
            $usuarioAutenticado->subusuario->subgerencia_id === $user->subusuario->subgerencia_id
        ) {
            return view('user.edit', compact('user', 'roles'));
        }

        // Si ninguna de las condiciones se cumple, denegar el acceso
        abort(403, 'No tienes permiso para editar este usuario.');
    }



    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'rol_id' => 'required|integer',
            'nombre_usuario' => 'required|string|max:100',
            'email' => 'required|string|email|max:254',
            'estado' => 'required|string|in:Activo,Inactivo',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validación para el avatar
        ]);

        try {
            $user = User::findOrFail($id);
            $persona = $user->persona; // Relación con la persona

            // Si se ha subido un nuevo avatar
            if ($request->hasFile('avatar')) {
                // Elimina el avatar viejo si existe
                if ($persona->avatar && Storage::exists('public/' . $persona->avatar)) {
                    Storage::delete('public/' . $persona->avatar);
                }

                // Guardar el nuevo avatar
                $file = $request->file('avatar');
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $timestamp = time();
                $filename = $originalName . '-' . $timestamp . '.' . $extension;

                // Asegurarse de que el nombre del archivo sea único
                while (Storage::disk('public')->exists('avatars/' . $filename)) {
                    $timestamp++;
                    $filename = $originalName . '-' . $timestamp . '.' . $extension;
                }

                $avatarPath = $file->storeAs('avatars', $filename, 'public');
                $persona->avatar = $avatarPath; // Actualizar el avatar en la tabla de persona
                $persona->save(); // Guardar cambios en persona
            }

            // Actualizar la información del usuario
            $user->update([
                'rol_id' => $validatedData['rol_id'],
                'nombre_usuario' => $validatedData['nombre_usuario'],
                'email' => $validatedData['email'],
                'estado' => $validatedData['estado'],
            ]);

            return redirect()->route('usuarios.index')->with('success', 'Usuario y avatar actualizados exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocurrió un error al actualizar el usuario.');
        }
    }


    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('user.show', compact('user'));
    }

    public function destroy($id)
    {
        $users = User::findOrFail($id);
        $users->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado exitosamente.');
    }

    public function cambiarContrasena($id)
    {
        $userAuth = auth()->user();          // Usuario autenticado
        $user = User::findOrFail($id);       // Usuario cuyo ID se recibe en el método

        // Permitir si el usuario autenticado es 'SuperAdmin'
        if ($userAuth->rol->nombre === 'SuperAdmin') {
            return view('user.cambiarContrasena', compact('user'));
        }

        // Permitir si el usuario autenticado es el mismo que el usuario a modificar
        if ($userAuth->id === $user->id) {
            return view('user.cambiarContrasena', compact('user'));
        }

        // Permitir si el usuario autenticado es gerente de la misma gerencia del usuario
        if ($userAuth->rol->nombre === 'Gerente' && $userAuth->gerencia && $user->gerencia && $userAuth->gerencia->id === $user->gerencia->id) {
            return view('user.cambiarContrasena', compact('user'));
        }

        // Permitir si el usuario autenticado es subgerente de la misma subgerencia del usuario
        if ($userAuth->rol->nombre === 'Subgerente' && $userAuth->subusuario && $user->subusuario && $userAuth->subusuario->subgerencia->id === $user->subusuario->subgerencia->id) {
            return view('user.cambiarContrasena', compact('user'));
        }

        // Bloquear acceso si ninguna de las condiciones anteriores se cumple
        abort(403, 'No tienes permiso para cambiar la contraseña de este usuario');
    }


    public function actualizarContrasena(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:8',
        ]);

        $user = User::findOrFail($id);
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('usuarios.index')->with('success', 'Contraseña actualizada exitosamente.');
    }
}
