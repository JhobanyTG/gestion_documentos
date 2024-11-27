@extends('layout/template')

@section('title', 'Crear Documento')

@section('content')
    <div class="container">
        <div class="row">
            <div class="container col-md-12 card form_documento" style="overflow-x: hidden;">
                <div class="col-md-12">
                    <form action="{{ route('documentos.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <!-- Subir archivo a la izquierda -->
                            <div class="col-md-4 mb-3 text-center" style="margin-top:15px">
                                    <div class="col-md-12 d-flex justify-content-center">
                                        <!-- Imagen para subir archivo -->
                                        <img class="img_file" id="uploadImage"
                                            src="{{ asset('images/icons/upload-file2.png') }}" />
                                    </div>
                                    <!-- Previsualización del archivo PDF -->
                                    <div class="row text-center">
                                        <div class="col-md-12">
                                            <iframe id="pdfPreview" src="" width="100%" height="250px"
                                                style="display:none; border: 1px solid #ccc;"></iframe>
                                        </div>
                                    </div>
                                    <div class="col-md-12 container-input mt-3">
                                        <input type="file" name="archivo" id="archivo" class="inputfile inputfile-1 btn-doc"
                                            accept=".pdf" required />
                                        <label for="archivo" class="form-label">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="iborrainputfile" width="20"
                                                height="17" viewBox="0 0 20 17">
                                                <path
                                                    d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z">
                                                </path>
                                            </svg>
                                            <span class="pt-serif-bold">Seleccionar archivo</span>
                                        </label>
                                        <div class="container-input mb-3">
                                            <a href="#" class="btn btn-doc" id="viewPdfBtn">
                                                <i class="fa fa-eye" aria-hidden="true"></i> Visualizar Archivo
                                            </a>
                                        </div>
                                        <!-- Modal para visualizar PDF -->
                                        <div class="modal fade" id="pdfModal" tabindex="-1"
                                            aria-labelledby="pdfModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="pdfModalLabel">Vista Previa del Archivo
                                                            PDF</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <!-- Iframe para mostrar el PDF en el modal -->
                                                        <iframe id="modalPdfViewer" src="" width="100%"
                                                            height="500px" style="border: none;"></iframe>

                                                        <!-- Ícono de PDF que se muestra en móviles -->
                                                        <div id="pdfIcon" style="display: none; text-align: center;">
                                                            <img class="img_file_pdf centered-img"
                                                                src="{{ asset('images/icons/pdf.png') }}" alt="PDF" />
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <!-- Botón de Abrir en Nueva Ventana -->
                                                        <a id="openInNewWindowBtn" class="btn button_2" href="#"
                                                            target="_blank"><i class="fa fa-external-link-square"
                                                                aria-hidden="true"></i>
                                                            Abrir en Nueva Ventana
                                                        </a>
                                                        <!-- Botón de Descargar -->
                                                        <a id="downloadPdfBtn" class="btn button_1" href="#" download>
                                                            <i class="fa fa-download" aria-hidden="true"></i>
                                                            Descargar
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @error('archivo')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                            </div>
                            <!-- Formulario a la derecha -->
                            <div class="col-md-8">
                                <div class="form-group mb-2">
                                    <label for="titulo" class="form-label label_documento">Título:</label>
                                    <input type="text"
                                        class="form-control documento @error('titulo') is-invalid @enderror" name="titulo"
                                        id="titulo" placeholder="Ingrese el título" value="{{ old('titulo') }}" required>
                                    @error('titulo')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="form-group mb-2 mt-2 col-12 col-md-6">
                                        <label for="tipodocumento_id" class="form-label label_documento">Tipo
                                            Documento:</label>
                                        <select name="tipodocumento_id"
                                            class="form-control documento @error('tipodocumento_id') is-invalid @enderror"
                                            id="tipodocumento_id" required>
                                            @foreach ($tiposDocumento as $tipoDocumento)
                                                <option value="{{ $tipoDocumento->id }}">{{ $tipoDocumento->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('tipodocumento_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group mt-2 col-12 col-md-6">
                                        <label for="estado" class="form-label label_documento">Estado:</label>
                                        @php
                                            // Obtener el usuario autenticado
                                            $usuarioAutenticado = auth()->user();

                                            // Verificar si el usuario tiene el rol de "Usuario creador"
                                            $esUsuarioCreador = $usuarioAutenticado->rol->nombre === 'UsuarioCreador';

                                            // Verificar si el usuario tiene el privilegio "Acceso a Crear Documento"
                                            $tienePrivilegioCrearDocumento = $usuarioAutenticado->rol->privilegios->contains(
                                                'nombre',
                                                'Acceso a Crear Documento',
                                            );
                                        @endphp

                                        <select name="estado"
                                            class="form-control documento @error('estado') is-invalid @enderror"
                                            id="estado" required>
                                            @if ($esUsuarioCreador && $tienePrivilegioCrearDocumento)
                                                <option value="Creado" style="color: red">Creado</option>
                                            @else
                                                <option value="Creado" style="color: grey">Creado</option>
                                                <option value="Validado" style="color: green">Validado</option>
                                                <option value="Publicado" style="color: blue">Publicado</option>
                                            @endif
                                        </select>

                                        @error('estado')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group mt-2">
                                    <label for="descripcion" class="form-label label_documento">Descripción:</label>
                                    <textarea class="form-control documento @error('descripcion') is-invalid @enderror" name="descripcion"
                                        id="descripcion" rows="5" placeholder="Ingrese la descripción" required>{{ old('descripcion') }}</textarea>
                                    @error('descripcion')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="container-input mt-3">
                                    <a href="{{ route('documentos.index') }}"
                                        class="btn button_2 me-2"><i class="fa fa-arrow-circle-left"
                                            aria-hidden="true"></i> Cancelar</a>
                                    <button type="submit" class="btn button_1 ms-2"><i
                                            class="fa fa-plus" aria-hidden="true"></i>
                                        Crear</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    toastr.options = {
                        "positionClass": "toast-top-right",
                        "timeOut": 5000,
                    };
                    toastr.error("{{ $error }}");
                @endforeach
            @endif
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var inputs = document.querySelectorAll(".inputfile");
            Array.prototype.forEach.call(inputs, function(input) {
                var label = input.nextElementSibling;
                input.addEventListener("change", function(e) {
                    var fileName = "";
                    if (input.files && input.files.length > 1)
                        fileName = input.getAttribute("data-multiple-caption").replace("{count}",
                            input.files.length);
                    else
                        fileName = e.target.value.split("\\").pop();
                    label.querySelector("span").innerHTML = fileName;
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('input[type="file"]').change(function() {
                var fileInput = $(this);
                var fileName = fileInput.val().split('\\').pop();
                var fileExtension = fileName.split('.').pop().toLowerCase();
                var allowedExtensions = ['pdf'];

                if (allowedExtensions.indexOf(fileExtension) === -1) {
                    fileInput.val('');
                    fileName = '';

                    var alertMessage =
                        'Solo se permiten archivos PDF.<br>Seleccione otro archivo por favor.';
                    var alertDiv = $(
                        '<div class="alert alert-danger alert-dismissible fade show position-fixed top-0 end-0 mt-2 ms-2" role="alert" style="z-index: 999; background-color: #C71E42; color: #FFFFFF;">' +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                        '<i class="fa fa-exclamation-triangle me-2" aria-hidden="true"></i>' +
                        alertMessage +
                        '</div>');
                    $('body').append(alertDiv);
                    setTimeout(function() {
                        alertDiv.fadeOut(500, function() {
                            $(this).remove();
                        });
                    }, 5000);
                }

                fileInput.siblings('.inputfile').text(fileName);
            });

            $('form').submit(function() {
                var fileInput = $('input[type="file"]');
                var fileName = fileInput.val().split('\\').pop();
                var fileExtension = fileName.split('.').pop().toLowerCase();
                var allowedExtensions = ['pdf'];

                if (allowedExtensions.indexOf(fileExtension) === -1) {
                    fileInput.val('');
                    fileName = '';

                    var alertMessage =
                        'Solo se permiten archivos PDF.<br>Seleccione otro archivo por favor.';
                    var alertDiv = $(
                        '<div class="alert alert-danger alert-dismissible fade show position-fixed top-0 end-0 mt-2 ms-2" role="alert" style="z-index: 999; background-color: #C71E42; color: #FFFFFF;">' +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                        '<i class="fa fa-exclamation-triangle me-2" aria-hidden="true"></i>' +
                        alertMessage +
                        '</div>');
                    $('body').append(alertDiv);
                    setTimeout(function() {
                        alertDiv.fadeOut(500, function() {
                            $(this).remove();
                        });
                    }, 5000);

                    return false;
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var estadoSelect = document.getElementById('estado');

            function updateColor() {
                var selectedOption = estadoSelect.options[estadoSelect.selectedIndex];
                estadoSelect.style.color = getComputedStyle(selectedOption).color;
            }

            // Inicializa el color al cargar la página
            updateColor();

            // Actualiza el color cuando se cambia la opción
            estadoSelect.addEventListener('change', updateColor);
        });
    </script>

    <script>
        document.getElementById('archivo').addEventListener('change', function(event) {
            var file = event.target.files[0];
            var uploadImage = document.getElementById('uploadImage');
            var pdfPreview = document.getElementById('pdfPreview');

            if (file) {
                var fileURL = URL.createObjectURL(file);

                if (window.innerWidth < 768) {
                    // En móviles, mostrar ícono de PDF
                    pdfPreview.style.display = 'none';
                    uploadImage.style.display = 'none';

                    // Crear y mostrar el ícono de PDF si no existe
                    let pdfIcon = document.querySelector('.pdf-preview-icon');
                    if (!pdfIcon) {
                        pdfIcon = document.createElement('div');
                        pdfIcon.className = 'pdf-preview-icon text-center mt-3';
                        pdfIcon.innerHTML =
                            '<img class="img_file_pdf centered-img" src="{{ asset('images/icons/pdf.png') }}" alt="PDF" />' +
                            '<p class="mt-2">' + file.name + '</p>';
                        pdfPreview.parentNode.insertBefore(pdfIcon, pdfPreview);
                    }
                } else {
                    // En desktop, mostrar preview
                    pdfPreview.src = fileURL;
                    pdfPreview.style.display = 'block';
                    uploadImage.style.display = 'none';

                    // Remover ícono si existe
                    const pdfIcon = document.querySelector('.pdf-preview-icon');
                    if (pdfIcon) {
                        pdfIcon.remove();
                    }
                }
            }
        });

        // Agregar listener para cambios de tamaño de ventana
        window.addEventListener('resize', function() {
            const file = document.getElementById('archivo').files[0];
            if (file) {
                var pdfPreview = document.getElementById('pdfPreview');
                var pdfIcon = document.querySelector('.pdf-preview-icon');

                if (window.innerWidth < 768) {
                    // Cambiar a vista móvil
                    pdfPreview.style.display = 'none';
                    if (!pdfIcon) {
                        pdfIcon = document.createElement('div');
                        pdfIcon.className = 'pdf-preview-icon text-center mt-3';
                        pdfIcon.innerHTML =
                            '<i class="fa fa-file-pdf-o" style="font-size: 64px; color: #d9534f;"></i>' +
                            '<p class="mt-2">' + file.name + '</p>';
                        pdfPreview.parentNode.insertBefore(pdfIcon, pdfPreview);
                    }
                } else {
                    // Cambiar a vista desktop
                    pdfPreview.style.display = 'block';
                    if (pdfIcon) {
                        pdfIcon.remove();
                    }
                }
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var viewPdfBtn = document.getElementById('viewPdfBtn');
            var modalPdfViewer = document.getElementById('modalPdfViewer');
            var downloadPdfBtn = document.getElementById('downloadPdfBtn');
            var openInNewWindowBtn = document.getElementById('openInNewWindowBtn');
            var archivoInput = document.getElementById('archivo');
            var pdfModal = new bootstrap.Modal(document.getElementById('pdfModal'));
            var currentPdfUrl = '';

            // Al hacer clic en "Visualizar Archivo"
            viewPdfBtn.addEventListener('click', function(e) {
                e.preventDefault();

                // Obtener el archivo actual del input
                var file = archivoInput.files[0];

                if (file && file.type === 'application/pdf') {
                    currentPdfUrl = URL.createObjectURL(file);
                    modalPdfViewer.src = currentPdfUrl; // Cargar el archivo en el iframe
                    downloadPdfBtn.href = currentPdfUrl; // Configurar enlace de descarga
                    openInNewWindowBtn.href =
                        currentPdfUrl; // Configurar enlace para abrir en nueva ventana

                    // Establecer el título del modal al nombre del archivo
                    var fileName = file.name; // Obtener el nombre del archivo
                    document.getElementById('pdfModalLabel').textContent =
                        fileName; // Actualizar el título del modal

                    pdfModal.show(); // Mostrar el modal
                } else {
                    alert('Por favor, selecciona un archivo PDF primero.');
                }
            });
        });
    </script>
    <script>
        // Detecta si el ancho de la pantalla es menor a 768px (tamaño típico de móviles)
        function adjustPdfView() {
            if (window.innerWidth < 768) {
                document.getElementById('modalPdfViewer').style.display = 'none';
                document.getElementById('pdfIcon').style.display = 'block';
            } else {
                document.getElementById('modalPdfViewer').style.display = 'block';
                document.getElementById('pdfIcon').style.display = 'none';
            }
        }

        // Llama a la función al cargar la página y al redimensionar la ventana
        window.addEventListener('load', adjustPdfView);
        window.addEventListener('resize', adjustPdfView);
    </script>
@endsection
