<?php

namespace App\Http\Controllers;

use App\Models\HistorialCambio;
use Illuminate\Http\Request;

class HistorialCambioController extends Controller
{
    /**
     * Muestra una lista de los historiales de cambios.
     */
    public function index()
    {
        // Obtener el historial de cambios de todos los documentos
        $historialCambios = HistorialCambio::with(['documento', 'usuario', 'subUsuario'])->get();

        // Retornar la vista con la variable $historialCambios
        return view('historial.index', compact('historialCambios'));
    }



    /**
     * Muestra un historial de cambios específico.
     */
    public function show($id)
    {
        $historialCambio = HistorialCambio::with(['documento', 'usuario', 'subUsuario'])->findOrFail($id);
        return view('historial.show', compact('historialCambio'));
    }

    // Puedes agregar otros métodos según sea necesario
}
