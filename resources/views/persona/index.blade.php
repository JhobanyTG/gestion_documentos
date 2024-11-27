@extends('layout/template')

@section('title', 'Lista de Personas')

@section('content')
    <div class="card-body mt-3 p-2">
        <div id="content_ta_wrapper" class="dataTables_wrapper">
            <div class="table-responsive">
                @if (auth()->user()->rol->privilegios->contains('nombre', 'Acceso Total'))
                    <a href="{{ route('personas.create') }}" class="btn btn-doc mb-3">
                        <i class="fa fa-plus" aria-hidden="true"></i> Registrar Persona y Usuario
                    </a>
                @endif
                <table id="content_ta" class="table table-striped mt-4 table-hover custom-table pt-serif-regular"
                    role="grid" aria-describedby="content_ta_info">
                    <thead>
                        <tr role="row">
                            <th class="text-center">DNI</th>
                            <th class="text-center">Imagen</th>
                            <th class="text-center">Nombres</th>
                            <th class="text-center">Apellido Paterno</th>
                            <th class="text-center">Apellido Materno</th>
                            <th class="text-center">Fecha de Nacimiento</th>
                            <th class="text-center">Celular</th>
                            <th class="text-center">Dirección</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($personas as $persona)
                            <tr class="odd">
                                <td class="text-center">{{ $persona->dni }}</td>
                                <td class="text-center">
                                    <img src="{{ $persona->avatar ? asset('storage/' . $persona->avatar) : asset('images/logo/avatar.png') }}"
                                        alt="{{ $persona->nombres }}" width="100">
                                </td>
                                <td class="text-center">{{ $persona->nombres }}</td>
                                <td class="text-center">{{ $persona->apellido_p }}</td>
                                <td class="text-center">{{ $persona->apellido_m }}</td>
                                <td class="text-center">{{ $persona->f_nacimiento }}</td>
                                <td class="text-center">{{ $persona->celular }}</td>
                                <td class="text-center">{{ $persona->direccion }}</td>
                                <td class="text-center">
                                    @php
                                        // Obtener el usuario autenticado
                                        $usuarioAutenticado = auth()->user();

                                        // Verificar si el usuario autenticado es el mismo que el usuario listado en personas
                                        $esSuPropioRegistro = $usuarioAutenticado->id == $persona->id;

                                        // Verificar si el usuario autenticado tiene el privilegio de Acceso Total
                                        $tieneAccesoTotal = $usuarioAutenticado->rol->privilegios->contains(
                                            'nombre',
                                            'Acceso Total',
                                        );

                                        // Verificar si el usuario autenticado está en la misma gerencia que el usuario de la persona
                                        $mismaGerencia =
                                            $usuarioAutenticado->gerencia_id === $persona->user->gerencia_id ||
                                            ($usuarioAutenticado->subgerencia &&
                                                $usuarioAutenticado->subgerencia->gerencia_id ===
                                                    $persona->user->gerencia_id);

                                        // Verificar si el usuario autenticado es encargado de gerencia
                                        $esEncargadoAutenticado = false;
                                        if ($usuarioAutenticado->gerencia) {
                                            $esEncargadoAutenticado =
                                                $usuarioAutenticado->id === $usuarioAutenticado->gerencia->usuario_id;
                                        }

                                        // Verificar si la persona listada es el encargado de la gerencia
                                        $esEncargadoGerencia = false;
                                        if ($persona->user->gerencia) {
                                            $esEncargadoGerencia =
                                                $persona->user->id === $persona->user->gerencia->usuario_id;
                                        }

                                        // Verificar si el usuario autenticado tiene el rol de SubUsuario
                                        $esSubUsuario = $usuarioAutenticado->rol->nombre === 'SubUsuario';
                                    @endphp

                                    {{-- Mostrar los botones si:
    - El usuario tiene Acceso Total
    - Es su propio registro
    - Es encargado de gerencia (nuevo)
    - O está en la misma gerencia y la persona listada no es el encargado de la gerencia --}}
                                    @if (!$esSubUsuario && ($tieneAccesoTotal || $esSuPropioRegistro || ($mismaGerencia && !$esEncargadoGerencia)))
                                        <a class="btn button_1" href="{{ route('personas.show', $persona->id) }}">
                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                        </a>
                                        <a class="btn button_2" href="{{ route('personas.edit', $persona->id) }}">
                                            <i class="fa fa-edit" aria-hidden="true"></i>
                                        </a>
                                        <button type="button" class="btn button_3"
                                            onclick="showUserConfirmationModal('{{ route('personas.destroy', $persona->id) }}')">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </button>
                                    @elseif ($esSubUsuario && $esSuPropioRegistro)
                                        <a class="btn button_1" href="{{ route('personas.show', $persona->id) }}">
                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                        </a>
                                        <a class="btn button_2" href="{{ route('personas.edit', $persona->id) }}">
                                            <i class="fa fa-edit" aria-hidden="true"></i>
                                        </a>
                                    @endif

                                </td>
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
                                                <p>¿Estás seguro de eliminar esta persona? Esta acción no se puede deshacer.
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
