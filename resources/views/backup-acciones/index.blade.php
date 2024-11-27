@extends('layout/template')

@section('title', 'Historial de Acciones')

@section('content')
    <div class="card-body mt-3 p-2">
        <div id="content_ta_wrapper" class="dataTables_wrapper">
            <div class="table-responsive">
                <table id="content_ta" class="table table-striped mt-4 table-hover custom-table pt-serif-regular"
                    role="grid" aria-describedby="content_ta_info">
                    <thead>
                        <tr role="row">
                            <th class="text-center">Fecha y Hora</th>
                            <th class="text-center">Administrador</th>
                            <th class="text-center">Tipo de Petición</th>
                            <th class="text-center">Acción</th>
                            <th class="text-center">Usuario Afectado</th>
                            <th class="text-center">Detalles</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($acciones as $accion)
                            <tr class="odd">
                                <td class="text-center">{{ $accion->created_at->format('d/m/Y H:i:s') }}</td>
                                <td class="text-center">{{ $accion->admin_nombre }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $accion->tipo_peticion === 'DELETE' ? 'bg-danger' :
                                        ($accion->tipo_peticion === 'PUT' ? 'bg-warning' :
                                        ($accion->tipo_peticion === 'POST' ? 'bg-success' : 'bg-info')) }}">
                                        {{ $accion->tipo_peticion }}
                                    </span>
                                </td>
                                <td class="text-center">{{ $accion->accion }}</td>
                                <td class="text-center">{{ $accion->usuario_afectado_nombre ?? 'N/A' }}</td>
                                <td class="text-center">
                                    <!-- <button type="button" class="btn btn-info"
                                        onclick='mostrarDetalles({!! json_encode($accion->detalles_cambios) !!}, "{{ $accion->descripcion }}")'>
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </button> -->
                                    <button type="button" class="btn button_1"
                                        onclick='mostrarDetalles(
                                            {!! json_encode($accion->detalles_cambios) !!},
                                            "{{ $accion->descripcion }}",
                                            "{{ $accion->admin_nombre }}",
                                            "{{ $accion->tipo_peticion }}",
                                            "{{ $accion->accion }}",
                                            "{{ $accion->usuario_afectado_nombre }}"
                                        )'>
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal para mostrar detalles -->
    <div class="modal fade" id="detallesModal" tabindex="-1" role="dialog" aria-labelledby="detallesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detallesModalLabel">Detalles de la Acción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="descripcion-container mb-4">
                        <h6>Descripción:</h6>
                        <h6 id="descripcionAccion" class="bg-light p-2 rounded"></h6>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Nombre Administrador:</h6>
                            <p id="adminNombre" class="bg-light p-2 rounded"></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Usuario Afectado:</h6>
                            <p id="usuarioAfectado" class="bg-light p-2 rounded"></p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Tipo de Petición:</h6>
                            <p id="tipoPeticion"></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Acción:</h6>
                            <p id="accionRealizada" class="bg-light p-2 rounded"></p>
                        </div>
                    </div>

                    <div id="detallesContainer">
                        <h6>Detalles:</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody id="detallesTabla">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>


    <script>
        function mostrarDetalles(detalles, descripcion, adminNombre, tipoPeticion, accion, usuarioAfectado) {
            const detallesObj = typeof detalles === 'string' ? JSON.parse(detalles) : detalles;

            // Mostrar información básica
            document.getElementById('adminNombre').textContent = adminNombre;
            document.getElementById('usuarioAfectado').textContent = usuarioAfectado || 'N/A';

            // Mostrar tipo de petición con badge
            const tipoPeticionEl = document.getElementById('tipoPeticion');
            const badgeClass = tipoPeticion === 'DELETE' ? 'bg-danger' :
                                tipoPeticion === 'PUT' ? 'bg-warning' :
                                tipoPeticion === 'POST' ? 'bg-success' : 'bg-info';
            tipoPeticionEl.innerHTML = `<span class="badge ${badgeClass}">${tipoPeticion}</span>`;

            document.getElementById('accionRealizada').textContent = accion;

            // Mostrar descripción sin corchetes
            document.getElementById('descripcionAccion').textContent =
                descripcion.replace('[ADMIN_TOTAL]', 'Administrador Total:')
                            .replace('[ADMIN_GERENCIA]', 'Administrador Gerencia:');

            // Generar tabla de detalles
            const tabla = document.getElementById('detallesTabla');
            tabla.innerHTML = '';

            const addRow = (label, value) => {
                const row = tabla.insertRow();
                row.insertCell(0).innerHTML = `<strong>${label}:</strong>`;
                row.insertCell(1).textContent = value;
            };

            // Mostrar detalles según el tipo de acción
            if (detallesObj.datos_modificados?.accion === 'cambio_contraseña') {
                addRow('Fecha y Hora', new Date(detallesObj.timestamp).toLocaleString());
            } else if (!detallesObj.datos_modificados) {
                addRow('Fecha y Hora', new Date(detallesObj.timestamp).toLocaleString());
            } else {
                if (detallesObj.datos_modificados) {
                    Object.entries(detallesObj.datos_modificados).forEach(([key, value]) => {
                        if (key !== 'avatar') {
                            const label = key.replace(/_/g, ' ').charAt(0).toUpperCase() +
                                        key.slice(1).replace(/_/g, ' ');
                            let displayValue = value;

                            // Si es rol, mostrar solo el nombre
                            if (key === 'rol' && typeof value === 'object') {
                                displayValue = value.nombre;
                            }

                            addRow(label, displayValue);
                        }
                    });
                }
                addRow('Fecha y Hora', new Date(detallesObj.timestamp).toLocaleString());
            }

            new bootstrap.Modal(document.getElementById('detallesModal')).show();
        }

        $(document).ready(function() {
            // Inicializar DataTables
            $('#content_ta').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json",
                    "paginate": {
                        "previous": "<i class='fa fa-angle-left'></i>",
                        "next": "<i class='fa fa-angle-right'></i>"
                    }
                },
                "order": [[0, "desc"]], // Esto ordena la primera columna (fecha) de forma descendente
                "pageLength": 15,
                "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                "lengthMenu": [[10, 15, 25, 50, -1], [10, 15, 25, 50, "Todos"]],
                "columnDefs": [
                    {
                        "targets": 0, // La columna de fecha (índice 0)
                        "type": "date", // Especifica que es una fecha
                        "render": function(data, type, row) {
                            if (type === 'sort') {
                                // Para ordenamiento, convierte la fecha a un formato que se pueda ordenar correctamente
                                return new Date(data).getTime();
                            }
                            return data;
                        }
                    }
                ]
            });

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    toastr.options = {
                        "positionClass": "toast-top-right",
                        "timeOut": 5000,
                    };
                    toastr.error("{{ $error }}");
                @endforeach
            @endif

            @if (Session::has('success'))
                toastr.options = {
                    "positionClass": "toast-bottom-right",
                };
                toastr.success("{{ Session::get('success') }}");
            @endif
        });
    </script>
@endsection
