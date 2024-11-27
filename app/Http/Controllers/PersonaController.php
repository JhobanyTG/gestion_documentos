<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Models\Subgerencia;
use App\Models\Gerencia;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PersonaController extends Controller
{

    public function index()
    {
        $user = auth()->user();
        $query = Persona::with('user');

        // Verificar si el usuario tiene el rol de 'SuperAdmin'
        if ($user->rol->nombre == 'SuperAdmin') {
            // Si es 'SuperAdmin', mostrar todas las personas sin restricciones
            $personas = $query->get();
        } else {
            // Filtrar personas según la gerencia o subgerencia del usuario
            if ($user->subusuario) {
                // Si es un subusuario, obtener su subgerencia y gerencia
                $subgerencia = $user->subusuario->subgerencia;
                $gerencia = $subgerencia->gerencia;

                // Filtrar personas cuyos usuarios pertenezcan a la misma subgerencia o gerencia
                $query->whereHas('user', function ($q) use ($subgerencia, $gerencia) {
                    $q->whereHas('subusuario.subgerencia', function ($q) use ($subgerencia) {
                        $q->where('id', $subgerencia->id);
                    })->orWhereHas('gerencia', function ($q) use ($gerencia) {
                        $q->where('id', $gerencia->id); // Filtrar por gerencia si es relevante
                    });
                });
            } elseif ($user->gerencia) {
                // Si el usuario está directamente asociado a una gerencia
                $gerencia = $user->gerencia;

                // Filtrar personas cuyos usuarios pertenezcan a la misma gerencia o subgerencias relacionadas
                $query->whereHas('user', function ($q) use ($gerencia) {
                    // Filtrar por la gerencia del usuario
                    $q->whereHas('gerencia', function ($q) use ($gerencia) {
                        $q->where('id', $gerencia->id); // Acceder correctamente a gerencias.id a través de la relación
                    })
                        // Incluir también los subusuarios de las subgerencias de esa gerencia
                        ->orWhereHas('subusuario.subgerencia', function ($q) use ($gerencia) {
                            $q->where('gerencia_id', $gerencia->id);
                        });
                });
            } else {
                // Si no tiene una gerencia ni subgerencia asociada, mostrar solo su propia persona
                $query->whereHas('user', function ($q) use ($user) {
                    $q->where('id', $user->id);
                });
            }

            $personas = $query->get();
        }

        return view('persona.index', compact('personas'));
    }



    public function create()
    {
        // Verificar si el usuario tiene el privilegio de "Acceso Total"
        if (!auth()->user()->rol->privilegios->contains('nombre', 'Acceso Total')) {
            abort(403, 'Acceso denegado');
        }

        // Cargar los roles si se pasa la verificación de acceso
        $roles = Rol::all();
        return view('persona.create', compact('roles'));
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'dni' => 'required|integer',
            'nombres' => 'required|string|max:100',
            'apellido_p' => 'required|string|max:50',
            'apellido_m' => 'required|string|max:50',
            'f_nacimiento' => 'required|date',
            'celular' => 'required|string|max:15',
            'direccion' => 'required|string|max:200',
            'rol_id' => 'required|integer',
            'nombre_usuario' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:users,email',
            'password' => 'required|string|min:8',
            'estado' => 'required|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Manejo del archivo de avatar
        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $timestamp = time();
            $filename = $originalName . '-' . $timestamp . '.' . $extension;

            // Asegúrate de que el nombre del archivo sea único
            while (Storage::disk('public')->exists('avatars/' . $filename)) {
                $timestamp++;
                $filename = $originalName . '-' . $timestamp . '.' . $extension;
            }

            $avatarPath = $file->storeAs('avatars', $filename, 'public');
        }

        $persona = Persona::create([
            'dni' => $validatedData['dni'],
            'nombres' => $validatedData['nombres'],
            'apellido_p' => $validatedData['apellido_p'],
            'apellido_m' => $validatedData['apellido_m'],
            'f_nacimiento' => $validatedData['f_nacimiento'],
            'celular' => $validatedData['celular'],
            'direccion' => $validatedData['direccion'],
            'avatar' => $avatarPath,
        ]);

        $user = User::create([
            'rol_id' => $validatedData['rol_id'],
            'persona_id' => $persona->id,
            'nombre_usuario' => $validatedData['nombre_usuario'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'estado' => $validatedData['estado'],
        ]);

        return redirect()->route('personas.index')->with('success', 'Persona y Usuario creados exitosamente.');
    }


    public function update(Request $request, $id)
    {
        // Validar los datos del formulario
        $request->validate([
            'dni' => 'required|string|max:20',
            'nombres' => 'required|string|max:100',
            'apellido_p' => 'required|string|max:100',
            'apellido_m' => 'required|string|max:100',
            'f_nacimiento' => 'required|date',
            'celular' => 'required|string|max:20',
            'direccion' => 'required|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $persona = Persona::findOrFail($id);
        // Manejo del archivo del avatar
        $avatarPath = $persona->avatar; // Mantiene el avatar actual si no se sube uno nuevo
        if ($request->hasFile('avatar')) {
            // Elimina el avatar antiguo si existe
            if ($persona->avatar && Storage::exists('public/' . $persona->avatar)) {
                Storage::delete('public/' . $persona->avatar);
            }

            $file = $request->file('avatar');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $timestamp = time();
            $filename = $originalName . '-' . $timestamp . '.' . $extension;

            // Asegúrate de que el nombre del archivo sea único
            while (Storage::disk('public')->exists('avatars/' . $filename)) {
                $timestamp++;
                $filename = $originalName . '-' . $timestamp . '.' . $extension;
            }

            $avatarPath = $file->storeAs('avatars', $filename, 'public');
        }



        $persona->update([
            'dni' => $request->input('dni'),
            'nombres' => $request->input('nombres'),
            'apellido_p' => $request->input('apellido_p'),
            'apellido_m' => $request->input('apellido_m'),
            'f_nacimiento' => $request->input('f_nacimiento'),
            'celular' => $request->input('celular'),
            'direccion' => $request->input('direccion'),
            'avatar' => $avatarPath,
        ]);

        // $user = User::where('persona_id', $id)->firstOrFail();
        // $userData = [
        //     'rol_id' => $request->input('rol_id'),
        //     'nombre_usuario' => $request->input('nombre_usuario'),
        //     'email' => $request->input('email'),
        //     'estado' => $request->input('estado'),
        // ];

        // $user->update($userData);

        return redirect()->route('personas.index')->with('success', 'Persona actualizada exitosamente.');
    }


    public function edit($id)
    {
        // Obtener la persona con el ID proporcionado
        $persona = Persona::findOrFail($id);

        // Obtener el usuario asociado a la persona
        $user = User::where('persona_id', $id)->firstOrFail();

        // Obtener los roles disponibles
        $roles = Rol::all();

        // Obtener el usuario autenticado
        $usuarioAutenticado = auth()->user();

        // Verificar si el usuario es SubUsuario
        if ($usuarioAutenticado->rol->nombre === 'SubUsuario') {
            // Solo permitir editar su propio registro
            if ($usuarioAutenticado->id == $persona->id) {
                return view('persona.edit', compact('persona', 'roles', 'user'));
            }
            // Si no es su propio registro, denegar acceso
            abort(403, 'No tienes permiso para editar este registro.');
        }

        // Verificar si el usuario tiene el privilegio de "Acceso Total"
        if ($usuarioAutenticado->rol->privilegios->contains('nombre', 'Acceso Total')) {
            return view('persona.edit', compact('persona', 'roles', 'user'));
        }

        // Verificar si el usuario está editando su propia persona
        if ($usuarioAutenticado->persona_id === $persona->id) {
            return view('persona.edit', compact('persona', 'roles', 'user'));
        }

        // Verificar si el usuario autenticado es gerente de alguna gerencia
        $gerenciaDelGerente = Gerencia::where('usuario_id', $usuarioAutenticado->id)->first();

        if ($gerenciaDelGerente) {
            // Verificar si el usuario asociado a la persona pertenece a su gerencia
            if ($user->gerencia && $user->gerencia->id === $gerenciaDelGerente->id) {
                return view('persona.edit', compact('persona', 'roles', 'user'));
            }

            // Verificar si el usuario asociado a la persona pertenece a alguna subgerencia de su gerencia
            if (
                $user->subusuario &&
                $user->subusuario->subgerencia &&
                $user->subusuario->subgerencia->gerencia_id === $gerenciaDelGerente->id
            ) {
                return view('persona.edit', compact('persona', 'roles', 'user'));
            }
        }

        // Verificar si el usuario autenticado es subgerente de la misma subgerencia
        if (
            $user->subusuario &&
            $usuarioAutenticado->subusuario &&
            $usuarioAutenticado->subusuario->subgerencia_id === $user->subusuario->subgerencia_id
        ) {
            return view('persona.edit', compact('persona', 'roles', 'user'));
        }

        // Si ninguna de las condiciones se cumple, denegar el acceso
        abort(403, 'No tienes permiso para editar esta persona.');
    }



    public function show($id)
    {
        // Obtener la persona con el ID proporcionado y su usuario asociado
        $persona = Persona::with('user')->findOrFail($id);
        $user = User::where('persona_id', $id)->firstOrFail();

        // Obtener el usuario autenticado
        $usuarioAutenticado = auth()->user();

        // Verificar si el usuario es SubUsuario
        if ($usuarioAutenticado->rol->nombre === 'SubUsuario') {
            // Solo permitir ver su propio registro
            if ($usuarioAutenticado->id == $persona->id) {
                return view('persona.show', compact('persona', 'user'));
            }
            // Si no es su propio registro, denegar acceso
            abort(403, 'No tienes permiso para ver este registro.');
        }

        // Verificar si el usuario tiene el privilegio de "Acceso Total"
        if ($usuarioAutenticado->rol->privilegios->contains('nombre', 'Acceso Total')) {
            return view('persona.show', compact('persona', 'user'));
        }

        // Verificar si el usuario está viendo su propia persona
        if ($usuarioAutenticado->persona_id === $persona->id) {
            return view('persona.show', compact('persona', 'user'));
        }

        // Verificar si el usuario autenticado es gerente de alguna gerencia
        $gerenciaDelGerente = Gerencia::where('usuario_id', $usuarioAutenticado->id)->first();

        if ($gerenciaDelGerente) {
            // Verificar si el usuario asociado a la persona pertenece a su gerencia
            if ($user->gerencia && $user->gerencia->id === $gerenciaDelGerente->id) {
                return view('persona.show', compact('persona', 'user'));
            }

            // Verificar si el usuario asociado a la persona pertenece a alguna subgerencia de su gerencia
            if (
                $user->subusuario &&
                $user->subusuario->subgerencia &&
                $user->subusuario->subgerencia->gerencia_id === $gerenciaDelGerente->id
            ) {
                return view('persona.show', compact('persona', 'user'));
            }
        }

        // Verificar si el usuario autenticado es subgerente de la misma subgerencia
        if (
            $user->subusuario &&
            $usuarioAutenticado->subusuario &&
            $usuarioAutenticado->subusuario->subgerencia_id === $user->subusuario->subgerencia_id
        ) {
            return view('persona.show', compact('persona', 'user'));
        }

        // Si ninguna de las condiciones se cumple, denegar el acceso
        abort(403, 'No tienes permiso para ver esta persona.');
    }


    public function destroy($id)
    {
        $persona = Persona::findOrFail($id);
        // Elimina la imagen asociada si existe
        if ($persona->avatar && Storage::exists('public/' . $persona->avatar)) {
            Storage::delete('public/' . $persona->avatar);
        }
        $persona->delete();

        return redirect()->route('personas.index')->with('success', 'Persona eliminada exitosamente.');
    }
}
