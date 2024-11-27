@extends('layout/template')

@section('title', 'Registrar Persona y Usuario')

@section('content')
    <div class="container mt-4 form_persona">
        <form id="registrationForm" action="{{ route('personas.store') }}" method="POST" class="form_persona_user"
            enctype="multipart/form-data">
            @csrf
            <div class="row forms">
                <!-- Formulario Usuario -->
                <div class="col-md-6">
                    <div class="form-group mt-3">
                        <label for="avatar" class="form-label label_persona">Imagen de Perfil</label>
                        <!-- Contenedor centrado para la previsualización -->
                        <div class="d-flex justify-content-center">
                            <!-- Imagen para previsualización, con una imagen por defecto -->
                            <img id="avatarPreview" src="{{ asset('images/logo/avatar.png') }}" alt="Previsualización"
                                class="img-thumbnail mb-2" style="width: 200px; height: 200px" />
                        </div>
                        <input type="file" class="form-control persona" id="avatar" name="avatar" accept="image/*"
                            onchange="previewImage(event)">
                    </div>
                    <div class="row mt-2">
                        <div class="form-group col-md-6">
                            <label for="nombre_usuario" class="form-label label_persona mt-2">Nombre de Usuario:</label>
                            <input type="text" class="form-control persona" id="nombre_usuario" name="nombre_usuario"
                                value="{{ old('nombre_usuario') }}" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="email" class="form-label label_persona mt-2">Email:</label>
                            <input type="email" class="form-control persona" id="email" name="email"
                                value="{{ old('email') }}" required>
                        </div>
                    </div>
                    <div class="form-group mt-2">
                        <label for="password" class="form-label label_persona">Contraseña:</label>
                        <input type="password" class="form-control persona" id="password" name="password" required>
                        @error('password')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <!-- Formulario Persona -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nombres" class="form-label label_persona">Nombres:</label>
                        <input type="text" class="form-control persona" id="nombres" name="nombres"
                            value="{{ old('nombres') }}" required>
                    </div>
                    <div class="row mt-2">
                        <div class="form-group col-md-6">
                            <label for="apellido_p" class="form-label label_persona">Apellido Paterno:</label>
                            <input type="text" class="form-control persona" id="apellido_p" name="apellido_p"
                                value="{{ old('apellido_p') }}" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="apellido_m" class="form-label label_persona">Apellido Materno:</label>
                            <input type="text" class="form-control persona" id="apellido_m" name="apellido_m"
                                value="{{ old('apellido_m') }}" required>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="form-group col-md-6">
                            <label for="dni" class="form-label label_persona">DNI:</label>
                            <input type="text" class="form-control persona" id="dni" name="dni"
                                value="{{ old('dni', $persona->dni ?? '') }}" required pattern="^\d{8}$"
                                maxlength="8" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="f_nacimiento" class="form-label label_persona">Fecha de Nacimiento:</label>
                            <input type="date" class="form-control persona" id="f_nacimiento" name="f_nacimiento"
                                value="{{ old('f_nacimiento', $persona->f_nacimiento ?? '') }}" required
                                max="{{ \Carbon\Carbon::now()->subYears(15)->format('Y-m-d') }}"
                                min="{{ \Carbon\Carbon::now()->subYears(100)->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="form-group mt-2">
                        <label for="celular" class="form-label label_persona">Celular:</label>
                        <input type="text" class="form-control persona" id="celular" name="celular"
                            value="{{ old('celular', $persona->celular ?? '') }}" required pattern="^\d{9}$"
                            maxlength="9" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                    </div>
                    <div class="form-group mt-2">
                        <label for="direccion" class="form-label label_persona">Dirección:</label>
                        <input type="text" class="form-control persona" id="direccion" name="direccion"
                            value="{{ old('direccion') }}" required>
                    </div>
                    <div class="row mt-2">
                        <div class="form-group col-md-6">
                            <label for="rol_id" class="form-label label_persona">Rol:</label>
                            <select class="form-control persona" id="rol_id" name="rol_id" required>
                                @foreach ($roles as $rol)
                                    @if ($rol->nombre === 'SuperAdmin' || $rol->nombre === 'Gerente')
                                        <option value="{{ $rol->id }}"
                                            {{ old('rol_id') == $rol->id ? 'selected' : '' }}>
                                            {{ $rol->nombre }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="estado" class="form-label label_persona">Estado:</label>
                            <select class="form-control persona" id="estado" name="estado" required>
                                <option value="Activo" selected>Activo</option>
                                <option value="Inactivo" {{ old('estado') == 'Inactivo' ? 'selected' : '' }}>Inactivo
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3 botones_form_persona text-center">
                <a href="{{ url('personas') }}" class="btn button_2 me-2"><i class="fa fa-arrow-circle-left"
                        aria-hidden="true"></i> Cancelar</a>
                <button type="submit" class="btn button_1 ms-2"><i class="fa fa-plus"
                        aria-hidden="true"></i>
                    Crear</button>

            </div>
        </form>
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
