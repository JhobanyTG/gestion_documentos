<!-- @extends('layout.template')

@section('title', 'Lista de Roles y Privilegios')

@section('content')
<div class="container">
    <h1>Lista de Rol Privilegios</h1>
    <a href="{{ route('rolprivilegios.create') }}" class="btn btn-primary">Crear Nuevo</a>
    <table class="table table-bordered mt-4">
        <thead>
            <tr>
                <th>ID</th>
                <th>Privilegio</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rolprivilegios as $rolprivilegio)
                <tr>
                    <td>{{ $rolprivilegio->id }}</td>
                    <td>{{ $rolprivilegio->privilegio->nombre }}</td>
                    <td>{{ $rolprivilegio->rol->nombre }}</td>
                    <td>
                        <a href="{{ route('rolprivilegios.show', $rolprivilegio->id) }}" class="btn btn-info">Ver</a>
                        <a href="{{ route('rolprivilegios.edit', $rolprivilegio->id) }}" class="btn btn-warning">Editar</a>
                        <form action="{{ route('rolprivilegios.destroy', $rolprivilegio->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection -->
