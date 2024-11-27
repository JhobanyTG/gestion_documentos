@extends('layout.template')

@section('title', 'Cambiar Contraseña')

@section('content')
    <div class="container form_password">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        <form method="POST" action="{{ route('change-password') }}">
            @csrf
            <div class="form-group mb-3 mt-2">
                <label for="current_password" class="form-label label_password">{{ __('Contraseña Actual:') }}</label>
                <input id="current_password" type="password" class="form-control input_change_pass password"
                    name="current_password" required autocomplete="current-password">
            </div>
            <div class="form-group mb-3">
                <label for="new_password" class="form-label label_password">{{ __('Nueva Contraseña:') }}</label>
                <input id="new_password" type="password" class="form-control input_change_pass password" name="new_password"
                    required autocomplete="new-password">
            </div>
            <div class="form-group mb-3">
                <label for="confirm_password"
                    class="form-label label_password">{{ __('Confirmar Nueva Contraseña:') }}</label>
                <input id="confirm_password" type="password" class="form-control input_change_pass password"
                    name="confirm_password" required autocomplete="new-password">
            </div>
            <div class="col-md-12 col-12 mt-4 d-flex align-items-center justify-content-center">
                <a href="{{ route('documentos.index') }}" class="btn button_2 me-2"><i
                        class="fa fa-arrow-circle-left" aria-hidden="true"></i> Cancelar</a>
                <button type="submit" class="btn button_1 ms-2">
                    <i class="fa fa-floppy-o" aria-hidden="true"></i> {{ __('Cambiar Contraseña') }}
                </button>
            </div>

        </form>
    </div>
@endsection
