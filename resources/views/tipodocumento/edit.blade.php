@extends('layout.template')

@section('title', 'Editar Tipo de Documento')

@section('content')
<div class="container form_tipodocumento">
    <form action="{{ route('tipodocumento.update', $tipodocumento->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group mb-3">
            <label for="nombre" class="form-label label_tipo">Nombre</label>
            <input type="text" class="form-control tipo @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre', $tipodocumento->nombre) }}">
            @error('nombre')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="descripcion" class="form-label label_tipo">Descripción</label>
            <textarea class="form-control tipo @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion">{{ old('descripcion', $tipodocumento->descripcion) }}</textarea>
            @error('descripcion')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <a href="{{ route('tipodocumento.index') }}" class="btn button_2"><i
            class="fa fa-arrow-circle-left" aria-hidden="true"></i> Cancelar</a>
        <button type="submit" class="btn button_1"><i class="fa fa-save" aria-hidden="true"></i> Guardar Cambios</button>
    </form>
</div>
@endsection
