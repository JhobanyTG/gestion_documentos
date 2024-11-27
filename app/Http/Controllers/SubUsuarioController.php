<?php

namespace App\Http\Controllers;

use App\Models\Gerencia;
use App\Models\Subgerencia;
use App\Models\Subusuario;
use App\Models\User;
use App\Models\Rol;
use App\Models\Persona;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;



class SubUsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Gerencia $gerencia, Subgerencia $subgerencia)
    {
        // Obtén todos los subusuarios de la subgerencia específica
        $subusuarios = $subgerencia->subusuarios()->with('user.persona')->get();

        return view('subusuarios.index', compact('gerencia', 'subgerencia', 'subusuarios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Gerencia $gerencia)
    {
        $usuario = auth()->user();

        if ($usuario->rol->privilegios->contains('nombre', 'Acceso Total') || $usuario->rol->nombre === 'Gerente' || $usuario->rol->nombre === 'SubGerente') {

            // Obtener el ID del usuario autenticado
            $usuarioId = Auth::id();

            // Verificar si el usuario autenticado es el propietario de la gerencia
            if ($gerencia->usuario_id === $usuarioId) {
                // Obtener todos los roles disponibles
                $roles = Rol::all();
                // Obtener las subgerencias que pertenecen a la gerencia actual
                $subgerencias = Subgerencia::where('gerencia_id', $gerencia->id)->get();
                return view('subusuarios.create', compact('gerencia', 'subgerencias', 'roles'));
            }

            if (auth()->user()->rol->privilegios->contains('nombre', 'Acceso Total')) {
                // Obtener todos los roles disponibles
                $roles = Rol::all();
                // Obtener las subgerencias que pertenecen a la gerencia actual
                $subgerencias = Subgerencia::where('gerencia_id', $gerencia->id)->get();
                return view('subusuarios.create', compact('gerencia', 'subgerencias', 'roles'));
            }

            // Verificar si el usuario es un subusuario relacionado con alguna subgerencia de la gerencia
            $subusuario = Subusuario::whereHas('subgerencia', function ($query) use ($gerencia) {
                $query->where('gerencia_id', $gerencia->id);
            })->where('user_id', $usuarioId)->first();

            if ($subusuario) {
                // Obtener todos los roles disponibles
                $roles = Rol::all();
                // Obtener las subgerencias que pertenecen a la gerencia actual
                $subgerencias = Subgerencia::where('gerencia_id', $gerencia->id)->get();
                return view('subusuarios.create', compact('gerencia', 'subgerencias', 'roles'));
            }

            // Si no pertenece ni a la gerencia ni a una subgerencia, denegar acceso
            abort(403, 'No tienes permiso para acceder a esta gerencia.');
        } else {
            // Si no tiene los permisos, bloquea el acceso
            abort(403, 'No tienes permiso para realizar esta acción');
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Gerencia $gerencia)
    {
        // Validar los datos
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
            'subgerencia_id' => 'required|integer',
            'cargo' => 'required|string|max:100',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Validar la longitud de la contraseña
        if (strlen($request->password) < 8) {
            return redirect()->route('subusuarios.create', $gerencia->id)
                ->withErrors(['password' => 'La contraseña debe tener al menos 8 caracteres.'])
                ->withInput();
        }

        // Crear la persona asociada al subusuario
        $persona = new Persona();
        // Asignar otros campos de persona
        $persona->nombres = $request->nombres;
        $persona->apellido_p = $request->apellido_p;
        $persona->apellido_m = $request->apellido_m;
        $persona->dni = $request->dni;
        $persona->f_nacimiento = $request->f_nacimiento;
        $persona->celular = $request->celular;
        $persona->direccion = $request->direccion;

        // Manejar el avatar
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public'); // Guarda en el directorio de public/avatars
            $persona->avatar = $avatarPath; // Guarda la ruta en el campo 'avatar'
        } else {
            $persona->avatar = null; // Ruta por defecto si no se sube ninguna imagen
        }

        $persona->save(); // Guarda la persona

        // Crear el usuario asociado a la persona
        $user = User::create([
            'rol_id' => $validatedData['rol_id'],
            'persona_id' => $persona->id,
            'nombre_usuario' => $validatedData['nombre_usuario'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'estado' => $validatedData['estado'],
        ]);

        // Crear el subusuario con el user_id recién creado
        Subusuario::create([
            'user_id' => $user->id,
            'subgerencia_id' => $validatedData['subgerencia_id'],
            'cargo' => $validatedData['cargo'],
        ]);
        return redirect()->route('gerencias.show', $gerencia->id)->with('success', 'Subusuario creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Gerencia $gerencia, Subgerencia $subgerencia, Subusuario $subusuario)
    {
        return view('subusuarios.show', compact('gerencia', 'subgerencia', 'subusuario'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Gerencia $gerencia, Subgerencia $subgerencia, Subusuario $subusuario)
    {
        // Obtener el ID del usuario autenticado
        $usuarioId = Auth::id();

        // Permitir al usuario autenticado editar su propio registro de subusuario
        if ($subusuario->user_id === $usuarioId) {
            return $this->loadEditView($gerencia, $subgerencia, $subusuario);
        }

        // Comprobar si el usuario tiene acceso
        if (
            auth()->user()->rol->privilegios->contains('nombre', 'Acceso Total') ||   auth()->user()->rol->nombre === 'Gerente' || auth()->user()->rol->nombre === 'SubGerente'
        ) {

            // Primero verificar que la subgerencia pertenezca a la gerencia
            if ($subusuario->subgerencia->gerencia_id !== $gerencia->id) {
                abort(404, 'La subgerencia no pertenece a esta gerencia.');
            }

            // Verificar si el usuario autenticado es el propietario de la gerencia
            if ($gerencia->usuario_id === $usuarioId) {
                return $this->loadEditView($gerencia, $subgerencia, $subusuario);
            }

            // Verificar si el usuario tiene privilegio de "Acceso Total"
            if (auth()->user()->rol->privilegios->contains('nombre', 'Acceso Total')) {
                return $this->loadEditView($gerencia, $subgerencia, $subusuario);
            }

            // Verificar si el usuario autenticado es un subgerente
            $subusuarioSubgerente = Subusuario::whereHas('subgerencia', function ($query) use ($gerencia) {
                $query->where('gerencia_id', $gerencia->id);
            })
                ->where('user_id', $usuarioId)
                ->where('subgerencia_id', true) // Asumiendo que tienes un campo es_subgerente
                ->first();

            if ($subusuarioSubgerente) {
                // Si es subgerente, solo puede editar subusuarios de su propia subgerencia
                if ($subusuarioSubgerente->subgerencia_id === $subusuario->subgerencia_id) {
                    return $this->loadEditView($gerencia, $subgerencia, $subusuario);
                } else {
                    abort(403, 'No tienes permiso para editar subusuarios de otras subgerencias.');
                }
            }

            // Verificar si el usuario autenticado es un subusuario regular de la gerencia
            $subusuarioRegular = Subusuario::whereHas('subgerencia', function ($query) use ($gerencia) {
                $query->where('gerencia_id', $gerencia->id);
            })
                ->where('user_id', $usuarioId)
                ->where('subgerencia_id', false)
                ->first();

            if ($subusuarioRegular) {
                // Los subusuarios regulares no deberían poder editar a otros subusuarios
                abort(403, 'No tienes permiso para editar subusuarios.');
            }

            // Si no cumple ninguna de las condiciones anteriores, denegar acceso
            abort(403, 'No tienes permiso para acceder a esta gerencia.');
        } else {
            // Si no tiene los permisos básicos, bloquea el acceso
            abort(403, 'No tienes permiso para realizar esta acción');
        }
    }

    private function loadEditView($gerencia, $subgerencia, $subusuario)
    {
        // Obtener todos los roles disponibles
        $roles = Rol::all();
        // Obtener las subgerencias que pertenecen a la gerencia actual
        $subgerencias = Subgerencia::where('gerencia_id', $gerencia->id)->get();
        // Obtener los usuarios disponibles para seleccionar
        $users = User::with('persona')->get();

        return view('subusuarios.edit', compact('gerencia', 'subgerencia', 'subusuario', 'users', 'roles', 'subgerencias'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Gerencia $gerencia, Subgerencia $subgerencia, Subusuario $subusuario)
    {
        $request->validate([
            'dni' => 'required|integer',
            'nombres' => 'required|string|max:100',
            'apellido_p' => 'required|string|max:50',
            'apellido_m' => 'required|string|max:50',
            'f_nacimiento' => 'required|date',
            'celular' => 'required|string|max:15',
            'direccion' => 'required|string|max:200',
            'rol_id' => 'required|integer',
            'nombre_usuario' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:users,email,' . $subusuario->user_id,
            'estado' => 'required|string|max:20',
            'subgerencia_id' => 'required|integer',
            'cargo' => 'required|string|max:100',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048' // Validación para el avatar
        ]);

        // Actualizar la persona
        $persona = $subusuario->user->persona;
        $persona->update([
            'dni' => $request->dni,
            'nombres' => $request->nombres,
            'apellido_p' => $request->apellido_p,
            'apellido_m' => $request->apellido_m,
            'f_nacimiento' => $request->f_nacimiento,
            'celular' => $request->celular,
            'direccion' => $request->direccion,
        ]);

        // Verificar si se ha subido un nuevo avatar
        if ($request->hasFile('avatar')) {
            // Eliminar el avatar anterior si existe y no es el predeterminado
            if ($persona->avatar && $persona->avatar !== 'default.png') {
                Storage::disk('public')->delete($persona->avatar);
            }

            // Obtener el archivo subido
            $avatarFile = $request->file('avatar');

            // Generar un nombre único para el archivo
            $originalName = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $avatarFile->getClientOriginalExtension();
            $timestamp = time();
            $avatarName = $originalName . '-' . $timestamp . '.' . $extension;

            // Asegurarse de que el nombre del archivo sea único
            while (Storage::disk('public')->exists('avatars/' . $avatarName)) {
                $timestamp++;
                $avatarName = $originalName . '-' . $timestamp . '.' . $extension;
            }

            // Mover el archivo al directorio de almacenamiento
            $avatarPath = $avatarFile->storeAs('avatars', $avatarName, 'public');

            // Actualizar el avatar en el modelo Persona
            $persona->update(['avatar' => $avatarPath]);
        }

        // Actualizar el usuario asociado a la persona
        $subusuario->user->update([
            'rol_id' => $request->rol_id,
            'nombre_usuario' => $request->nombre_usuario,
            'email' => $request->email,
            'estado' => $request->estado,
        ]);

        // Actualizar el subusuario
        $subusuario->update([
            'subgerencia_id' => $request->subgerencia_id,
            'cargo' => $request->cargo,
        ]);

        return redirect()->route('gerencias.show', $gerencia->id)->with('success', 'Subusuario actualizado exitosamente.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Gerencia $gerencia, Subgerencia $subgerencia, Subusuario $subusuario)
    {
        if (auth()->user()->rol->privilegios->contains('nombre', 'Acceso Total') || auth()->user()->rol->privilegios->contains('nombre', 'Acceso a Gerencia')) {

            $user = $subusuario->user;
            $persona = $user->persona;

            // Eliminar el subusuario
            $subusuario->delete();

            // Eliminar el usuario y luego la persona asociada
            $user->delete();
            $persona->delete();

            return redirect()->route('gerencias.show', $gerencia->id)->with('success', 'Subusuario eliminado exitosamente.');
        } else {
            // Si no tiene los permisos, bloquea el acceso
            abort(403, 'No tienes permiso para realizar esta acción');
        }
    }
}
