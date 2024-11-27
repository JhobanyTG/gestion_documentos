<?php

namespace App\Http\Controllers;

use App\Models\BackupAccion;
use Illuminate\Http\Request;

class BackupAccionesController extends Controller
{
    public function index()
    {
        $acciones = BackupAccion::orderBy('created_at', 'desc')->get(); // Quitar paginate()
        return view('backup-acciones.index', compact('acciones'));
    }
    // public function show(BackupAccion $backupAccion)
    // {
    //     return view('backup-acciones.show', compact('backupAccion'));
    // }

    // No necesitamos los otros métodos ya que este es un log de solo lectura
}
