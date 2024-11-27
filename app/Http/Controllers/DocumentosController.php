<?php

namespace App\Http\Controllers;


use App\Models\Documento;
use App\Models\TipoDocumento;
use App\Models\Subusuario;
use App\Models\Subgerencia;
use App\Models\HistorialCambio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;





class DocumentosController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()) {

            $user = auth()->user();

            // Inicializar la consulta
            $query = Documento::with('tipoDocumento', 'gerencia', 'subgerencia');

            $tiposDocumento = TipoDocumento::all(); // Asegúrate de tener el modelo TipoDocumento configurado


            // Aplicar filtros de búsqueda
            $searchTerm = $request->input('q');
            $fecha = $request->input('fecha');
            $filtroAnio = $request->input('anio');
            $filtroMes = $request->input('mes', []);
            $filtroTipoDocumento = $request->input('tipodocumento_id', []);

            // Verificar si el usuario tiene el rol de 'SuperAdmin'
            if ($user->rol->nombre == 'SuperAdmin') {
                // Si es 'SuperAdmin', mostrar todos los documentos sin restricciones
                $documentos = $query->paginate(4);
            } else {
                // Determinar el tipo de usuario y aplicar los filtros correspondientes
                if ($user->subusuario) {
                    // Si es un subusuario
                    $subgerencia = $user->subusuario->subgerencia;
                    $gerencia = $subgerencia->gerencia;

                    $query->where('gerencia_id', $gerencia->id)
                        ->where(function ($q) use ($subgerencia) {
                            $q->where('subgerencia_id', $subgerencia->id)
                                ->orWhereNull('subgerencia_id');
                        });
                } elseif ($user->gerencia) {
                    // Si el usuario está asociado directamente a una gerencia
                    $query->where('gerencia_id', $user->gerencia->id);
                } elseif ($subgerencia = Subgerencia::where('usuario_id', $user->id)->first()) {
                    // Si el usuario está asociado a una subgerencia
                    $query->where('gerencia_id', $subgerencia->gerencia_id)
                        ->where(function ($q) use ($subgerencia) {
                            $q->where('subgerencia_id', $subgerencia->id)
                                ->orWhereNull('subgerencia_id');
                        });
                } else {
                    // Si no tiene una gerencia ni subgerencia asociada, mostrar solo sus propios documentos
                    $query->where('user_id', $user->id);
                }
            }

            if ($searchTerm || $fecha || $filtroAnio || $filtroMes || $filtroTipoDocumento) {
                if ($searchTerm) {
                    $query->where(function ($query) use ($searchTerm) {
                        $query->where('titulo', 'like', '%' . $searchTerm . '%')
                            ->orWhere('descripcion', 'like', '%' . $searchTerm . '%');
                    });
                }

                if ($fecha) {
                    $query->whereDate('created_at', $fecha);
                }

                if ($filtroAnio) {
                    $query->whereYear('created_at', $filtroAnio);
                }

                if ($filtroMes && is_array($filtroMes) && !empty($filtroMes)) {
                    $query->whereIn(DB::raw('MONTH(created_at)'), $filtroMes);
                }

                if (!empty($filtroTipoDocumento)) {
                    $query->where('tipodocumento_id', $filtroTipoDocumento);
                }
            }

            $query->orderByDesc('created_at');
            $documentos = $query->paginate(4);

            $documentos->appends(['q' => $searchTerm, 'fecha' => $fecha, 'anio' => $filtroAnio, 'mes' => $filtroMes, 'tipodocumento_id' => $filtroTipoDocumento]);

            // Obtener años disponibles para el filtro
            $availableYears = Documento::distinct()
                ->orderByDesc('created_at')
                ->pluck('created_at')
                ->map(function ($date) {
                    return $date->format('Y');
                })
                ->unique();

            // Obtener meses disponibles para el filtro en el año seleccionado
            $availableMonths = [];
            if ($filtroAnio) {
                $availableMonths = Documento::selectRaw('MONTH(created_at) as month')
                    ->whereYear('created_at', $filtroAnio)
                    ->groupBy('month')
                    ->pluck('month');
            }

