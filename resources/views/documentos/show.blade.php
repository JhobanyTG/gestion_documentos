@extends('layout/template')

@section('title', 'Ver Documento')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>{{ $documento->titulo }}</h1>
            <p><strong>Tipo Documento:</strong> {{ $documento->tipoDocumento->nombre }}</p>
            <p><strong>Descripción:</strong> {{ $documento->descripcion }}</p>
            <p><strong>Archivo:</strong> <a href="{{ Storage::url($documento->archivo) }}" target="_blank">Ver Archivo</a></p>
            <p><strong>Subusuario:</strong> {{ $documento->subusuario->nombre }}</p>
            <p><strong>Estado:</strong> {{ $documento->estado }}</p>
            <a href="{{ route('documentos.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </div>
</div>
@endsection
