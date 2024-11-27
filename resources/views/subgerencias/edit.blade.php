@extends('layout.template')

@section('title', 'Editar Sub Gerencia')

@section('content')
    @php
        $usuario = auth()->user();
        $tieneAcceso =
            $usuario->rol->nombre === 'SuperAdmin' ||
            $gerencia->usuario_id === $usuario->id ||
            $subgerencia->usuario_id === $usuario->id;
    @endphp
    @if ($tieneAcceso)
        <div class="container mt-4">
            <div class="container col-md-4 card form_subgerencia">
                <div class="card-body">
                    <form
                        action="{{ route('subgerencias.update', ['gerencia' => $gerencia->id, 'subgerencia' => $subgerencia->id]) }}"
                        method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Primera columna -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nombre" class="form-label label_subgerencia">Nombre de la Sub
                                        Gerencia:</label>
                                    <input type="text" name="nombre" class="form-control subgerencia" id="nombre"
                                        value="{{ $subgerencia->nombre }}" required>
                                </div>

                                <div class="form-group mt-3">
                                    <label for="descripcion" class="form-label label_subgerencia">Descripción:</label>
                                    <textarea name="descripcion" class="form-control subgerencia" id="descripcion" rows="4" required>{{ $subgerencia->descripcion }}</textarea>
                                </div>
                            </div>

                            <!-- Segunda columna -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="telefono" class="form-label label_subgerencia">Teléfono:</label>
                                    <input type="tel" name="telefono" class="form-control subgerencia" id="telefono"
                                        value="{{ $subgerencia->telefono }}" required pattern="^\d{9}$" maxlength="9"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                </div>

                                <div class="row">
                                    <div class="form-group mt-1 col-md-8">
                                        <label for="usuario_id" class="form-label label_subgerencia">Encargado:</label>
                                        <select name="usuario_id" class="form-control subgerencia" id="usuario_id" required>
                                            @foreach ($users as $user)
                                                @if ($user->id == $gerencia->usuario_id)
                                                    <option value="{{ $user->id }}"
                                                        {{ $subgerencia->usuario_id == $user->id ? 'selected' : '' }}>
                                                        {{ $user->persona->nombres }} {{ $user->persona->apellido_p }}
                                                        {{ $user->persona->apellido_m }}
                                                    </option>
                                                @else
                                                    <option value="{{ $user->id }}"
                                                        {{ $subgerencia->usuario_id == $user->id ? 'selected' : '' }}>
                                                        {{ $user->persona->nombres }} {{ $user->persona->apellido_p }}
                                                        {{ $user->persona->apellido_m }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group mt-1 col-md-4">
                                        <label for="estado" class="form-label label_subgerencia">Estado:</label>
                                        <select name="estado" class="form-control subgerencia" id="estado" required>
                                            <option value="Activo"
                                                {{ $subgerencia->estado == 'Activo' ? 'selected' : '' }}>
                                                Activo</option>
                                            <option value="Inactivo"
                                                {{ $subgerencia->estado == 'Inactivo' ? 'selected' : '' }}>Inactivo
                                            </option>
                                        </select>
                                    </div>

                                    <div class="form-group mt-3">
                                        <label for="direccion" class="form-label label_subgerencia">Dirección:</label>
                                        <input type="text" name="direccion" class="form-control subgerencia"
                                            id="direccion" value="{{ $subgerencia->direccion }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-center">
                            <a href="{{ route('gerencias.show', $gerencia->id) }}"
                                class="btn button_2 me-2"><i class="fa fa-arrow-circle-left"
                                    aria-hidden="true"></i> Cancelar
                            </a>
                            <button type="submit" class="btn button_1 ms-2"><i class="fa fa-save"
                                    aria-hidden="true"></i> Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @else
        <div class="container mt-4">
            <div class="alert alert-danger">
                No tienes permiso para editar esta subgerencia.
            </div>
        </div>
    @endif
@stop
