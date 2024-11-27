@extends('layout/template')

@section('title', 'Editar Usuario')

@section('content')
    <div class="container mt-4 form_persona">
        <form id="editForm" action="{{ route('usuarios.update', $user->id) }}" method="POST" class="form_persona_user" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row forms">
                <!-- Imagen de Perfil -->
                <div class="col-md-4 d-flex flex-column align-items-center">
                    <div class="form-group text-center">
                        <label for="avatar" class="form-label label_persona">Imagen de Perfil</label>
                        <!-- Contenedor centrado para la previsualización -->
                        <div class="d-flex justify-content-center">
                            <!-- Imagen para previsualización -->
                            <img id="avatarPreview" src="{{ $user->persona->avatar ? asset('storage/' . $user->persona->avatar) : asset('images/logo/avatar.png') }}" alt="Previsualización" class="img-thumbnail mb-2" style="width: 200px; height: 200px" />
                        </div>
                        <input type="file" class="form-control persona" id="avatar" name="avatar" accept="image/*" onchange="previewImage(event)">
                    </div>
                </div>

                <!-- Formulario Usuario -->
                <div class="col-md-8">
                        <!-- Nombre de Usuario -->
                        <div class="form-group mt-3">
                            <label for="nombre_usuario" class="form-label label_persona">Nombre de Usuario:</label>
                            <input type="text" class="form-control persona" id="nombre_usuario" name="nombre_usuario" value="{{ old('nombre_usuario', $user->nombre_usuario) }}" required>
                            @error('nombre_usuario')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mt-3">
                            <label for="email" class="form-label label_persona">Correo:</label>
                            <input type="email" class="form-control persona" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                    <div class="row mt-3">
                        <!-- Estado -->
                        <div class="form-group col-md-6">
                            <label for="estado" class="form-label label_persona">Estado:</label>
                            <select name="estado" id="estado" class="form-control persona" required>
                                <option value="Activo" {{ old('estado', $user->estado) == 'Activo' ? 'selected' : '' }}>Activo</option>
                                <option value="Inactivo" {{ old('estado', $user->estado) == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            @error('estado')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Rol -->
                        <div class="form-group col-md-6">
                            <label for="rol_id" class="form-label label_persona">Rol:</label>
                            <select name="rol_id" id="rol_id" class="form-control persona" required>
                                @if(auth()->user()->rol->nombre === 'SuperAdmin')
                                    @foreach($roles as $rol)
                                        <option value="{{ $rol->id }}" {{ old('rol_id', $user->rol_id) == $rol->id ? 'selected' : '' }}>
                                            {{ $rol->nombre }}
                                        </option>
                                    @endforeach
                                @elseif(auth()->user()->rol->nombre === 'Gerente')
                                    @foreach($roles as $rol)
                                        @if($rol->nombre !== 'SuperAdmin')
                                            <option value="{{ $rol->id }}" {{ old('rol_id', $user->rol_id) == $rol->id ? 'selected' : '' }}>
                                                {{ $rol->nombre }}
                                            </option>
                                        @endif
                                    @endforeach
                                @elseif(auth()->user()->rol->nombre === 'SubGerente')
                                    @foreach($roles as $rol)
                                        @if($rol->nombre !== 'SuperAdmin' && $rol->nombre !== 'Gerente')
                                            <option value="{{ $rol->id }}" {{ old('rol_id', $user->rol_id) == $rol->id ? 'selected' : '' }}>
                                                {{ $rol->nombre }}
                                            </option>
                                        @endif
                                    @endforeach
                                @else
                                    @foreach($roles as $rol)
                                        @if($rol->id == auth()->user()->rol_id)
                                            <option value="{{ $rol->id }}" selected>
                                                {{ $rol->nombre }}
                                            </option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                            @error('rol_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>
            </div>

            <div class="mt-3 botones_form_persona text-center">
                <a href="{{ route('usuarios.index') }}" class="btn button_2 me-2"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Cancelar</a>
                <a href="{{ route('usuarios.cambiarContrasena', $user->id) }}" class="btn button_1 ms-2"><i class="fa fa-unlock-alt" aria-hidden="true"></i>
                    Cambiar Contraseña</a>
                <button type="submit" class="btn button_3 ms-2"><i class="fa fa-save" aria-hidden="true"></i> Guardar Cambios</button>
            </div>
        </form>
    </div>

    <!-- Script para previsualización de la imagen -->
    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('avatarPreview');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection
