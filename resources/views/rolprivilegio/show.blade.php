<!-- @extends('layout.template')

@section('title', 'Ver Rol Privilegio')

@section('content')
<div class="container">
    <div>
        <strong>ID:</strong> {{ $rolprivilegio->id }}
    </div>
    <div>
        <strong>Privilegio:</strong> {{ $rolprivilegio->privilegio->nombre }}
    </div>
    <div>
        <strong>Rol:</strong> {{ $rolprivilegio->rol->nombre }}
    </div>
    <a href="{{ route('rolprivilegios.index') }}" class="btn btn-primary">Volver</a>
</div>
@endsection -->
