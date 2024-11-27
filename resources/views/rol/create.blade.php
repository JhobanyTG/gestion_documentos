@extends('layout/template')

@section('title', 'Crear Rol')

@section('content')
    <div class="container mt-4 form_rol">
        <div class="row">
            <!-- Formulario -->
            <div class="col-md-5">
                <form action="{{ route('roles.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="nombre" class="form-label label_rol">Nombre</label>
                        <input type="text" name="nombre" id="nombre" class="form-control rol" required>
                    </div>
                    <div class="form-group">
                        <label for="descripcion" class="form-label label_rol ">Descripción</label>
                        <textarea name="descripcion" id="descripcion" class="form-control rol" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="available_privileges">Privilegios Disponibles</label>
                        <select id="available_privileges" class="form-control rol" multiple>
                            @foreach ($all_privilegios as $privilegio)
                                <option value="{{ $privilegio->id }}">{{ $privilegio->nombre }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn mt-2 button_1" onclick="addPrivileges()"><i class="fa fa-arrow-down" aria-hidden="true"></i>Agregar
                            Privilegios</button>
                    </div>

                    <div class="form-group label_rol">
                        <label class="control-label">Privilegios Asignados</label>
                        <ul id="assigned_privileges" class="list-group rol">
                            <li class="list-group-item rol">No se han asignado privilegios aún.</li>
                        </ul>
                    </div>

                    <!-- Campo oculto para los IDs de los privilegios -->
                    <input type="hidden" name="privilegios" id="privilegios" value="">
                    <a href="{{ route('roles.index') }}" class="btn mt-3 button_2"><i
                        class="fa fa-arrow-circle-left" aria-hidden="true"></i> Cancelar</a>
                    <button type="submit" class="btn mt-3 button_1"><i class="fa fa-plus" aria-hidden="true"></i>
                        Crear</button>
                </form>
            </div>

            <!-- Columna de Privilegios -->
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

            // Remove "No se han asignado privilegios aún" message if it exists
            let noPrivilegesMessage = assignedPrivileges.querySelector('li.list-group-item.rol:not([data-privilege-id])');
            if (noPrivilegesMessage) {
                noPrivilegesMessage.remove();
            }

            Array.from(availablePrivileges.selectedOptions).forEach(option => {
                let privilegeId = option.value;
                let privilegeName = option.text;

                // Add to assigned list
                let listItem = document.createElement('li');
                listItem.className = 'list-group-item rol';
                listItem.setAttribute('data-privilege-id', privilegeId);
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

            let listItem = assignedPrivileges.querySelector(`li[data-privilege-id="${id}"]`);
            if (listItem) {
                privilegeName = listItem.innerText.replace('Quitar', '').trim();
                listItem.remove();
            }

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
            let privilegeItems = assignedPrivileges.querySelectorAll('li[data-privilege-id]');

            let privilegeIds = Array.from(privilegeItems).map(item => item.getAttribute('data-privilege-id'));

            document.getElementById('privilegios').value = privilegeIds.join(',');

            // Handle empty state
            if (privilegeIds.length === 0) {
                if (!assignedPrivileges.querySelector('li:not([data-privilege-id])')) {
                    assignedPrivileges.innerHTML =
                        '<li class="list-group-item rol">No se han asignado privilegios aún.</li>';
                }
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
