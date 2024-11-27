@extends('layout.template')

@section('title', 'Editar Gerencia')

@section('content')
    @php
        $usuario = auth()->user();
        $tienePermiso =
            $gerencia->usuario_id === $usuario->id || $usuario->rol->privilegios->contains('nombre', 'Acceso Total');
    @endphp
        @if($tienePermiso)
    <div class="container">
        <div class="container col-md-4 card form_gerencia">
            <div class="col-md-12">
                <form action="{{ route('gerencias.update', $gerencia->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- Primera columna -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre" class="form-label label_gerencia">Nombre de la Gerencia:</label>
                                <input type="text" name="nombre" class="form-control gerencia" id="nombre"
                                    value="{{ $gerencia->nombre }}" required>
                            </div>
                            <div class="form-group">
                                <label for="descripcion" class="form-label label_gerencia">Descripción:</label>
                                <textarea name="descripcion" class="form-control gerencia" id="descripcion" rows="4" required>{{ $gerencia->descripcion }}</textarea>
                            </div>
                            <div class="form-group mt-3">
                                <label for="telefono" class="form-label label_gerencia">Teléfono:</label>
                                <input type="tel" name="telefono" class="form-control gerencia" id="telefono"
                                    value="{{ $gerencia->telefono }}" required pattern="^\d{9}$" maxlength="9"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                            </div>
                        </div>

                        <!-- Segunda columna -->
                        <div class="col-md-6">
                            <div class="form-group mt-3">
                                <label for="direccion" class="form-label label_gerencia">Dirección:</label>
                                <input type="text" name="direccion" class="form-control gerencia" id="direccion"
                                    value="{{ $gerencia->direccion }}" required>
                            </div>

                            <div class="form-group mt-3">
                                <label for="gerente_id" class="form-label label_gerencia">Gerente (User):</label>
                                <select name="gerente_id" class="form-control gerencia" id="gerente_id" required>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ $gerencia->gerente_id == $user->id ? 'selected' : '' }}>
                                            {{ $user->persona->nombres }} {{ $user->persona->apellido_p }}
                                            {{ $user->persona->apellido_m }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mt-3">
                                <label for="estado" class="form-label label_gerencia">Estado:</label>
                                <select name="estado" class="form-control gerencia" id="estado" required>
                                    <option value="Activo" {{ $gerencia->estado == 'Activo' ? 'selected' : '' }}>Activo
                                    </option>
                                    <option value="Inactivo" {{ $gerencia->estado == 'Inactivo' ? 'selected' : '' }}>
                                        Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 text-center">
                        <a href="{{ route('gerencias.show', $gerencia->id) }}"
                            class="btn button_2 me-2"><i class="fa fa-arrow-circle-left"
                                aria-hidden="true"></i> Cancelar</a>
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
                No tienes permiso para editar esta gerencia.
            </div>
        </div>
    @endif
@stop
