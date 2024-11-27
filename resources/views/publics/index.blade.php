@extends('layout/templatep')

@section('title', 'Lista de Documentos')

@section('content')
    <div class="container mt-4">
        <form action="{{ route('public.index') }}" method="GET" class="mb-3">
            <div class="buscador input-group mb-3">
                <input type="text" class="form-control" placeholder="Buscar..." name="q" value="{{ $searchTerm }}">
                @if ($filtroAnio || $searchTerm || !empty($filtroMes) || !empty($filtroTipoDocumento))
                    <a class="border border-2" href="{{ route('public.index') }}">
                        <i class="fa fa-times m-4" style="color: red;" aria-hidden="true"></i>
                    </a>
                @endif
                @if ($filtroAnio)
                    <input type="hidden" name="anio" value="{{ $filtroAnio }}">
                @endif
                @if (!empty($filtroMes))
                    @foreach ($filtroMes as $mes)
                        <input type="hidden" name="mes[]" value="{{ $mes }}">
                    @endforeach
                @endif
                @if (!empty($filtroTipoDocumento))
                    @foreach ($filtroTipoDocumento as $tipo)
                        <input type="hidden" name="tipodocumento_id[]" value="{{ $tipo }}">
                    @endforeach
                @endif
                <button class="btn button_1" type="submit">Buscar</button>
            </div>
        </form>
        @if ($searchTerm || $filtroAnio || !empty($filtroMes) || !empty($filtroTipoDocumento))
            <p class="resultado-buscador">
                Resultados de búsqueda de:
                @if ($searchTerm || $filtroAnio || !empty($filtroMes) || !empty($filtroTipoDocumento))

                    @if ($filtroAnio)
                        @if ($searchTerm || $filtroMes)
                        @endif
                        <strong>Año: {{ $filtroAnio }}</strong>
                    @endif
                    @if ($filtroMes)
                        @if ($filtroAnio || $searchTerm)
                            ,
                        @endif
                        <strong>Mes:
                            @php
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
                            {{ implode(', ', array_map(fn($mes) => $mesesEnEspanol[$mes] ?? $mes, $filtroMes)) }}
                        </strong>
                    @endif
                    @if (!empty($filtroTipoDocumento))
                        @if ($searchTerm || $filtroAnio || $filtroMes)
                            ,
                        @endif
                        <strong>Tipo:
                            {{ implode(', ', $tiposDocumento->whereIn('id', $filtroTipoDocumento)->pluck('nombre')->toArray()) ?: 'Ninguno seleccionado' }}
                        </strong>
                    @endif
                    @if ($searchTerm)
                        @if ($filtroAnio || $filtroMes)
                            y
                        @endif
                        <strong>Término: {{ $searchTerm }}</strong>
                    @endif
                @else
                    <strong>No se encontraron resultados.</strong>
                @endif
        @endif
        <div class="row">
            <div class="filtro order-md-2 col-md-2">
                <div class="mb-3">
                    <h5>Listar</h5>
                    <div class="row">
                        <div class="col-12">
                            <form action="{{ route('public.index') }}" method="GET" class="d-inline">
                                <button type="submit"
                                    class="btn btn-block w-100 {{ !$filtroAnio ? 'btn-dark' : 'btn-light' }}">
                                    Todos
                                </button>
                            </form>
                            @foreach ($availableYears as $year)
                                <form action="{{ route('public.index') }}" method="GET" class="d-inline">
                                    <input type="hidden" name="anio" value="{{ $year }}">
                                    <button type="submit"
                                        class="btn btn-block w-100 {{ $filtroAnio == $year ? 'btn-dark' : 'btn-light' }}">
                                        {{ $year }}
                                    </button>
                                    <input type="hidden" name="q" value="{{ $searchTerm }}">
                                    @if (!empty($filtroMes))
                                        @foreach ($filtroMes as $mes)
                                            <input type="hidden" name="mes[]" value="{{ $mes }}">
                                        @endforeach
                                    @endif
                                </form>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-12">
                        <h5>Filtros</h5>
                        <div class="row">
                            <div class="col-12">
                                <form action="{{ route('public.index') }}" method="GET" id="filtroForm">
                                    <div class="input-group mb-3">
                                        <div class="col-12">
                                            @if ($filtroAnio)
                                                @php
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
                                        </div>
                                        <br>
                                        <div class="mb-3">
                                            <h5 for="tipodocumento_id" class="form-label">Tipo de Documento</h5>
                                            <div>
                                                @foreach ($tiposDocumento as $tipo)
                                                    <div class="form-check">
                                                        <input type="checkbox" name="tipodocumento_id[]"
                                                            value="{{ $tipo->id }}"
                                                            id="tipodocumento_{{ $tipo->id }}" class="form-check-input"
                                                            {{ is_array($filtroTipoDocumento) && in_array($tipo->id, $filtroTipoDocumento) ? 'checked' : '' }}>
                                                        <label for="tipodocumento_{{ $tipo->id }}"
                                                            class="form-check-label">{{ $tipo->nombre }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <input type="hidden" name="anio" value="{{ $filtroAnio }}">
                                        <input type="hidden" name="q" value="{{ $searchTerm }}">
                                        <div style="display: block; margin-bottom: 10px; width: 100%;">
                                            <button class="btn button_1" type="submit">Ejecutar
                                                Filtro</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-10 order-md-1">
                <div class="card-body">
                    <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">
                        <div class="row">
                            @foreach ($documentos as $documento)
                                <div class="col-md-4 col-sm-6 col-lg-3 mb-4" data-id="{{ $documento->id }}">
                                    <div class="card h-100 shadow-sm">
                                        <!-- PDF Preview -->
                                        <div class="card-preview position-relative" style="height: 200px; cursor: pointer"
                                            onclick="openPdfModal('{{ $documento->id }}', '{{ asset('storage/documentos/' . basename($documento->archivo)) }}')">
                                            <iframe
                                                src="{{ asset('storage/documentos/' . basename($documento->archivo)) }}"
                                                type="application/pdf"
                                                style="width: 100%; height: 100%; pointer-events: none;"
                                                frameborder="0"
                                                loading="lazy">
                                            </iframe>
                                        </div>

                                        <!-- Card Body -->
                                        <div class="card-body">
                                            <h5 class="card-title mb-2">
                                                @php
                                                    if ($searchTerm) {
                                                        $escapedSearchTerm = preg_quote($searchTerm, '/');
                                                        $highlightedTitle = preg_replace(
                                                            '/(' . $escapedSearchTerm . ')/i',
                                                            '<mark>$1</mark>',
                                                            $documento->titulo,
                                                        );
                                                    } else {
                                                        $highlightedTitle = $documento->titulo;
                                                    }
                                                @endphp
                                                {!! $highlightedTitle !!}
                                            </h5>

                                            <div class="document-info mb-2">
                                                <p class="text-muted mb-1">
                                                    <small><strong>Fecha:</strong> {{ $documento->created_at->format('Y-m-d') }}</small>
                                                </p>
                                                <p class="text-muted mb-1">
                                                    <small><strong>Tipo:</strong> {{ $documento->tipoDocumento->nombre }}</small>
                                                </p>
                                                <p class="text-muted mb-1">
                                                    <small><strong>Gerencia:</strong>
                                                        {{ $documento->gerencia ? $documento->gerencia->nombre : 'Creado Por el administrador' }}
                                                    </small>
                                                </p>
                                                <p class="text-muted mb-1">
                                                    <small><strong>Subgerencia:</strong>
                                                        {{ $documento->subgerencia ? $documento->subgerencia->nombre : 'N/A' }}
                                                    </small>
                                                </p>
                                            </div>

                                            <div class="description-container mb-2">
                                                <p class="text-muted mb-1">
                                                    <small><strong>Descripción:</strong>
                                                        @if (strlen($documento->descripcion) > 65)
                                                            @php
                                                                if ($searchTerm) {
                                                                    // Escapar el término de búsqueda para evitar problemas de HTML
                                                                    $escapedSearchTerm = preg_quote($searchTerm, '/');
                                                                    // Usar preg_replace para reemplazo insensible al caso en la descripción truncada
                                                                    $highlightedDescription = preg_replace(
                                                                        '/(' . $escapedSearchTerm . ')/i',
                                                                        '<mark>$1</mark>',
                                                                        substr($documento->descripcion, 0, 65),
                                                                    );
                                                                    // Usar preg_replace para reemplazo insensible al caso en la descripción completa
                                                                    $highlightedFullDescription = preg_replace(
                                                                        '/(' . $escapedSearchTerm . ')/i',
                                                                        '<mark>$1</mark>',
                                                                        $documento->descripcion,
                                                                    );
                                                                } else {
                                                                    // Si no hay término de búsqueda, mostrar la descripción sin resaltar
                                                                    $highlightedDescription = substr(
                                                                        $documento->descripcion,
                                                                        0,
                                                                        65,
                                                                    );
                                                                    $highlightedFullDescription = $documento->descripcion;
                                                                }
                                                            @endphp
                                                            <span class="truncated">{!! $highlightedDescription !!}...</span>
                                                            <span class="expand-description"
                                                                data-target="#desc-{{ $documento->id }}">Ver más</span>
                                                            <div id="desc-{{ $documento->id }}" class="collapse full-description">
                                                                <span>{!! $highlightedFullDescription !!}</span>
                                                                <span class="collapse-description">Ver menos</span>
                                                            </div>
                                                        @else
                                                            @php
                                                                if ($searchTerm) {
                                                                    // Escapar el término de búsqueda para evitar problemas de HTML
                                                                    $escapedSearchTerm = preg_quote($searchTerm, '/');
                                                                    // Usar preg_replace para reemplazo insensible al caso en la descripción completa
                                                                    $highlightedDescription = preg_replace(
                                                                        '/(' . $escapedSearchTerm . ')/i',
                                                                        '<mark>$1</mark>',
                                                                        $documento->descripcion,
                                                                    );
                                                                } else {
                                                                    // Si no hay término de búsqueda, mostrar la descripción sin resaltar
                                                                    $highlightedDescription = $documento->descripcion;
                                                                }
                                                            @endphp
                                                            {!! $highlightedDescription !!}
                                                        @endif
                                                    </small>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal fade pt-serif-regular" id="pdfModal-{{ $documento->id }}" tabindex="-1" role="dialog"
                                    aria-labelledby="pdfModalLabel-{{ $documento->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="pdfModalLabel-{{ $documento->id }}">
                                                    {{ $documento->titulo }}
                                                </h5>
                                                <button type="button" class="btn-close" data-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body" id="pdfModalBody-{{ $documento->id }}">
                                                <!-- El contenido se insertará dinámicamente -->
                                            </div>
                                            <div class="modal-footer">
                                                <a href="{{ asset('storage/documentos/' . basename($documento->archivo)) }}"
                                                    class="btn btn-info" target="_blank">
                                                    <i class="fa fa-external-link-square" aria-hidden="true"></i>
                                                    Abrir en otra ventana
                                                </a>
                                                <a href="{{ asset('storage/documentos/' . basename($documento->archivo)) }}"
                                                    download="{{ basename($documento->archivo) }}"
                                                    class="btn btn-dark">
                                                    <i class="fa fa-download" aria-hidden="true"></i>
                                                    Descargar
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal pt-serif-regular" tabindex="-1" role="dialog"
                                    id="confirmationModal">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirmar Eliminación</h5>
                                                <button type="button" class="btn-close" data-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="text-center">¿Estás seguro de eliminar este Documento? Esta acción no se puede deshacer.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary btn-no"
                                                    data-dismiss="modal"><i class="fa fa-ban" aria-hidden="true"></i>
                                                    Cancelar</button>
                                                <form action="{{ route('documentos.destroy', $documento->id) }}"
                                                    method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger"
                                                        onclick="return);"><i class="fa fa-trash"
                                                            aria-hidden="true"></i> Confirmar</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            @if ($documentos->isEmpty())
                                <div class="col-12">
                                    <p class="text-center">No se encontraron resultados para la búsqueda.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="paginacion">
            <div class="d-flex justify-content-center mt-4">
                {{ $documentos->links('pagination.custom') }}
            </div>
            <div class="text-center">
                @if ($documentos->count() > 1)
                    Mostrando ítems {{ $documentos->firstItem() }}-{{ $documentos->lastItem() }} de
                    {{ $documentos->total() }}
                @else
                    Mostrando ítem {{ $documentos->firstItem() }} de {{ $documentos->total() }}
                @endif
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            @if (Session::has('success'))
                toastr.options = {
                    "positionClass": "toast-bottom-right",
                };
                toastr.success("{{ Session::get('success') }}");
            @endif
        });
    </script>
    <script>
        function showConfirmationModal() {
            $('#confirmationModal').modal('show');
        }
    </script>
    <script>
        $(document).ready(function() {
            $('.btn-close, .btn-no').click(function() {
                $('#confirmationModal').modal('hide');
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            function openPdfModal(pdfUrl, pdfName, modalId) {
                // Usar jQuery para mayor seguridad en la selección
                var $modal = $(`#pdfModal-${modalId}`);
                var $modalBody = $(`#pdfModalBody-${modalId}`);

                if ($modalBody.length === 0) {
                    console.error('Modal body not found for ID:', modalId);
                    return;
                }

                // Verificar si es dispositivo móvil
                if (window.innerWidth <= 768) {
                    $modalBody.html(`
                        <div class="text-center">
                            <img src="/images/icons/pdf.png" alt="PDF Icon" class="pdf-icon-modal mb-3" style="width: 100px;">
                        </div>
                    `);
                } else {
                    $modalBody.html('<embed src="' + pdfUrl + '" type="application/pdf" width="100%" height="500px" />');
                }

                // Actualizar el título y mostrar el modal
                $modal.find('.modal-title').text(pdfName);
                $modal.modal('show');
            }

            // Event listener para el preview de la card
            $('.card-preview').on('click', function() {
                var $card = $(this).closest('.card');
                var pdfUrl = $(this).find('iframe').attr('src');
                var pdfName = $card.find('.card-title').text().trim();
                // Obtener el ID del documento desde un data attribute
                var documentId = $card.closest('[data-id]').data('id');

                if (documentId) {
                    openPdfModal(pdfUrl, pdfName, documentId);
                } else {
                    console.error('Document ID not found');
                }
            });

            // Manejar cambios de tamaño de ventana
            $(window).on('resize', function() {
                $('.modal.show').each(function() {
                    var modalId = $(this).attr('id').replace('pdfModal-', '');
                    var pdfUrl = $(this).find('.modal-footer a').first().attr('href');
                    var pdfName = $(this).find('.modal-title').text();
                    openPdfModal(pdfUrl, pdfName, modalId);
                });
            });

            // Cerrar modales
            $('.btn-close, .btn-no').click(function() {
                $(this).closest('.modal').modal('hide');
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('.expand-description').on('click', function(e) {
                e.preventDefault();
                var target = $(this).data('target');
                $(this).hide();
                $(target).collapse('show');
                // Asegurar que el contenedor padre no limite la altura
                $(target).closest('.description-container').css('max-height', 'none');
                $(target).find('.collapse-description').show();
                $(target).siblings('.truncated').hide();
            });

            $('.collapse-description').on('click', function(e) {
                e.preventDefault();
                var target = $(this).closest('.full-description');
                $(target).collapse('hide');
                // Restaurar la altura del contenedor
                $(target).closest('.description-container').css('max-height', '');
                $(this).hide();
                $(target).siblings('.expand-description').show();
                $(target).siblings('.truncated').show();
            });

            // Inicialización
            $('.collapse-description').hide();
            $('.full-description').collapse('hide');
        });
    </script>

@stop
