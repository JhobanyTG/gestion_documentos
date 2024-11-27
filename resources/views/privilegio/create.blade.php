@extends('layout/template')

@section('title', 'Crear Privilegio')

@section('content')
    <div class="container mt-4 form_privilegio">
        <form action="{{ route('privilegios.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre:</label>
                <input type="text" id="nombre" name="nombre" class="form-control privi" value="{{ old('nombre') }}" required>
                @error('nombre')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label ">Descripción:</label>
                <textarea id="descripcion" name="descripcion" class="form-control privi" rows="3" required>{{ old('descripcion') }}</textarea>
                @error('descripcion')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <a href="{{ route('privilegios.index') }}" class="btn mb-3 button_2"><i
                class="fa fa-arrow-circle-left" aria-hidden="true"></i> Cancelar</a>

            <button type="submit" class="btn mb-3 button_1"><i class="fa fa-plus" aria-hidden="true"></i>
                Crear</button>

        </form>
    </div>
@endsection
