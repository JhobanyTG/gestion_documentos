<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página no encontrada</title>
    <style>
        body {
            background: url('{{ asset('images/fondo/404.jpeg') }}') no-repeat center center fixed; /* Ruta a la imagen en la carpeta public */            background-size: cover;
            color: #343a40;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            text-align: center;
            padding: 20px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 3em;
            margin: 0.5em 0;
        }
        h2 {
            font-size: 2em;
            margin: 0.5em 0;
        }
        p {
            font-size: 1.2em;
            color: #6c757d;
        }
        .btn {
            display: inline-block;
            margin-top: 1em;
            padding: 10px 20px;
            font-size: 1em;
            color: #fff;
            background-color: #34495E;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #2D3E50;
        }
        .btn i {
            margin-right: 5px;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <h1>404</h1>
        <h2>Página no encontrada</h2>
        <p>La página que estás buscando no existe.</p>
        <a href="{{ url('login') }}" class="btn"><i class="fas fa-arrow-circle-left"></i> Regresar</a>
    </div>
</body>
</html>
