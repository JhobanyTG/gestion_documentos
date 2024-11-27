<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\Subgerencia;
use App\Models\Gerencia;
use App\Models\User;
use App\Models\TipoDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
{
    try {
        // Aquí cargas los datos que necesitas para el dashboard
        $documentosPorGerencia = Documento::select('gerencia_id', DB::raw('count(*) as total'))
            ->with('gerencia') // Asegúrate de cargar la relación
            ->groupBy('gerencia_id')
            ->get();

        // Documentos por Tipo
        $documentosPorTipo = Documento::with('tipoDocumento')
            ->select('tipodocumento_id', DB::raw('count(*) as total'))
            ->groupBy('tipodocumento_id')
            ->get()
            ->map(function ($item) {
                return [
                    'tipo_documento' => $item->tipoDocumento,
                    'total' => $item->total
                ];
            });

        $usuariosPorRol = User::select('rol_id', DB::raw('count(*) as total'))
            ->with('rol')
            ->groupBy('rol_id')
            ->get()
            ->map(function ($item) {
                return [
                    'rol' => $item->rol,
                    'total' => $item->total
                ];
            });

        // $documentosPorFecha = Documento::select(DB::raw('DATE(created_at) as fecha'), DB::raw('count(*) as total'))
        //     ->groupBy(DB::raw('DATE(created_at)'))
        //     ->get();

        // En tu controlador

        // Documentos del último mes
        $documentosPorFechaUltimoMes = Documento::select(
            DB::raw('DATE(created_at) as fecha'),
            DB::raw('count(*) as total')
        )
            ->whereRaw('created_at >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('fecha')
            ->get();

        // Documentos históricos agrupados por fecha
        $documentosPorFechaHistorico = Documento::select(
            DB::raw('DATE(created_at) as fecha'),
            DB::raw('YEAR(created_at) as anio'),
            DB::raw('MONTH(created_at) as mes'),
            DB::raw('count(*) as total')
        )
            ->groupBy(
                DB::raw('DATE(created_at)'),
                DB::raw('YEAR(created_at)'),
                DB::raw('MONTH(created_at)')
            )
            ->orderBy('fecha')
            ->get();

        // En tu DashboardController, añade:
        $totalDocumentos = Documento::count();
        // $totalDocumentos = Documento::count();
        $totalUsuarios = User::count();
        $totalGerencias = Gerencia::count();
        $totalSubgerencias = Subgerencia::count();

        $documentosPorMes = Documento::select(DB::raw('YEAR(created_at) as año'), DB::raw('MONTH(created_at) as mes'), DB::raw('count(*) as total'))
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
            ->get();

        $documentosPorEstado = Documento::select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->get();

        // Retorna la vista con las variables cargadas
        return view('dashboard.index', compact(
            'documentosPorGerencia',
            'documentosPorTipo',
            'documentosPorFechaUltimoMes',
            'documentosPorFechaHistorico',
            'totalDocumentos',
            'totalUsuarios',
            'totalGerencias',
            'totalSubgerencias',
            'documentosPorMes',
            'documentosPorEstado',
            'usuariosPorRol'
        ));
    } catch (\Exception $e) {
        // Registro de error en el log y dd para diagnóstico
        Log::error('Error en DashboardController@index: ' . $e->getMessage());
        dd('Error: No se pudo cargar el dashboard', $e->getMessage());

        // Redirigir a la vista con un mensaje de error para el usuario
        return view('dashboard.index')->with('error', 'Hubo un problema al cargar los datos del dashboard.');
    }
}
}
