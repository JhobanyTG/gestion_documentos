@extends('layout/template')

@section('title', 'Editar Persona')

@section('content')
    <div class="container mt-4 form_persona">
        <form id="editForm" action="{{ route('personas.update', $persona->id) }}" method="POST" class="form_persona_user"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row forms">
                <!-- Imagen de Perfil -->
                <div class="col-md-4 d-flex flex-column align-items-center">
                    <div class="form-group mt-3 text-center">
                        <label for="avatar" class="form-label label_persona">Imagen de Perfil</label>
                        <!-- Contenedor centrado para la previsualización -->
                        <div class="d-flex justify-content-center">
                            <!-- Imagen para previsualización -->
                            <img id="avatarPreview"
                                src="{{ $persona->avatar ? asset('storage/' . $persona->avatar) : asset('images/logo/avatar.png') }}"
                                alt="Previsualización" class="img-thumbnail mb-2" style="width: 200px; height: 200px" />
                        </div>
                        <input type="file" class="form-control persona" id="avatar" name="avatar" accept="image/*"
                            onchange="previewImage(event)">
                    </div>
                </div>

                <!-- Formulario Persona -->
                <div class="col-md-8">
                    <div class="row mt-2">
                        <div class="form-group col-md-6">
                            <label for="dni" class="form-label label_persona">DNI:</label>
                            <input type="text" class="form-control persona" id="dni" name="dni"
                                value="{{ old('dni', $persona->dni) }}" required pattern="^\d{8}$"
                                maxlength="8" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="nombres" class="form-label label_persona">Nombres:</label>
                            <input type="text" class="form-control persona" id="nombres" name="nombres"
                                value="{{ old('nombres', $persona->nombres) }}" required>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="form-group col-md-6">
                            <label for="apellido_p" class="form-label label_persona">Apellido Paterno:</label>
                            <input type="text" class="form-control persona" id="apellido_p" name="apellido_p"
                                value="{{ old('apellido_p', $persona->apellido_p) }}" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="apellido_m" class="form-label label_persona">Apellido Materno:</label>
                            <input type="text" class="form-control persona" id="apellido_m" name="apellido_m"
                                value="{{ old('apellido_m', $persona->apellido_m) }}" required>
                        </div>
                    </div>
                    <div class="form-group  mt-2">
                        <label for="direccion" class="form-label label_persona">Dirección:</label>
                        <input type="text" class="form-control persona" id="direccion" name="direccion"
                            value="{{ old('direccion', $persona->direccion) }}" required>
                    </div>
                    <div class="row mt-2">
                        <div class="form-group col-md-6">
                            <label for="celular" class="form-label label_persona">Celular:</label>
                            <input type="text" class="form-control persona" id="celular" name="celular"
                                value="{{ old('celular', $persona->celular) }}" required pattern="^\d{9}$" maxlength="9"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="f_nacimiento" class="form-label label_persona">Fecha de Nacimiento:</label>
                            <input type="date" class="form-control persona" id="f_nacimiento" name="f_nacimiento"
                                value="{{ old('f_nacimiento', $persona->f_nacimiento) }}" required
                                max="{{ \Carbon\Carbon::now()->subYears(15)->format('Y-m-d') }}"
                                min="{{ \Carbon\Carbon::now()->subYears(100)->format('Y-m-d') }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3 botones_form_persona text-center">
                <a href="{{ route('personas.index') }}" class="btn button_2 me-2"><i
                        class="fa fa-arrow-circle-left" aria-hidden="true"></i> Cancelar</a>
                <button type="submit" class="btn button_1 ms-2"><i class="fa fa-save" aria-hidden="true"></i>
                    Guardar Cambios</button>
            </div>
        </form>
    </div>

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
