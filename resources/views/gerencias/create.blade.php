@extends('layout.template')

@section('title', 'Crear Gerencia')

@section('content')
    <div class="container">
        <div class="row">
            <div class="container col-md-4 card form_gerencia">
                <div class="col-md-12">
                    <form action="{{ route('gerencias.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <!-- Primera columna -->
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="nombre" class="form-label label_gerencia">Nombre de la Gerencia:</label>
                                    <input type="text" name="nombre" class="form-control gerencia" id="nombre" required>
                                </div>
                                <div class="form-group">
                                    <label for="descripcion" class="form-label label_gerencia">Descripción:</label>
                                    <textarea name="descripcion" class="form-control gerencia" id="descripcion" rows="4" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="telefono" class="form-label label_gerencia">Teléfono:</label>
                                    <input type="tel" name="telefono" class="form-control gerencia" id="telefono" required pattern="^\d{9}$" maxlength="9" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                </div>
                            </div>

                            <!-- Segunda columna -->
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="direccion" class="form-label label_gerencia">Dirección:</label>
                                    <input type="text" name="direccion" class="form-control gerencia" id="direccion" required>
                                </div>
                                <div class="form-group mt-3">
                                    <label for="usuario_id" class="form-label label_gerencia">Gerente (User):</label>
                                    <select name="usuario_id" class="form-control gerencia" id="usuario_id" required>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->persona->nombres }} {{ $user->persona->apellido_p }} {{ $user->persona->apellido_m }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mt-3">
                                    <label for="estado" class="form-label label_gerencia">Estado:</label>
                                    <select name="estado" class="form-control gerencia" id="estado" required>
                                        <option value="Activo">Activo</option>
                                        <option value="Inactivo">Inactivo</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-center">
                            <a href="{{ route('gerencias.index') }}" class="btn button_2 me-2"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Cancelar</a>
                            <button type="submit" class="btn button_1 ms-2"><i class="fa fa-plus" aria-hidden="true"></i> Crear</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
