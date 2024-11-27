@extends('layout.template')

@section('content')
    <div class="container mt-4">

        <a href="{{ route('gerencias.index') }}" class="btn btn-doc mb-3"><i class="fa fa-arrow-left" aria-hidden="true"></i>
            Volver
        </a>
        <div class="card">
            <div class="card-body">
                <p><strong>Nombre:</strong> {{ $gerencia->nombre }}</p>
                <p><strong>Descripción:</strong> {{ $gerencia->descripcion }}</p>
                <p><strong>Teléfono:</strong> {{ $gerencia->telefono }}</p>
                <p><strong>Dirección:</strong> {{ $gerencia->direccion }}</p>
                <p><strong>Estado:</strong> {{ $gerencia->estado }}</p>
                <p><strong>Gerente:</strong> {{ $gerencia->user->persona->nombres }}
                    {{ $gerencia->user->persona->apellido_p }} {{ $gerencia->user->persona->apellido_m }}</p>

                <div class="mt-4 d-flex justify-content-end">
                    @if (auth()->user()->rol->privilegios->contains('nombre', 'Acceso Total') || auth()->user()->rol->nombre === 'Gerente')
                        <a href="{{ route('gerencias.edit', $gerencia->id) }}" class="btn button_2 mx-1">
                            <i class="fa fa-edit"></i> Editar
                        </a>
                    @endif
                    @if (auth()->user()->rol->privilegios->contains('nombre', 'Acceso Total') ||
                            auth()->user()->rol->nombre === 'SuberAdmin')
                        <button type="button" class="btn btn-danger" onclick="showConfirmationModal()">
                            <i class="fa fa-trash" aria-hidden="true"></i> Eliminar
                        </button>
                    @endif

                    <div class="modal fade" tabindex="-1" role="dialog" id="confirmationModal">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Confirmar Eliminación</h5>
                                    <button type="button" class="btn-close" data-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>¿Estás seguro de eliminar esta gerencia? Esta acción no se puede deshacer.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary btn-no" data-dismiss="modal">No</button>
                                    <form action="{{ route('gerencias.destroy', $gerencia->id) }}" method="POST"
                                        class="d-inline-block">
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
                </div>
            </div>
        </div>

        <h4 class="d-flex justify-content-center mt-5 pt-serif-bold">Sub Usuarios</h4>
        @if (auth()->user()->rol->privilegios->contains('nombre', 'Acceso Total') ||
                auth()->user()->rol->nombre === 'Gerente' ||
                auth()->user()->rol->nombre === 'SubGerente')
            @if ($gerencia->subgerencias->count() > 0)
                {{-- Verifica si hay subgerencias --}}
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('subusuarios.create', ['gerencia' => $gerencia->id]) }}"
                        class="btn btn-doc pt-serif-regular">
                        <i class="fa fa-plus" aria-hidden="true"></i> Registrar
                    </a>
                </div>
            @endif
        @endif

        <table class="table">
            <thead>
                <tr class="pt-serif-bold">
                    <th class="">Nombre</th>
                    <th class="">Cargo</th>
                    <th class="">DNI</th>
                    <th class="">Rol</th>
                    <th class="">Sub Gerencia</th>
                    @if (auth()->user()->rol->privilegios->contains('nombre', 'Acceso Total') ||
                            auth()->user()->rol->privilegios->contains('nombre', 'Acceso a Gerencia'))
                        <th class="col-2">Acciones</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($gerencia->subgerencias as $subgerencia)
                    @foreach ($subgerencia->subusuarios as $subusuario)
                        <tr class="pt-serif-regular">
                            <td class="text-center">{{ $subusuario->user->persona->nombres }}
                                {{ $subusuario->user->persona->apellido_p }}
                                {{ $subusuario->user->persona->apellido_m }}</td>
                            <td class="text-center">{{ $subusuario->cargo }}</td>
                            <td class="text-center">{{ $subusuario->user->persona->dni }}</td>
                            <td class="text-center">{{ $subusuario->user->rol->nombre }}</td>
                            <td class="text-center">{{ $subgerencia->nombre }}</td>
                            @php
                                // Verificar si el usuario es el encargado de la gerencia
                                $esEncargadoGerencia = auth()->user()->id == $gerencia->usuario_id;

                                // Verificar si el usuario es el encargado de la subgerencia
                                $esEncargadoSubgerencia = false;
                                if (
                                    auth()->user()->subusuario &&
                                    auth()->user()->subusuario->subgerencia_id == $subusuario->subgerencia_id
                                ) {
                                    $esEncargadoSubgerencia =
                                        $subusuario->subgerencia->usuario_id == auth()->user()->id;
                                }

                                // Verificar si el usuario autenticado es el mismo que se está listando en la tabla
                                $esUsuarioActual = auth()->user()->id == $subusuario->user_id;
                            @endphp

                            @if (auth()->user()->rol->privilegios->contains('nombre', 'Acceso Total') ||
                                    $esEncargadoGerencia ||
                                    $esEncargadoSubgerencia ||
                                    $esUsuarioActual)
                                <td class="text-center">
                                    <!-- Mostrar el botón de editar siempre que se cumpla alguna de las condiciones -->
                                    <a href="{{ route('subusuarios.edit', [$gerencia->id, $subusuario->id]) }}"
                                        class="btn btn-sm button_2">
                                        <i class="fa fa-edit" style="line-height: 1;"></i> Editar
                                    </a>

                                    <!-- Mostrar el botón de eliminar solo si NO es el propio usuario -->
                                    @if (!$esUsuarioActual)
                                        <button type="button" class="btn btn-sm button_3"
                                            onclick="showSubusuarioConfirmationModal('{{ route('subusuarios.destroy', [$gerencia->id, $subusuario->id]) }}')">
                                            <i class="fa fa-trash" style="line-height: 1;"></i> Eliminar
                                        </button>
                                    @endif
                                </td>
                            @endif

                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>

        <div class="modal fade" tabindex="-1" role="dialog" id="subusuarioConfirmationModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmar Eliminación</h5>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Estás seguro de eliminar este subusuario? Esta acción no se puede deshacer.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-no" data-dismiss="modal">No</button>
                        <form id="subusuarioDeleteForm" method="POST" class="d-inline-block">
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

        <h4 class="d-flex justify-content-center pt-serif-bold mt-5">Sub Gerencias</h4>
        @if (auth()->user()->rol->privilegios->contains('nombre', 'Acceso Total') || auth()->user()->rol->nombre === 'Gerente')
            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('subgerencias.create', $gerencia->id) }}" class="btn btn-doc pt-serif-regular">
                    <i class="fa fa-plus" aria-hidden="true"></i> Registrar
                </a>
            </div>
        @endif
        <table class="table">
            <thead>
                <tr class="pt-serif-bold">
                    <th>Nombre</th>
                    <th>Encargado</th>
                    <th>Teléfono</th>
                    <!-- <th>Dirección</th> -->
                    <th>Estado</th>
                    @if (auth()->user()->rol->privilegios->contains('nombre', 'Acceso Total') ||
                            auth()->user()->rol->privilegios->contains('nombre', 'Acceso a Gerencia'))
                        <th class="col-2">Acciones</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($gerencia->subgerencias as $subgerencia)
                    <tr class="pt-serif-regular">
                        <td class="text-center">{{ $subgerencia->nombre }}</td>
                        <td class="text-center">{{ $subgerencia->user->persona->nombres }}
                            {{ $subgerencia->user->persona->apellido_p }}
                            {{ $subgerencia->user->persona->apellido_m }}</td>
                        <td class="text-center">{{ $subgerencia->telefono }}</td>
                        <!-- <td>{{ $subgerencia->direccion }}</td> -->
                        <td class="text-center">{{ $subgerencia->estado }}</td>
                        <td class="text-center">
                            @if (auth()->user()->rol->privilegios->contains('nombre', 'Acceso Total') ||
                                    auth()->user()->rol->nombre == 'Gerente' ||
                                    auth()->user()->id == $subgerencia->usuario_id)
                                <a href="{{ route('subgerencias.edit', [$gerencia->id, $subgerencia->id]) }}"
                                    class="btn btn-sm button_2">
                                    <i class="fa fa-edit" style="line-height: 1;"></i> Editar
                                </a>
                            @endif
                            @if (auth()->user()->rol->privilegios->contains('nombre', 'Acceso Total') || auth()->user()->rol->nombre === 'Gerente')
                                <button type="button" class="btn btn-sm button_3"
                                    onclick="showSubgerenciaConfirmationModal('{{ route('subgerencias.destroy', [$gerencia->id, $subgerencia->id]) }}')">
                                    <i class="fa fa-trash" style="line-height: 1;"></i> Eliminar
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="modal fade" tabindex="-1" role="dialog" id="subgerenciaConfirmationModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmar Eliminación</h5>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Estás seguro de eliminar esta subgerencia? Esta acción no se puede deshacer.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-no" data-dismiss="modal">No</button>
                        <form id="subgerenciaDeleteForm" method="POST" class="d-inline-block">
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

    </div>

    <script>
        $(document).ready(function() {
            @if (Session::has('success'))
                toastr.options = {
                    "positionClass": "toast-bottom-right"
                };
                toastr.success("{{ Session::get('success') }}");
            @endif
        });

        function showConfirmationModal() {
            $('#confirmationModal').modal('show');
        }

        function showSubgerenciaConfirmationModal(deleteUrl) {
            $('#subgerenciaDeleteForm').attr('action', deleteUrl);
            $('#subgerenciaConfirmationModal').modal('show');
        }

        $(document).ready(function() {
            $('.btn-close, .btn-no').click(function() {
                $('#confirmationModal').modal('hide');
                $('#subgerenciaConfirmationModal').modal('hide');
                $('#subusuarioConfirmationModal').modal('hide');
            });
        });

        function showSubusuarioConfirmationModal(url) {
            $('#subusuarioConfirmationModal').modal('show');
            $('#subusuarioDeleteForm').attr('action', url);
        }
    </script>
    </script>
@stop
