@extends('layout.template')

@section('title', 'Editar Subusuario')

@section('content')
    <div class="container mt-4 form_subusuario">
        <form id="editForm" action="{{ route('subusuarios.update', ['gerencia' => $gerencia->id, 'subgerencia' => $subgerencia->id, 'subusuario' => $subusuario->id]) }}" method="POST" class="form_persona_user" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row forms">
                <div class="col-md-6">
                    <div class="row">
                        <div class="form-group mt-3 text-center">
                            <label for="avatar" class="form-label label_subusuario">Imagen de Perfil</label>
                            <div class="d-flex justify-content-center">
                                <img id="avatarPreview" src="{{ $subusuario->user->persona->avatar ? asset('storage/' . $subusuario->user->persona->avatar) : asset('images/logo/avatar.png') }}" alt="Previsualización"
                                    class="img-thumbnail mb-2" style="width: 200px; height: 200px" />
                            </div>
                            <input type="file" class="form-control subusuario" id="avatar" name="avatar" accept="image/*"
                                onchange="previewImage(event)">
                        </div>
                        <div class="form-group mt-3 col-md-6">
                            <label for="nombre_usuario" class="form-label label_subusuario">Nombre de Usuario:</label>
                            <input type="text" class="form-control subusuario" id="nombre_usuario" name="nombre_usuario" value="{{ $subusuario->user->nombre_usuario }}" required>
                        </div>
                        <div class="form-group mt-3 col-md-6">
                            <label for="email" class="form-label label_subusuario">Email:</label>
                            <input type="email" class="form-control subusuario" id="email" name="email" value="{{ $subusuario->user->email }}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group mt-2 col-md-6">
                            <label for="rol_id" class="form-label label_subusuario">Rol:</label>
                            <select class="form-control subusuario" id="rol_id" name="rol_id" required>
                                @foreach($roles as $rol)
                                    @if ($rol->nombre !== 'SuperAdmin' && $rol->nombre !== 'Gerente')
                                        <option value="{{ $rol->id }}" {{ $subusuario->user->rol_id == $rol->id ? 'selected' : '' }}>
                                            {{ $rol->nombre }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mt-2 col-md-6">
                            <label for="estado" class="form-label label_subusuario">Estado:</label>
                            <select class="form-control subusuario" id="estado" name="estado" required>
                                <option value="activo" {{ $subusuario->user->estado == 'activo' ? 'selected' : '' }}>Activo</option>
                                <option value="inactivo" {{ $subusuario->user->estado == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mt-2">
                        <label for="nombres" class="form-label label_subusuario">Nombres:</label>
                        <input type="text" class="form-control subusuario" id="nombres" name="nombres" value="{{ $subusuario->user->persona->nombres }}" required>
                    </div>
                    <div class="row mt-2">
                        <div class="form-group col-md-6">
                            <label for="apellido_p" class="form-label label_subusuario">Apellido Paterno:</label>
                            <input type="text" class="form-control subusuario" id="apellido_p" name="apellido_p" value="{{ $subusuario->user->persona->apellido_p }}" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="apellido_m" class="form-label label_subusuario">Apellido Materno:</label>
                            <input type="text" class="form-control subusuario" id="apellido_m" name="apellido_m" value="{{ $subusuario->user->persona->apellido_m }}" required>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="form-group col-md-6">
                            <label for="dni" class="form-label label_subusuario">DNI:</label>
                            <input type="text" class="form-control subusuario" id="dni" name="dni"
                                value="{{ $subusuario->user->persona->dni }}" required
                                pattern="\d{8}" title="El DNI debe contener exactamente 8 dígitos numéricos."
                                maxlength="8"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="f_nacimiento" class="form-label label_subusuario">Fecha de Nacimiento:</label>
                            <input type="date" class="form-control subusuario" id="f_nacimiento" name="f_nacimiento"
                                value="{{ $subusuario->user->persona->f_nacimiento }}" required
                                max="{{ \Carbon\Carbon::now()->subYears(15)->format('Y-m-d') }}"
                                min="{{ \Carbon\Carbon::now()->subYears(100)->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group  col-md-5">
                            <label for="celular" class="form-label label_subusuario">Celular:</label>
                            <input type="text" class="form-control subusuario" id="celular" name="celular"
                                value="{{ $subusuario->user->persona->celular }}" required
                                pattern="\d{9}" title="El celular debe contener exactamente 9 dígitos numéricos."
                                maxlength="9"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                        </div>
                        <div class="form-group col-md-7">
                            <label for="direccion" class="form-label label_subusuario">Dirección:</label>
                            <input type="text" class="form-control subusuario" id="direccion" name="direccion" value="{{ $subusuario->user->persona->direccion }}" required>
                        </div>
                    </div>
                    <div class="form-group mt-2">
                        <label for="subgerencia_id" class="form-label label_subusuario">Subgerencia:</label>
                        <select class="form-control subusuario" id="subgerencia_id" name="subgerencia_id" required>
                            @foreach($subgerencias as $subgerencia)
                                <option value="{{ $subgerencia->id }}" {{ $subusuario->subgerencia_id == $subgerencia->id ? 'selected' : '' }}>{{ $subgerencia->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mt-2">
                        <label for="cargo" class="form-label label_subusuario">Cargo:</label>
                        <input type="text" class="form-control subusuario" id="cargo" name="cargo" value="{{ $subusuario->cargo }}" required>
                    </div>
                </div>
            </div>

            <div class="mt-3 botones_form_persona text-center">
                <a href="{{ route('gerencias.show', $gerencia->id) }}" class="btn button_2 me-2">
                    <i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Cancelar
                </a>
                <a href="{{ route('usuarios.cambiarContrasena', $subusuario->user->id) }}" class="btn button_3 ms-2"><i class="fa fa-unlock-alt" aria-hidden="true"></i>
                    Cambiar Contraseña</a>
                <button type="submit" class="btn button_1 ms-2">
                    <i class="fa fa-save" aria-hidden="true"></i> Guardar Cambios</button>
            </div>
        </form>
    </div>
    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('avatarPreview');
                output.src = reader.result;
                output.style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection
