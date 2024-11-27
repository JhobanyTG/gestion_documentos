<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>@yield('title')</title>
		<link rel="icon" href="{{ asset('images/logo/logo.png') }}">
		<meta name="description" content="">
		<meta name="viewport" content="width=device-width, initial-scale=1">

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
        <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
        <link rel="stylesheet" href="{{ asset('css/public.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </head>
    <body class="pt-serif-regular">
    <div class="header">
            <div class="company-info col-md-10 p-start">
                <div class="company-logo">
                    <img src="{{ asset('images/logo/logo.png') }}" alt="Logo">
                </div>
                <div class="company-nombre">
                    <h4>AIxion Systems</h4>
                    <p>Software</p>
                </div>
            </div>
            <div class="login-button col-md-2 p-end">
                <!-- <button><a href="https://iestpnunoa.edu.pe/">Página Principal</a></button> -->
                <a href="{{ route('login') }} " class="btn btn-public me-2"><i class="fa fa-sign-in" aria-hidden="true"></i>
                    Login</a>
            </div>
    </div>
        <div class="contentp">
            <div class="border-dark border-bottom mb-2 mt-2">
            </div>
            <main>
                <div>
                    @yield('content')
                </div>
            </main>
        </div>
    </body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://mozilla.github.io/pdf.js/build/pdf.js"></script>
<script src="{{ asset('js/public.js') }}"></script>
</html>
