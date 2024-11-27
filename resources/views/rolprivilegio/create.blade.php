<!-- @extends('layout.template')

@section('title', 'Crear Rol Privilegio')

@section('content')
<div class="container">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('rolprivilegios.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="privilegio_id">Privilegio</label>
            <select name="privilegio_id" id="privilegio_id" class="form-control">
                @foreach ($privilegios as $privilegio)
                    <option value="{{ $privilegio->id }}">{{ $privilegio->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="rol_id">Rol</label>
            <select name="rol_id" id="rol_id" class="form-control">
                @foreach ($roles as $rol)
                    <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>
@endsection -->
