@extends('layout/template')

@section('title', 'Editar Documento')

@section('content')
    <div class="container">
        <div class="row">
            <div class="container col-md-12 form_documento" style="overflow-x: hidden;">
                <div class="col-md-12 mt-2">
                    <form action="{{ route('documentos.update', $documento->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <!-- Subir archivo a la izquierda -->
                            <div class="col-md-4 mb-4">
                                <div class="row text-center">
                                    <div class="col-md-12">
                                        <iframe id="pdfIframe"
                                            src="{{ asset('storage/documentos/' . basename($documento->archivo)) }}"
                                            style="display: block; border: 1px solid #ccc; pointer-events: auto;"
                                            frameborder="0" loading="lazy" width="100%" height="250px"></iframe>
                                        <!-- Añadir div para el ícono de PDF -->
                                        <div id="pdfIcon" class="pdf-preview-icon text-center" style="display: none;">
                                            <img class="img_file_pdf centered-img" src="{{ asset('images/icons/pdf.png') }}"
                                                alt="PDF" />
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center pt-serif-regular mt-2">
                                    <p class="nombre_archivo text-center"
                                        data-original-name="{{ basename($documento->archivo) }}">
                                        {{ basename($documento->archivo) }}</p>
                                </div>
                                <div class="container-input mb-3">
                                    <input type="file" name="archivo" id="archivo" class="inputfile inputfile-1"
                                        accept=".pdf" />
                                    <label for="archivo">
                                        <i class="fa fa-repeat" aria-hidden="true"></i>
                                        <span class="pt-serif-bold">Reemplazar archivo</span>
                                    </label>
                                </div>
                                <div class="container-input mb-3">
                                    <a href="#" class="btn btn-doc" onclick="openPdfModal()">
                                        <i class="fa fa-eye" aria-hidden="true"></i> Visualizar Archivo Actual
                                    </a>
                                </div>
                                <!-- Modal para visualizar PDF -->
                                <div class="modal fade pt-serif-regular" id="pdfModal" tabindex="-1" role="dialog"
                                    aria-labelledby="pdfModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="pdfModalLabel">
                                                    {{ basename($documento->archivo) }}</h5>
                                                <button type="button" class="btn-close" data-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body" id="pdfModalBody">
                                                <!-- Ícono de PDF que se muestra solo en dispositivos móviles -->
                                                <div id="pdfIconMobile" class="text-center">
                                                    <img class="img_file_pdf centered-img"
                                                        src="{{ asset('images/icons/pdf.png') }}" alt="PDF" />
                                                    <p class="nombre_archivo">{{ basename($documento->archivo) }}</p>
                                                </div>

                                                <!-- Previsualización del PDF que se muestra en pantallas grandes -->
                                                <iframe id="pdfIframeDesktop"
                                                    src="{{ asset('storage/documentos/' . basename($documento->archivo)) }}"
                                                    style="width: 100%; height: 500px; border: none; display: none;"></iframe>
                                            </div>
                                            <div class="modal-footer">
                                                <a href="{{ asset('storage/documentos/' . basename($documento->archivo)) }}"
                                                    class="btn button_2" target="_blank">
                                                    <i class="fa fa-external-link-square" aria-hidden="true"></i> Abrir en
                                                    otra ventana
                                                </a>
                                                <a href="{{ asset('storage/documentos/' . basename($documento->archivo)) }}"
                                                    download="{{ basename($documento->archivo) }}" class="btn button_1">
                                                    <i class="fa fa-download" aria-hidden="true"></i> Descargar
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- Formulario a la derecha -->
                            <div class="col-md-8">
                                <div class="form-group mb-2">
                                    <label for="titulo">Título:</label>
                                    <input type="text" class="form-control" name="titulo" id="titulo"
                                        placeholder="Ingrese el título" value="{{ old('titulo', $documento->titulo) }}"
                                        required>
                                </div>

                                <div class="form-group mt-3">
                                    <label for="descripcion">Descripción:</label>
                                    <textarea class="form-control" name="descripcion" id="descripcion" rows="5" placeholder="Ingrese la descripción"
                                        required>{{ old('descripcion', $documento->descripcion) }}</textarea>
                                </div>
                                <div class="row">
                                    <div class="form-group mb-2 mt-3 col-md-6">
                                        <label for="tipodocumento_id">Tipo Documento:</label>
                                        <select name="tipodocumento_id" class="form-control" id="tipodocumento_id"
                                            required>
                                            @foreach ($tiposDocumento as $tipoDocumento)
                                                <option value="{{ $tipoDocumento->id }}"
                                                    {{ $documento->tipodocumento_id == $tipoDocumento->id ? 'selected' : '' }}>
                                                    {{ $tipoDocumento->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group mt-3 col-md-6">
                                        <label for="estado">Estado actual:</label>
                                        <input type="text" class="form-control" value="{{ $documento->estado }}"
                                            disabled>
                                        <!-- Botón para abrir el modal de cambio de estado -->
                                        <button type="button" class="btn mt-2 button_3"
                                            data-bs-toggle="modal" data-bs-target="#changeStatusModal">Cambiar
                                            Estado</button>
                                    </div>
                                </div>

                                <div class="container-input mt-4">
                                    <a href="{{ route('documentos.index') }}" class="btn button_2 me-2">
                                        <i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn button_1 ms-2">
                                        <i class="fa fa-save" aria-hidden="true"></i> Guardar Cambios
                                    </button>
                                </div>
                            </div>
                    </form>
                </div>
            </div>

            <!-- Modal para cambiar el estado -->
            <div class="modal fade" id="changeStatusModal" tabindex="-1" aria-labelledby="changeStatusLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="changeStatusLabel">Cambiar Estado</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="documento_id" name="documento_id" value="{{ $documento->id }}">
                            <label for="nuevo_estado">Nuevo Estado:</label>
                            @php
                                // Obtener el usuario autenticado
                                $usuarioAutenticado = auth()->user();

                                // Verificar si el usuario tiene el rol "Usuario Validador" con el privilegio "Acceso a Validar Documento"
                                $esUsuarioValidadorConPrivilegio =
                                    $usuarioAutenticado->rol->nombre === 'UsuarioValidador' &&
                                    $usuarioAutenticado->rol->privilegios->contains(
                                        'nombre',
                                        'Acceso a Validar Documento',
                                    );

                                // Verificar si el usuario tiene el rol "Usuario Publicador" con el privilegio "Acceso a Publicar Documento"
                                $esUsuarioPublicadorConPrivilegio =
                                    $usuarioAutenticado->rol->nombre === 'UsuarioPublicador' &&
                                    $usuarioAutenticado->rol->privilegios->contains(
                                        'nombre',
                                        'Acceso a Publicar Documento',
                                    );
                            @endphp

                            <select id="nuevo_estado" class="form-control" name="nuevo_estado" required>
                                {{-- Mostrar el estado actual como primera opción --}}
                                <option value="{{ $documento->estado }}"
                                    style="color: {{ $documento->estado == 'Creado' ? 'red' : ($documento->estado == 'Validado' ? 'green' : 'blue') }}">
                                    {{ $documento->estado }} (Estado Actual)
                                </option>

                                {{-- Mostrar opciones según el rol y privilegios --}}
                                @if ($esUsuarioValidadorConPrivilegio)
                                    @if ($documento->estado !== 'Validado')
                                        <option value="Validado" style="color: green">Validado</option>
                                    @endif
                                @elseif ($esUsuarioPublicadorConPrivilegio)
                                    @if ($documento->estado !== 'Publicado')
                                        <option value="Publicado" style="color: blue">Publicado</option>
                                    @endif
                                @else
                                    {{-- Mostrar todas las opciones para otros usuarios, excepto el estado actual --}}
                                    @if ($documento->estado !== 'Creado')
                                        <option value="Creado" style="color: red">Creado</option>
                                    @endif
                                    @if ($documento->estado !== 'Validado')
                                        <option value="Validado" style="color: green">Validado</option>
                                    @endif
                                    @if ($documento->estado !== 'Publicado')
                                        <option value="Publicado" style="color: blue">Publicado</option>
                                    @endif
                                @endif
                            </select>

                            <label for="descripcion_modal">Descripción:</label>
                            <textarea id="descripcion_modal" class="form-control" name="descripcion_modal" rows="3" required></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn button_2" data-bs-dismiss="modal">Cerrar</button>
                            <button type="button" class="btn button_1" id="confirmChangeStatus">Cambiar
                                Estado</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <script>
        // Función para mostrar el ícono de PDF cuando la pantalla es móvil
        function showPdfIcon() {
            const pdfPreview = document.getElementById('pdfPreview');
            const pdfIcon = document.getElementById('pdfIcon');

            // Verificar el ancho de la pantalla
            if (window.innerWidth < 768) {
                pdfPreview.style.display = 'none';
                pdfIcon.style.display = 'block';
            } else {
                pdfPreview.style.display = 'block';
                pdfIcon.style.display = 'none';
            }
        }

        // Llamar a la función cuando se carga la página o cuando se redimensiona la ventana
        window.addEventListener('load', showPdfIcon);
        window.addEventListener('resize', showPdfIcon);
    </script>
    <script>
        $(document).ready(function() {
            let isRequestInProgress = false;

            $('#confirmChangeStatus').click(function() {
                if (isRequestInProgress) return;

                const descripcion = $('#descripcion_modal').val().trim();
                const documentoId = $('#documento_id').val();
                const nuevoEstado = $('#nuevo_estado').val();

                // Validación del lado del cliente
                if (!descripcion) {
                    toastr.error('La descripción es obligatoria.');
                    return;
                }

                isRequestInProgress = true;
                $(this).prop('disabled', true);

                $.ajax({
                    url: `{{ url('documentos/${documentoId}/cambiarEstado') }}`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        estado: nuevoEstado,
                        descripcion: descripcion
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Estado cambiado exitosamente');
                            // Redireccionar a la página de índice de documentos
                            window.location.href =
                                "{{ route('documentos.index') }}"; // Asegúrate de que la ruta sea correcta
                        } else {
                            toastr.error(response.message ||
                                'Ha ocurrido un error al cambiar el estado.');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        if (response && response.errors) {
                            const firstError = Object.values(response.errors)[0];
                            toastr.error(Array.isArray(firstError) ? firstError[0] :
                                firstError);
                        } else {
                            toastr.error(
                                'Ha ocurrido un error. Por favor, inténtalo de nuevo.');
                        }
                    },
                    complete: function() {
                        $('#confirmChangeStatus').prop('disabled', false);
                        isRequestInProgress = false;
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            @if ($errors->any())
                toastr.options = {
                    "positionClass": "toast-bottom-right",
                };
                toastr.error("{{ $errors->first() }}");
            @endif
        });
    </script>
    <script>
        function openPdfModal() {
            var pdfUrl = "{{ asset('storage/documentos/' . basename($documento->archivo)) }}";
            var modalBody = document.getElementById('pdfModalBody');
            modalBody.innerHTML = '<embed src="' + pdfUrl + '" type="application/pdf" width="100%" height="500px" />';
            $('#pdfModal').modal('show');
        }
    </script>
    <script>
        $(document).ready(function() {
            $('.btn-close, .btn-no').click(function() {
                $('#pdfModal').modal('hide');
            });
        });
    </script>
    <script>
        // Función para ajustar la vista según el tamaño de pantalla
        function adjustPdfView() {
            const pdfIframe = document.getElementById('pdfIframe');
            const pdfIcon = document.getElementById('pdfIcon');
            const currentFile = document.querySelector('.nombre_archivo').textContent;

            if (window.innerWidth < 768) {
                // Vista móvil
                if (pdfIframe) pdfIframe.style.display = 'none';
                if (pdfIcon) {
                    pdfIcon.style.display = 'block';
                    // Actualizar el nombre del archivo en el ícono
                    const fileNameElement = pdfIcon.querySelector('p');
                    if (fileNameElement) fileNameElement.textContent = currentFile;
                }
            } else {
                // Vista desktop
                if (pdfIframe) pdfIframe.style.display = 'block';
                if (pdfIcon) pdfIcon.style.display = 'none';
            }
        }
        // Manejar el cambio de archivo
        document.getElementById('archivo').addEventListener('change', function(e) {
            const file = this.files[0];
            const nombreArchivoElement = document.querySelector('.nombre_archivo');
            const pdfIframe = document.getElementById('pdfIframe');
            const pdfIcon = document.getElementById('pdfIcon');

            if (file) {
                const fileName = file.name;
                const fileURL = URL.createObjectURL(file);

                // Actualizar el nombre del archivo
                if (nombreArchivoElement) {
                    nombreArchivoElement.textContent = fileName;
                }

                // Actualizar la previsualización según el tamaño de pantalla
                if (window.innerWidth < 768) {
                    // Vista móvil
                    if (pdfIframe) pdfIframe.style.display = 'none';
                    if (pdfIcon) {
                        pdfIcon.style.display = 'block';
                        const fileNameElement = pdfIcon.querySelector('p');
                        if (fileNameElement) fileNameElement.textContent = fileName;
                    }
                } else {
                    // Vista desktop
                    if (pdfIframe) {
                        pdfIframe.src = fileURL;
                        pdfIframe.style.display = 'block';
                    }
                    if (pdfIcon) pdfIcon.style.display = 'none';
                }
            }
        });

        // Ejecutar al cargar la página y al cambiar el tamaño de la ventana
        document.addEventListener('DOMContentLoaded', adjustPdfView);
        window.addEventListener('resize', adjustPdfView);
    </script>
    <script>
        function adjustPdfView() {
            const pdfIframeDesktop = document.getElementById('pdfIframeDesktop');
            const pdfIconMobile = document.getElementById('pdfIconMobile');

            if (window.innerWidth < 768) {
                // Si el ancho de la pantalla es menor a 768px, mostramos el ícono de PDF y ocultamos el iframe
                pdfIframeDesktop.style.display = 'none';
                pdfIconMobile.style.display = 'block';
            } else {
                // Si el ancho de la pantalla es mayor o igual a 768px, mostramos el iframe y ocultamos el ícono
                pdfIframeDesktop.style.display = 'block';
                pdfIconMobile.style.display = 'none';
            }
        }

        function openPdfModal() {
            // Muestra el modal
            $('#pdfModal').modal('show');
            // Ajusta la vista del PDF según el tamaño de la pantalla
            adjustPdfView();
        }

        // Ejecuta la función cuando se carga la página y cuando se redimensiona la ventana
        window.addEventListener('load', adjustPdfView);
        window.addEventListener('resize', adjustPdfView);
    </script>

@endsection
