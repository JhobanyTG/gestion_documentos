<?php

namespace App\Http\Controllers;

use App\Models\TipoDocumento;
use Illuminate\Http\Request;

class TipoDocumentoController extends Controller
{
    // Muestra una lista de los registros
    public function index()
    {
        $tipodocumentos = TipoDocumento::all();
        return view('tipodocumento.index', compact('tipodocumentos'));
    }

    // Muestra el formulario para crear un nuevo registro
    public function create()
    {
        return view('tipodocumento.create');
    }

    // Almacena un nuevo registro en la base de datos
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        TipoDocumento::create($request->all());

        return redirect()->route('tipodocumento.index')
            ->with('success', 'Tipo de documento creado con éxito.');
    }

    // Muestra el formulario para editar un registro específico
    public function edit(TipoDocumento $tipodocumento)
    {
        return view('tipodocumento.edit', compact('tipodocumento'));
    }

    // Actualiza un registro específico en la base de datos
    public function update(Request $request, TipoDocumento $tipodocumento)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        $tipodocumento->update($request->all());

        return redirect()->route('tipodocumento.index')
            ->with('success', 'Tipo de documento actualizado con éxito.');
    }

    // Elimina un registro específico de la base de datos
    public function destroy(TipoDocumento $tipodocumento)
    {
        $tipodocumento->delete();

        return redirect()->route('tipodocumento.index')
            ->with('success', 'Tipo de documento eliminado con éxito.');
    }
}
