@extends('layout/template')

@section('title', 'Lista de Roles')

@section('content')
    <div class="container card-body mt-4">
        <div id="content_ta_wrapper" class="dataTables_wrapper">
            <div class="table-responsive">
                @if (auth()->user()->rol->privilegios->contains('nombre', 'Acceso Total'))
                    <a href="{{ route('roles.create') }}" class="btn btn-doc mb-3"><i class="fa fa-plus" aria-hidden="true"></i>
                        Crear Nuevo Rol</a>
                @endif
                <table id="content_ta" class="table table-striped table-hover custom-table pt-serif-regular" role="grid"
                    aria-describedby="content_ta_info">
                    <thead>
                        <tr role="row">
                            <th classs="text-center">ID</th>
                            <th classs="text-center">Nombre</th>
                            <th classs="text-center">Descripción</th>
                            <th classs="text-center">Privilegios</th>
                            @if (auth()->user()->rol->privilegios->contains('nombre', 'Acceso Total'))
                                <th>Acciones</th>
                            @endif

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                            <tr>
                                <td class="text-center">{{ $role->id }}</td>
                                <td class="text-center">{{ $role->nombre }}</td>
                                <td class="text-center">{{ Str::limit($role->descripcion, 50) }}</td>
                                <td class="text-center">
                                    @forelse ($role->privilegios as $privilegio)
                                        <span class="text badge text-bg-secondary">{{ $privilegio->nombre }}</span>
                                    @empty
                                        <span class="text-muted">Sin privilegios</span>
                                    @endforelse
                                </td>
                                @if (auth()->user()->rol->privilegios->contains('nombre', 'Acceso Total'))
                                    <td class="text-center">
                                        <a href="{{ route('roles.edit', $role->id) }}" class="btn button_2">
                                            <i class="fa fa-edit" aria-hidden="true"></i>
                                        </a>
                                        <button type="button" class="btn button_3"
                                            onclick="showUserConfirmationModal('{{ route('roles.destroy', $role->id) }}')">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                @endif
                                <!-- Modal para confirmar la eliminación -->
                                <div class="modal fade" tabindex="-1" role="dialog" id="userConfirmationModal">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirmar Eliminación</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>¿Estás seguro de eliminar este rol? Esta acción no se puede deshacer.
                                                </p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary btn-no"
                                                    data-bs-dismiss="modal">No</button>
                                                <form id="userDeleteForm" method="POST" class="d-inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="fa fa-trash"></i> Sí
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
        function showUserConfirmationModal(actionUrl) {
            // Establecer la acción del formulario
            document.getElementById('userDeleteForm').action = actionUrl;

            // Abrir el modal
            $('#userConfirmationModal').modal('show');
        }
    </script>
@endsection
