@extends('layout.template')

@section('title', 'Registrar Subusuario')

@section('content')
    <div class="container mt-4 form_subusuario">
        <form id="registrationForm" action="{{ route('subusuarios.store', ['gerencia' => $gerencia->id]) }}" method="POST"
            class="form_persona_user" enctype="multipart/form-data">
            @csrf
            <div class="row forms">
                <div class="col-md-6">
                    <div class="row">
                        <!-- Imagen de Perfil -->
                        <div class="form-group mt-3">
                            <label for="avatar" class="form-label label_subusuario">Imagen de Perfil</label>
                            <div class="d-flex justify-content-center">
                                <img id="avatarPreview" src="{{ asset('images/logo/avatar.png') }}" alt="Previsualización"
                                    class="img-thumbnail mb-2" style="width: 200px; height: 200px" />
                            </div>
                            <input type="file" class="form-control subusuario" id="avatar" name="avatar" accept="image/*" onchange="previewImage(event)">
                        </div>
                        <div class="form-group mt-3 col-md-6">
                            <label for="nombre_usuario" class="form-label label_subusuario">Nombre de Usuario:</label>
                            <input type="text" class="form-control subusuario" id="nombre_usuario" name="nombre_usuario"
                                value="{{ old('nombre_usuario') }}" required>
                        </div>
                        <div class="form-group mt-3 col-md-6">
                            <label for="email" class="form-label label_subusuario">Email:</label>
                            <input type="email" class="form-control subusuario" id="email" name="email"
                                value="{{ old('email') }}" required>
                        </div>
                    </div>
                    <div class="form-group mt-2">
                        <label for="password" class="form-label label_subusuario">Contraseña:</label>
                        <input type="password" class="form-control subusuario" id="password" name="password" required>
                        @error('password')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="form-group mt-2 col-md-6">
                            <label for="rol_id" class="form-label label_subusuario">Rol:</label>
                            <select class="form-control subusuario" id="rol_id" name="rol_id" required>
                                @foreach ($roles as $rol)
                                    @if ($rol->nombre !== 'SuperAdmin' && $rol->nombre !== 'Gerente' && $rol->nombre !== 'SubGerente')
                                        <option value="{{ $rol->id }}"
                                            {{ old('rol_id') == $rol->id ? 'selected' : '' }}>
                                            {{ $rol->nombre }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mt-2 col-md-6">
                            <label for="estado" class="form-label label_subusuario">Estado:</label>
                            <select class="form-control subusuario" id="estado" name="estado" required>
                                <option value="activo" selected>Activo</option>
                                <option value="inactivo" {{ old('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mt-2">
                        <label for="nombres" class="form-label label_subusuario">Nombres:</label>
                        <input type="text" class="form-control subusuario" id="nombres" name="nombres"
                            value="{{ old('nombres') }}" required>
                    </div>
                    <div class="row mt-2">
                        <div class="form-group col-md-6">
                            <label for="apellido_p" class="form-label label_subusuario">Apellido Paterno:</label>
                            <input type="text" class="form-control subusuario" id="apellido_p" name="apellido_p"
                                value="{{ old('apellido_p') }}" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="apellido_m" class="form-label label_subusuario">Apellido Materno:</label>
                            <input type="text" class="form-control subusuario" id="apellido_m" name="apellido_m"
                                value="{{ old('apellido_m') }}" required>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="form-group col-md-6">
                            <label for="dni" class="form-label label_subusuario">DNI:</label>
                            <input type="text" class="form-control subusuario" id="dni" name="dni"
                                value="{{ old('dni') }}" required pattern="[0-9]+" maxlength="8"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="f_nacimiento" class="form-label label_subusuario">Fecha de Nacimiento:</label>
                            <input type="date" class="form-control subusuario" id="f_nacimiento" name="f_nacimiento"
                                value="{{ old('f_nacimiento') }}" required
                                max="{{ \Carbon\Carbon::now()->subYears(15)->format('Y-m-d') }}"
                                min="{{ \Carbon\Carbon::now()->subYears(100)->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="celular" class="form-label label_subusuario">Celular:</label>
                        <input type="text" class="form-control subusuario" id="celular" name="celular"
                            value="{{ old('celular') }}" required pattern="[0-9]+" maxlength="9"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                    </div>

                    <div class="form-group mt-2">
                        <label for="direccion" class="form-label label_subusuario">Dirección:</label>
                        <input type="text" class="form-control subusuario" id="direccion" name="direccion"
                            value="{{ old('direccion') }}" required>
                    </div>

                    <div class="form-group mt-2">
                        <label for="subgerencia_id" class="form-label label_subusuario">Subgerencia:</label>
                        <select class="form-control subusuario" id="subgerencia_id" name="subgerencia_id" required>
                            @foreach ($subgerencias as $subgerencia)
                                <option value="{{ $subgerencia->id }}"
                                    {{ old('subgerencia_id') == $subgerencia->id ? 'selected' : '' }}>
                                    {{ $subgerencia->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mt-2">
                        <label for="cargo" class="form-label label_subusuario">Cargo:</label>
                        <input type="text" class="form-control subusuario" id="cargo" name="cargo"
                            value="{{ old('cargo') }}" required>
                    </div>
                </div>
            </div>

            <div class="mt-3 botones_form_persona text-center">
                <a href="{{ route('gerencias.show', $gerencia->id) }}" class="btn button_2 me-2">
                    <i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Cancelar
                </a>
                <button type="submit" class="btn button_1 ms-2">
                    <i class="fa fa-plus" aria-hidden="true"></i> Crear
                </button>
            </div>
        </form>
    </div>
    <script>
        // Función para previsualizar la imagen seleccionada
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('avatarPreview');
                output.src = reader.result;
                output.style.display = 'block'; // Muestra la imagen después de seleccionarla
            };
            reader.readAsDataURL(event.target.files[0]);
        }

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
@endsection
