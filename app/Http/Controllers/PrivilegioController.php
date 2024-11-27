<?php

namespace App\Http\Controllers;

use App\Models\Privilegio;
use Illuminate\Http\Request;

class PrivilegioController extends Controller
{
    // Mostrar una lista de los privilegios
    public function index()
    {
        $privilegios = Privilegio::all();
        return view('privilegio.index', compact('privilegios'));
    }

    // Mostrar el formulario para crear un nuevo privilegio
    public function create()
    {
        return view('privilegio.create');
    }

    // Almacenar un nuevo privilegio en la base de datos
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'required|string|max:1500',
        ]);

        Privilegio::create($request->all());

        return redirect()->route('privilegios.index')->with('success', 'Privilegio creado exitosamente.');
    }

    // Mostrar los detalles de un privilegio específico
    public function show(Privilegio $privilegio)
    {
        return view('privilegios.show', compact('privilegio'));
    }

    // Mostrar el formulario para editar un privilegio existente
    public function edit(Privilegio $privilegio)
    {
        return view('privilegio.edit', compact('privilegio'));
    }

    // Actualizar un privilegio existente en la base de datos
    public function update(Request $request, Privilegio $privilegio)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'required|string|max:1500',
        ]);

        $privilegio->update($request->all());

        return redirect()->route('privilegios.index')->with('success', 'Privilegio actualizado exitosamente.');
    }

    // Eliminar un privilegio existente de la base de datos
    public function destroy(Privilegio $privilegio)
    {
        $privilegio->delete();

        return redirect()->route('privilegios.index')->with('success', 'Privilegio eliminado exitosamente.');
    }
}
