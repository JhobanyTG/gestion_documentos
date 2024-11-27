@extends('layout/template')

@section('title', 'Editar Rol')

@section('content')
    <div class="container mt-4 form_rol">
        <div class="row">
            <div class="col-md-5">
                <form action="{{ route('roles.update', $role->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="nombre" class="form-label label_rol">Nombre</label>
                        <input type="text" name="nombre" id="nombre" class="form-control rol"
                            value="{{ $role->nombre }}" required>
                    </div>
                    <div class="form-group">
                        <label for="descripcion" class="form-label label_rol">Descripción</label>
                        <textarea name="descripcion" id="descripcion" class="form-control rol" required>{{ $role->descripcion }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="available_privileges">Privilegios Disponibles</label>
                        <select id="available_privileges" class="form-control rol" multiple>
                            @foreach ($all_privilegios as $privilegio)
                                @if (!$role->privilegios->contains('id', $privilegio->id)) <!-- Filtra los privilegios ya asignados -->
                                    <option value="{{ $privilegio->id }}">{{ $privilegio->nombre }}</option>
                                @endif
                            @endforeach
                        </select>
                        <button type="button" class="btn mt-2 button_1" onclick="addPrivileges()"><i class="fa fa-arrow-down" aria-hidden="true"></i>Agregar
                            Privilegios</button>
                    </div>

                    <div class="form-group label_rol">
                        <label class="control-label">Privilegios Asignados</label>
                        <ul id="assigned_privileges" class="list-group rol">
                            @forelse($role->privilegios as $privilegio)
                                <li class="list-group-item rol">{{ $privilegio->nombre }}
                                    <button type="button" class="btn btn-danger btn-sm float-right"
                                        onclick="removePrivilege({{ $privilegio->id }})">Quitar</button>
                                </li>
                            @empty
                                <li class="list-group-item rol">Este rol no tiene privilegios asignados.</li>
                            @endforelse
                        </ul>
                    </div>

                    <input type="hidden" name="privilegios" id="privilegios"
                        value="{{ $role->privilegios->pluck('id')->implode(',') }}">
                    <a href="{{ route('roles.index') }}" class="btn mt-3 button_2"><i
                        class="fa fa-arrow-circle-left" aria-hidden="true"></i> Cancelar</a>
                    <button type="submit" class="btn mt-3 button_1"><i class="fa fa-save" aria-hidden="true"></i> Guardar Cambios</button>
                </form>
            </div>

            <div class="col-md-7">
                <h2 class="form_title_rol2">Lista de Privilegios</h2>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($all_privilegios as $privilegio)
                            <tr>
                                <td class="text-center">{{ $privilegio->id }}</td>
                                <td class="text-center">{{ $privilegio->nombre }}</td>
                                <td class="text-center">{{ Str::limit($privilegio->descripcion, 50) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            updatePrivilegesInput(); // Asegúrate de que el campo oculto se actualice al cargar la página
        });

        function addPrivileges() {
            let availablePrivileges = document.getElementById('available_privileges');
            let assignedPrivileges = document.getElementById('assigned_privileges');

            Array.from(availablePrivileges.selectedOptions).forEach(option => {
                let privilegeId = option.value;
                let privilegeName = option.text;

                // Add to assigned list
                let listItem = document.createElement('li');
                listItem.className = 'list-group-item';
                listItem.innerHTML =
                    `${privilegeName} <button type="button" class="btn button_3 btn-sm float-right" onclick="removePrivilege(${privilegeId})">Quitar</button>`;
                assignedPrivileges.appendChild(listItem);

                // Remove from available list
                option.remove();
            });

            // Update hidden input
            updatePrivilegesInput();
        }

        function removePrivilege(id) {
            let assignedPrivileges = document.getElementById('assigned_privileges');
            let availablePrivileges = document.getElementById('available_privileges');
            let privilegeName = null;

            Array.from(assignedPrivileges.children).forEach(listItem => {
                if (listItem.innerHTML.includes(`onclick="removePrivilege(${id})"`)) {
                    privilegeName = listItem.innerText.replace('Quitar', '').trim();
                    listItem.remove();
                }
            });

            // Add to available list
            if (privilegeName) {
                let option = document.createElement('option');
                option.value = id;
                option.text = privilegeName;
                availablePrivileges.appendChild(option);
            }

            // Update hidden input
            updatePrivilegesInput();
        }

        function updatePrivilegesInput() {
            let assignedPrivileges = document.getElementById('assigned_privileges');
            let privilegeIds = Array.from(assignedPrivileges.children)
                .filter(listItem => listItem.innerHTML.includes('onclick="removePrivilege'))
                .map(listItem => listItem.innerHTML.match(/onclick="removePrivilege\((\d+)\)/)[1]);

            document.getElementById('privilegios').value = privilegeIds.join(',');

            // Handle empty state
            if (privilegeIds.length === 0) {
                document.getElementById('assigned_privileges').innerHTML =
                    '<li class="list-group-item">Este rol no tiene privilegios asignados.</li>';
            }
        }
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
@endsection
