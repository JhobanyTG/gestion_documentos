@extends('layout/template')

@section('title', 'Historial')

@section('content')
    <div class="container">
        <div class="col-md-12 order-md-1">
            <div class="card-body">
                <a href="{{ route('historial.exportar', ['q' => request('q'), 'anio' => request('anio'), 'mes' => request('mes')]) }}" class="btn btn-doc mb-3"> <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                    Exportar a PDF</a>

                @php
                    // Definir el array con los nombres de los meses en español
                    $mesesEnEspanol = [
                        1 => 'Enero',
                        2 => 'Febrero',
                        3 => 'Marzo',
                        4 => 'Abril',
                        5 => 'Mayo',
                        6 => 'Junio',
                        7 => 'Julio',
                        8 => 'Agosto',
                        9 => 'Septiembre',
                        10 => 'Octubre',
                        11 => 'Noviembre',
                        12 => 'Diciembre',
                    ];
                @endphp

                <!-- Filtro del historial por año, mes y término de búsqueda -->
                <form action="{{ route('documentos.historial', ['documentoId' => 0]) }}" method="GET" class="mb-3">
                    <div class="buscador input-group mb-3">
                        <input type="text" class="form-control" placeholder="Buscar..." name="q"
                            value="{{ $searchTerm }}">

                        @if ($filtroAnio || $searchTerm || !empty($filtroMes))
                            <a class="border border-2" href="{{ route('documentos.historial', ['documentoId' => 0]) }}">
                                <i class="fa fa-times m-4" style="color: red;" aria-hidden="true"></i>
                            </a>
                        @endif

                        <button class="btn btn-doc" type="submit">Buscar</button>
                    </div>
                    <!-- Campos ocultos para mantener los filtros -->
                    <input type="hidden" name="anio" value="{{ $filtroAnio }}">
                    @if (!empty($filtroMes))
                        @foreach ($filtroMes as $mes)
                            <input type="hidden" name="mes[]" value="{{ $mes }}">
                        @endforeach
                    @endif
                </form>

                @if ($searchTerm || $filtroAnio || !empty($filtroMes))
                    <p class="resultado-buscador">
                        Resultados de búsqueda de:
                        @if ($filtroAnio)
                            <strong>Año: {{ $filtroAnio }}</strong>
                        @endif
                        @if ($filtroMes)
                            <strong>Mes:
                                {{ implode(', ', array_map(fn($mes) => $mesesEnEspanol[$mes] ?? $mes, $filtroMes)) }}</strong>
                        @endif
                        @if ($searchTerm)
                            <strong>Término: {{ $searchTerm }}</strong>
                        @endif
                    </p>
                @endif

                <!-- Columna del filtro de años y meses -->
                <div class="row">
                    <div class="filtro order-md-2 col-md-2">
                        <div class="mb-3">
                            <h4>Listar por Año</h4>
                            <div class="row">
                                <div class="col-12">
                                    <form action="{{ route('documentos.historial', ['documentoId' => 0]) }}"
                                        method="GET">
                                        <button type="submit"
                                            class="btn btn-block w-100 {{ !$filtroAnio ? 'btn-dark' : 'btn-light' }}">Todos</button>
                                        <input type="hidden" name="q" value="{{ $searchTerm }}">
                                    </form>

                                    @foreach ($availableYears as $year)
                                        <form action="{{ route('documentos.historial', ['documentoId' => 0]) }}"
                                            method="GET">
                                            <input type="hidden" name="anio" value="{{ $year }}">
                                            <input type="hidden" name="q" value="{{ $searchTerm }}">
                                            <button type="submit"
                                                class="btn btn-block w-100 {{ $filtroAnio == $year ? 'btn-dark' : 'btn-light' }}">{{ $year }}</button>
                                        </form>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Filtros de Meses -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h4>Filtrar por Mes</h4>
                                <div class="row">
                                    <div class="col-12">
                                        <form action="{{ route('documentos.historial', ['documentoId' => 0]) }}"
                                            method="GET" id="filtroForm">
                                            @if ($filtroAnio)
                                                @foreach ($availableMonths as $month)
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input m-1" name="mes[]"
                                                            id="mes{{ $month }}" value="{{ $month }}"
                                                            {{ in_array($month, $filtroMes) ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="mes{{ $month }}">{{ $mesesEnEspanol[$month] }}</label>
                                                    </div>
                                                @endforeach
                                            @else
                                                <p>Selecciona un año para filtrar los meses.</p>
                                            @endif
                                            <input type="hidden" name="anio" value="{{ $filtroAnio }}">
                                            <input type="hidden" name="q" value="{{ $searchTerm }}">
                                            <div style="display: block; margin-bottom: 10px; width: 100%;">
                                                <button class="boton-filtro btn btn-doc" type="submit">Ejecutar
                                                    Filtro</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla del historial filtrado -->
                    <div class="table_doc col-md-10 order-md-1">
                        <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">
                            <table id="example1" class="table mt-4 table-hover pt-serif-regular" role="grid"
                                aria-describedby="example1_info">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Documento</th>
                                        <th>Estado Anterior</th>
                                        <th>Estado Nuevo</th>
                                        <th>Descripción</th>
                                        <th>Usuario</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($historial as $cambio)
                                        <tr>
                                            <td class="text-center">{{ $cambio->id }}</td>
                                            <td class="text-center">{{ $cambio->documento->titulo ?? 'Sin título' }}</td>
                                            <td class="text-center">
                                                @if ($cambio->estado_anterior === 'Creado')
                                                    <span class="badge text-bg-secondary">Creado</span>
                                                @elseif($cambio->estado_anterior === 'Validado')
                                                    <span class="badge text-bg-success">Validado</span>
                                                @elseif($cambio->estado_anterior === 'Publicado')
                                                    <span class="badge text-bg-primary">Publicado</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($cambio->estado_nuevo === 'Creado')
                                                    <span class="badge text-bg-danger">Creado</span>
                                                @elseif($cambio->estado_nuevo === 'Validado')
                                                    <span class="badge text-bg-success">Validado</span>
                                                @elseif($cambio->estado_nuevo === 'Publicado')
                                                    <span class="badge text-bg-primary">Publicado</span>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $cambio->descripcion }}</td>
                                            <td class="text-center">{{ $cambio->user->nombre_usuario }}</td>
                                            <td class="text-center">{{ $cambio->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="paginacion">
            <div class="d-flex justify-content-center mt-4">
                {{ $historial->links('pagination.custom') }}
            </div>
            <div class="text-center">
                @if ($historial->count() > 1)
                    Mostrando ítems {{ $historial->firstItem() }}-{{ $historial->lastItem() }} de
                    {{ $historial->total() }}
                @else
                    Mostrando ítem {{ $historial->firstItem() }} de {{ $historial->total() }}
                @endif
            </div>
        </div>
    </div>
@endsection