            return view('documentos.index', compact('documentos', 'searchTerm', 'fecha', 'availableYears', 'availableMonths', 'filtroAnio', 'filtroMes', 'tiposDocumento', 'filtroTipoDocumento'));
        } else {
            return redirect()->to('/');
        }
    }

    public function create()
    {

        $user = auth()->user();

        // Bloquear acceso si el rol del usuario es "Usuario Creador"
        if ($user->rol->nombre === 'UsuarioValidador' || $user->rol->nombre === 'UsuarioPublicador') {
            abort(403, 'No tienes permiso para crear documentos');
        }

        if (auth()->user()->rol->privilegios->contains('nombre', 'Acceso Total')  || auth()->user()->rol->nombre === 'Gerente' || auth()->user()->rol->nombre === 'SubGerente' || auth()->user()->rol->privilegios->contains('nombre', 'Acceso a Crear Documento') || auth()->user()->rol->privilegios->contains('nombre', 'Acceso a Documentos')) {
            $tiposDocumento = TipoDocumento::all();
            $subUsuarios = SubUsuario::all(); // Obtén todos los subusuarios

            return view('documentos.create', compact('tiposDocumento', 'subUsuarios'));
        } else {
            // Si no tiene los permisos, bloquea el acceso
            abort(403, 'No tienes permiso para realizar esta acción');
        }
    }



    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'tipodocumento_id' => 'required|exists:tipodocumento,id',
            'archivo' => [
                'required',
                'file',
                'mimes:pdf',
                'max:10000',
                Rule::unique('documentos', 'archivo'),
            ],
            'estado' => 'required|in:Creado,Validado,Publicado',
        ]);

        // Obtener el usuario autenticado
        $user = Auth::user();
        $subusuario = $user->subusuario; // Obtener el subusuario del usuario autenticado

        // Determinar la subgerencia y la gerencia
        $subgerencia = $subusuario ? $subusuario->subgerencia : null; // Obtener la subgerencia si existe
        $gerencia = $subgerencia ? $subgerencia->gerencia : $user->gerencia; // Obtener la gerencia del subgerencia o del usuario

        $fileName = null;
        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            $fileName = time() . '-' . $file->getClientOriginalName();
            $file->storeAs('public/documentos', $fileName);
        }

        Documento::create([
            'sub_usuarios_id' => $subusuario ? $subusuario->id : null, // Asignar el ID del subusuario o null
            'user_id' => $user->id,
            'tipodocumento_id' => $request->input('tipodocumento_id'),
            'titulo' => $request->input('titulo'),
            'descripcion' => $request->input('descripcion'),
            'archivo' => $fileName,
            'estado' => $request->input('estado'),
            'gerencia_id' => $gerencia ? $gerencia->id : null, // Asignar la gerencia
            'subgerencia_id' => $subgerencia ? $subgerencia->id : null, // Asignar la subgerencia o 'NA'
        ]);

        return redirect()->route('documentos.index')->with('success', 'Documento creado exitosamente.');
    }

    public function edit($id)
    {
        $user = auth()->user();

        // Bloquear acceso si el rol del usuario es "Usuario Creador"
        if ($user->rol->nombre === 'UsuarioCreador') {
            abort(403, 'No tienes permiso para editar documentos');
        }

        // Verificar si el documento existe
        $documento = Documento::findOrFail($id);

        // Si el usuario tiene el rol "Usuario Validador", solo permitir editar si el documento está en estado "Creado"
        if ($user->rol->nombre === 'UsuarioValidador' && $documento->estado !== 'Creado') {
            abort(403, 'Solo puedes editar documentos en estado Creado');
        }

        // Si el usuario tiene el rol "Usuario Publicador", solo permitir editar si el documento está en estado "Validado"
        if ($user->rol->nombre === 'UsuarioPublicador' && $documento->estado !== 'Validado') {
            abort(403, 'Solo puedes editar documentos en estado Validado');
        }

        // Verificar si el usuario tiene privilegios o es 'SuperAdmin'
        $privilegiosNecesarios = [
            'Acceso Total',
            'Acceso a Gerencia',
            'Acceso a Documentos',
            'Acceso a Validar Documento',
            'Acceso a Publicar Documento'
        ];

        $tienePrivilegios = $user->rol->privilegios->whereIn('nombre', $privilegiosNecesarios)->isNotEmpty() || $user->rol->nombre === 'SuperAdmin';

        // Si es 'SuperAdmin', permitir el acceso sin verificar gerencias o subgerencias
        if ($user->rol->nombre === 'SuperAdmin') {
            $tiposDocumento = TipoDocumento::all();
            return view('documentos.edit', compact('documento', 'tiposDocumento', 'user'));
        }

        // Obtener gerencia y subgerencia del usuario
        $subgerencia = $user->subusuario ? $user->subusuario->subgerencia : null;
        $gerencia = $subgerencia ? $subgerencia->gerencia : $user->gerencia;

        // Verificar si el usuario es gerente
        $esGerente = $user->rol->nombre === 'Gerente';

        // Verificar si el documento pertenece a la gerencia del usuario
        $perteneceAGerencia = $documento->gerencia_id === ($gerencia ? $gerencia->id : null);

        // Verificar si el documento pertenece a la subgerencia del usuario
        $perteneceASubgerencia = $subgerencia ? $documento->subgerencia_id === $subgerencia->id : false;

        // Validar si el documento es de la gerencia o de la subgerencia
        $esDocumentoValido = $perteneceAGerencia || $perteneceASubgerencia;

        // Permitir acceso si el usuario tiene privilegios y el documento pertenece a su gerencia o subgerencia
        if ($tienePrivilegios && $esDocumentoValido) {
            $tiposDocumento = TipoDocumento::all();
            return view('documentos.edit', compact('documento', 'tiposDocumento', 'user'));
        }

        // Bloquear el acceso si el usuario no tiene permiso para editar el documento
        abort(403, 'No tienes permiso para editar este documento');
    }


    public function update(Request $request, $id)
    {
        $documento = Documento::findOrFail($id);
        $fileName = $documento->archivo;

        $request->validate([
            'titulo' => 'required|string|max:255',
            'tipodocumento_id' => 'required|exists:tipodocumento,id',
            'descripcion' => 'required|string',
            'archivo' => [
                'nullable',
                'file',
                'mimes:pdf',
                'max:10000',
                Rule::unique('documentos', 'archivo')->ignore($documento->id),
            ],
            'sub_usuarios_id' => 'nullable|exists:subusuarios,id',
        ]);

        if ($request->hasFile('archivo')) {
            $archivo = $request->file('archivo');

            // Generar el número incremental
            $ultimoDocumento = Documento::latest('id')->first();
            $numeroIncremental = $ultimoDocumento ? str_pad($ultimoDocumento->id + 1, 4, '0', STR_PAD_LEFT) : '0001';

            // Modificar el nombre del archivo
            $archivoNombreOriginal = $archivo->getClientOriginalName();
            $archivoNombre = 'ATISR-' . $numeroIncremental . '-' . $archivoNombreOriginal;

            // Guardar el archivo con el nuevo nombre
            $archivoRuta = $archivo->storeAs('archivos', $archivoNombre, 'public');

            // Eliminar el archivo anterior si existe
            if ($documento->archivo) {
                Storage::delete('public/' . $documento->archivo);
            }

            $documento->archivo = $archivoRuta;
        }

        // Guardar los datos del documento
        $documento->update([
            'user_id' => Auth::user()->id,
            'sub_usuarios_id' => $request->input('sub_usuarios_id') ?? null,
            'tipodocumento_id' => $request->input('tipodocumento_id'),
            'titulo' => $request->input('titulo'),
            'descripcion' => $request->input('descripcion'),
            'archivo' => $documento->archivo, // Asegúrate de guardar el nuevo nombre de archivo
            'estado' => $documento->estado, // Mantener el estado actual
            'gerencia_id' => Auth::user()->gerencia->id ?? null,
            'subgerencia_id' => Auth::user()->subgerencia->id ?? null,
        ]);

        // // Registrar el cambio de estado en historial_cambios con más detalles
        // HistorialCambio::create([
        //     'documento_id' => $documento->id,
        //     'estado_anterior' => $documento->estado,  // Registrar el estado anterior antes de la actualización
        //     'nuevo_estado' => $request->input('estado'),
        //     'descripcion' => 'Cambio de estado de ' . $documento->estado . ' a ' . $request->input('estado'),
        //     'usuario_id' => Auth::user()->id,
        // ]);


        return redirect()->route('documentos.index')->with('success', 'Documento actualizado exitosamente.');
    }


    public function destroy($id)
    {

        if (auth()->user()->rol->privilegios->contains('nombre', 'Acceso Total')  || auth()->user()->rol->nombre === 'Gerente' || auth()->user()->rol->nombre === 'SubGerente' || auth()->user()->rol->privilegios->contains('nombre', 'Acceso a Documentos')) {
            $documento = Documento::findOrFail($id);
            if ($documento->archivo) {
                Storage::delete('public/documentos/' . $documento->archivo);
            }
            $documento->delete();

            return redirect()->route('documentos.index')
                ->with('success', 'El registro ha sido eliminado exitosamente.');
        } else {
            return redirect()->to('/');
        }
    }

    public function cambiarEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|string',
            'descripcion' => 'required|string',
        ]);

        try {
            // Encuentra el documento por su ID
            $documento = Documento::findOrFail($id);

            // Guarda el estado anterior
            $estadoAnterior = $documento->estado;

            // Cambia el estado del documento
            $documento->estado = $request->estado;
            $documento->save();

            // Crea un nuevo historial de cambios
            HistorialCambio::create([
                'documento_id' => $documento->id,
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => $request->estado,
                'descripcion' => $request->descripcion,
                'user_id' => auth()->user()->id,
                'sub_usuario_id' => auth()->user()->subusuario_id,
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al cambiar el estado.']);
        }
    }

    public function mostrarHistorial(Request $request, $documentoId)
    {
        if (!auth()->user()) {
            return redirect()->to('/');
        }

        $user = auth()->user();

        // Inicializar la consulta del historial de cambios
        $query = HistorialCambio::with('documento.tipoDocumento', 'documento.gerencia', 'documento.subgerencia');

        // Filtros de búsqueda
        $searchTerm = $request->input('q');
        $fecha = $request->input('fecha');
        $filtroAnio = $request->input('anio');
        $filtroMes = $request->input('mes', []);
        $filtroTipoDocumento = $request->input('tipodocumento_id', []);


        // Verificar si el usuario tiene el rol de 'SuperAdmin'
        if ($user->rol->nombre === 'SuperAdmin') {
            // Si es 'SuperAdmin', no aplicar restricciones adicionales
        } else {
            // Determinar el tipo de usuario y aplicar los filtros correspondientes
            if ($user->subusuario) {
                // Si es un subusuario
                $subgerencia = $user->subusuario->subgerencia;
                $gerencia = $subgerencia->gerencia;

                $query->whereHas('documento', function ($q) use ($gerencia, $subgerencia) {
                    $q->where('gerencia_id', $gerencia->id)
                        ->where(function ($q) use ($subgerencia) {
                            $q->where('subgerencia_id', $subgerencia->id)
                                ->orWhereNull('subgerencia_id');
                        });
                });
            } elseif ($user->gerencia) {
                // Si el usuario está asociado directamente a una gerencia
                $query->whereHas('documento', function ($q) use ($user) {
                    $q->where('gerencia_id', $user->gerencia->id);
                });
            } elseif ($subgerencia = Subgerencia::where('usuario_id', $user->id)->first()) {
                // Si el usuario está asociado a una subgerencia
                $query->whereHas('documento', function ($q) use ($subgerencia) {
                    $q->where('gerencia_id', $subgerencia->gerencia_id)
                        ->where(function ($q) use ($subgerencia) {
                            $q->where('subgerencia_id', $subgerencia->id)
                                ->orWhereNull('subgerencia_id');
                        });
                });
            } else {
                // Si no tiene una gerencia ni subgerencia asociada, mostrar solo el historial de sus propios documentos
                $query->whereHas('documento', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            }
        }

        if ($searchTerm || $fecha || $filtroAnio || $filtroMes || $filtroTipoDocumento) {

            // Aplicar filtros de búsqueda si están presentes
            if ($searchTerm) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->whereHas('documento', function ($q) use ($searchTerm) {
                        $q->where('titulo', 'like', '%' . $searchTerm . '%')
                            ->orWhere('descripcion', 'like', '%' . $searchTerm . '%');
                    })
                        ->orWhere('descripcion', 'LIKE', "%{$searchTerm}%")
                        ->orWhereHas('user', function ($q) use ($searchTerm) {
                            $q->where('nombre_usuario', 'LIKE', "%{$searchTerm}%");
                        });
                });
            }

            if ($fecha) {
                $query->whereDate('created_at', $fecha);
            }

            if ($filtroAnio) {
                $query->whereYear('created_at', $filtroAnio);
            }

            if (!empty($filtroMes) && is_array($filtroMes)) {
                $query->whereIn(DB::raw('MONTH(created_at)'), $filtroMes);
            }

            if (!empty($filtroTipoDocumento)) {
                $query->whereHas('documento', function ($q) use ($filtroTipoDocumento) {
                    $q->whereIn('tipodocumento_id', $filtroTipoDocumento);
                });
            }
        }

        // Ordenar por la fecha de creación
        $query->orderByDesc('created_at');

        // Obtener el historial filtrado y paginar los resultados
        $historial = $query->paginate(7);
        $historial->appends(['q' => $searchTerm, 'fecha' => $fecha, 'anio' => $filtroAnio, 'mes' => $filtroMes, 'tipodocumento_id' => $filtroTipoDocumento]);

        // Obtener años disponibles para el filtro
        $availableYears = HistorialCambio::distinct()
            ->orderByDesc('created_at')
            ->pluck('created_at')
            ->map(function ($date) {
                return $date->format('Y');
            })
            ->unique();

        // Obtener meses disponibles para el filtro en el año seleccionado
        $availableMonths = [];
        if ($filtroAnio) {
            $availableMonths = HistorialCambio::selectRaw('MONTH(created_at) as month')
                ->whereYear('created_at', $filtroAnio)
                ->groupBy('month')
                ->pluck('month');
        }

        // Devolver la vista con el historial filtrado y los filtros disponibles
        return view('historial.index', compact('historial', 'searchTerm', 'fecha', 'availableYears', 'availableMonths', 'filtroAnio', 'filtroMes', 'filtroTipoDocumento'));
    }

    public function generarReporte(Request $request)
    {
        $user = Auth::user();

        // Inicializar la consulta
        $query = Documento::with(['tipoDocumento', 'gerencia', 'subgerencia']);

        // Obtener y validar los filtros
        $searchTerm = trim($request->input('q'));
        $fecha = $request->input('fecha');
        $filtroAnio = $request->input('anio');
        $filtroMes = json_decode($request->input('mes'), true);
        $filtroTipoDocumento = json_decode($request->input('tipodocumento_id'), true);


        // Convertir filtroMes a array si es string
        if (!is_array($filtroMes)) {
            $filtroMes = explode(',', $filtroMes);
        }

        // Convertir filtroTipoDocumento a array si es string
        if (!is_array($filtroTipoDocumento)) {
            $filtroTipoDocumento = explode(',', $filtroTipoDocumento);
        }

        // Filtrar según el rol del usuario
        if ($user->rol->nombre != 'SuperAdmin') {
            if ($user->subusuario) {
                $subgerencia = $user->subusuario->subgerencia;
                $gerencia = $subgerencia->gerencia;

                $query->where('gerencia_id', $gerencia->id)
                    ->where(function ($q) use ($subgerencia) {
                        $q->where('subgerencia_id', $subgerencia->id)
                            ->orWhereNull('subgerencia_id');
                    });
            } elseif ($user->gerencia) {
                $query->where('gerencia_id', $user->gerencia->id);
            } elseif ($subgerencia = Subgerencia::where('usuario_id', $user->id)->first()) {
                $query->where('gerencia_id', $subgerencia->gerencia_id)
                    ->where(function ($q) use ($subgerencia) {
                        $q->where('subgerencia_id', $subgerencia->id)
                            ->orWhereNull('subgerencia_id');
                    });
            } else {
                $query->where('user_id', $user->id);
            }
        }

        // Aplicar filtros
        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('titulo', 'like', '%' . $searchTerm . '%')
                    ->orWhere('descripcion', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('gerencia', function ($q) use ($searchTerm) {
                        $q->where('nombre', 'like', '%' . $searchTerm . '%');
                    })
                    ->orWhereHas('subgerencia', function ($q) use ($searchTerm) {
                        $q->where('nombre', 'like', '%' . $searchTerm . '%');
                    });
            });
        }

        if ($fecha) {
            $query->whereDate('created_at', $fecha);
        }

        if ($filtroAnio) {
            $query->whereYear('created_at', $filtroAnio);
        }

        // Aplicar filtro de mes
        if (!empty($filtroMes)) {
            $query->where(function ($q) use ($filtroMes) {
                foreach ($filtroMes as $mes) {
                    if (is_numeric($mes) && $mes >= 1 && $mes <= 12) {
                        $q->orWhereRaw('MONTH(created_at) = ?', [$mes]);
                    }
                }
            });
        }

        // Aplicar filtro de tipo de documento
        if (!empty($filtroTipoDocumento)) {
            $query->where(function ($q) use ($filtroTipoDocumento) {
                $q->whereIn('tipodocumento_id', array_filter($filtroTipoDocumento, 'is_numeric'));
            });
        }

        // Obtener los documentos filtrados
        $documentos = $query->orderByDesc('created_at')->get();

        // Si no hay documentos, retornar con un mensaje
        if ($documentos->isEmpty()) {
            return redirect()->back()->with('error', 'No se encontraron documentos para exportar.');
        }

        // Generar el PDF con los documentos filtrados
        $pdf = Pdf::loadView('reporte', compact('documentos'))
            ->setPaper('A4', 'landscape');

        return $pdf->download('reporte_documentos.pdf');
    }


    public function exportarPDF(Request $request)
    {
        $user = auth()->user();

        // Obtener los filtros del request
        $anio = $request->input('anio');
        $meses = $request->input('mes', []);
        $searchTerm = $request->input('q');
        $filtroTipoDocumento = $request->input('tipodocumento_id', []);

        // Inicializar la consulta del historial
        $query = HistorialCambio::with('documento.tipoDocumento', 'documento.gerencia', 'documento.subgerencia');

        // Filtrar según el rol del usuario
        if ($user->rol->nombre != 'SuperAdmin') {
            // Filtrar según la gerencia o subgerencia del usuario
            if ($user->subusuario) {
                $subgerencia = $user->subusuario->subgerencia;
                $gerencia = $subgerencia->gerencia;

                $query->whereHas('documento', function ($q) use ($gerencia, $subgerencia) {
                    $q->where('gerencia_id', $gerencia->id)
                        ->where(function ($q) use ($subgerencia) {
                            $q->where('subgerencia_id', $subgerencia->id)
                                ->orWhereNull('subgerencia_id');
                        });
                });
            } elseif ($user->gerencia) {
                $query->whereHas('documento', function ($q) use ($user) {
                    $q->where('gerencia_id', $user->gerencia->id);
                });
            } elseif ($subgerencia = Subgerencia::where('usuario_id', $user->id)->first()) {
                $query->whereHas('documento', function ($q) use ($subgerencia) {
                    $q->where('gerencia_id', $subgerencia->gerencia_id)
                        ->where(function ($q) use ($subgerencia) {
                            $q->where('subgerencia_id', $subgerencia->id)
                                ->orWhereNull('subgerencia_id');
                        });
                });
            } else {
                $query->whereHas('documento', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            }
        }

        // Filtro por año
        if ($anio) {
            $query->whereYear('created_at', $anio);
        }

        // Filtro por meses
        if (!empty($meses)) {
            $query->whereIn(DB::raw('MONTH(created_at)'), $meses);
        }

        // Filtro por término de búsqueda en el título del documento
        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('documento', function ($q) use ($searchTerm) {
                    $q->where('titulo', 'like', '%' . $searchTerm . '%')
                        ->orWhere('descripcion', 'like', '%' . $searchTerm . '%');
                })
                    ->orWhere('descripcion', 'LIKE', "%{$searchTerm}%")
                    ->orWhereHas('user', function ($q) use ($searchTerm) {
                        $q->where('nombre_usuario', 'LIKE', "%{$searchTerm}%");
                    });
            });
        }
        if (!empty($filtroTipoDocumento)) {
            $query->whereHas('documento', function ($q) use ($filtroTipoDocumento) {
                $q->whereIn('tipo_documento_id', $filtroTipoDocumento);
            });
        }

        // Ejecutar la consulta y obtener los datos filtrados
        $historial = $query->get();

        // Crear el PDF con los datos filtrados
        $pdf = PDF::loadView('historial.pdf', compact('historial'));

        // Descargar el PDF
        return $pdf->download('historial_cambios_filtrado.pdf');
    }
}
