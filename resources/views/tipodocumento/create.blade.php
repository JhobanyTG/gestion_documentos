@extends('layout.template')

@section('title', 'Crear Tipo de Documento')

@section('content')
<div class="container form_tipodocumento">
    <form action="{{ route('tipodocumento.store') }}" method="POST">
        @csrf
        <div class="form-group mb-3">
            <label for="nombre" class="form-label label_tipo">Nombre</label>
            <input type="text" class="form-control tipo @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre') }}" class="form-control tipo">
            @error('nombre')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="descripcion" class="form-label label_tipo">Descripción</label>
            <textarea class="form-control tipo @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" class="form-control tipo">{{ old('descripcion') }}</textarea>
            @error('descripcion')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <a href="{{ route('tipodocumento.index') }}" class="btn button_2"><i
            class="fa fa-arrow-circle-left" aria-hidden="true"></i> Cancelar</a>
        <button type="submit" class="btn button_1"><i class="fa fa-plus" aria-hidden="true"></i>
            Crear</button>
    </form>
</div>
@endsection
