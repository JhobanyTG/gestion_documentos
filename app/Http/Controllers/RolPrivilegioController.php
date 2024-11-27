<?php

namespace App\Http\Controllers;

use App\Models\RolPrivilegio;
use App\Models\Rol;
use App\Models\Privilegio;
use Illuminate\Http\Request;

class RolPrivilegioController extends Controller
{
    public function index()
    {
        $rolprivilegios = RolPrivilegio::with(['rol', 'privilegio'])->get();
        return view('rolprivilegio.index', compact('rolprivilegios'));
    }

    public function create()
    {
        $roles = Rol::all();
        $privilegios = Privilegio::all();
        return view('rolprivilegio.create', compact('roles', 'privilegios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'privilegio_id' => 'required|exists:privilegios,id',
            'rol_id' => 'required|exists:rols,id',
        ]);

        RolPrivilegio::create($request->all());

        return redirect()->route('rolprivilegios.index')
            ->with('success', 'RolPrivilegio creado con éxito.');
    }

    public function show(RolPrivilegio $rolprivilegio)
    {
        return view('rolprivilegio.show', compact('rolprivilegio'));
    }

    public function edit(RolPrivilegio $rolprivilegio)
    {
        $roles = Rol::all();
        $privilegios = Privilegio::all();
        return view('rolprivilegio.edit', compact('rolprivilegio', 'roles', 'privilegios'));
    }

    public function update(Request $request, RolPrivilegio $rolprivilegio)
    {
        $request->validate([
            'privilegio_id' => 'required|exists:privilegios,id',
            'rol_id' => 'required|exists:rols,id',
        ]);

        $rolprivilegio->update($request->all());

        return redirect()->route('rolprivilegios.index')
            ->with('success', 'RolPrivilegio actualizado con éxito.');
    }

    public function destroy(RolPrivilegio $rolprivilegio)
    {
        $rolprivilegio->delete();

        return redirect()->route('rolprivilegios.index')
            ->with('success', 'RolPrivilegio eliminado con éxito.');
    }
}
