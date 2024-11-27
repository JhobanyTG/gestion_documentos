<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Gerencia;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');

    }

    public function login(Request $request)
    {
        // Lógica de inicio de sesión
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect()->intended('/documentos');
        } else {
            return redirect()->back()->withErrors(['email' => 'Credenciales incorrectas']);
        }
    }

    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    public function changePassword(Request $request)
    {
        // Lógica de cambio de contraseña
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|different:current_password',
            'confirm_password' => 'required|same:new_password',
        ]);

        $user = Auth::user();
        if (Hash::check($request->current_password, $user->password)) {
            $user->update(['password' => Hash::make($request->new_password)]);
            return redirect()->route('documentos.index')->with('success', 'Contraseña cambiada con éxito');
        } else {
            return redirect()->back()->withErrors(['current_password' => 'La contraseña actual es incorrecta']);
        }
    }

    public function showRegisterForm()
    {
        // Solo permitir el registro si el usuario actual es un administrador
        if (Auth::check() && Auth::user()->role === 'SuperAdmin') {
            return view('auth.register');
        } else {
            return redirect()->route('login')->withErrors(['Unauthorized' => 'No tienes permisos para registrar nuevos usuarios']);
        }
    }

    public function register(Request $request)
    {
        // Lógica de registro
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required|in:user,SuperAdmin',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('dashboard')->with('success', 'Usuario registrado con éxito');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/');
    }

    public function redirectToGerencia()
    {
        $user = Auth::user();

        // Busca la gerencia del usuario
        $gerencia = Gerencia::where('usuario_id', $user->id)->first();

        // Verifica que la gerencia exista
        if ($gerencia) {
            // Redirige a la ruta de la gerencia con el ID correcto
            return redirect()->route('gerencias.show', ['gerencia' => $gerencia->id]);
        } else {
            return redirect()->back()->withErrors(['error' => 'No tienes ninguna gerencia asignada']);
        }
    }


}
