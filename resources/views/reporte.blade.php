<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Documentos</title>
    <style>
        body {
            font-family: system-ui;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
            background-color: #00C2EC
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 8px;
            text-align: center;
        }

        .badge {
            padding: 5px;
            border-radius: 5px;
        }

        .text-bg-danger {
            background-color: #dc3545;
            color: white;
        }

        .text-bg-success {
            background-color: #28a745;
            color: white;
        }

        .text-bg-primary {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>

<body>
    <h1>Reporte de Documentos</h1>
    <table id="example1" class="table mt-4 table-hover pt-serif-regular" role="grid" aria-describedby="example1_info">
        <thead>
            <tr>
                <th>N°</th>
                <th>Fecha</th>
                <th>Título</th>
                <th>Tipo Documento</th>
                <th>Descripción</th>
                <th>Gerencia</th>
                <th>SubGerencia</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($documentos as $documento)
                <tr>
                    <td>{{ $documento->id }}</td>
                    <td>{{ $documento->created_at->format('Y-m-d') }}</td>
                    <td>{{ $documento->titulo }}</td>
                    <td>{{ $documento->tipoDocumento->nombre }}</td>
                    <td>{{ Str::limit($documento->descripcion, 150) }}</td>
                    <td>{{ $documento->gerencia ? $documento->gerencia->nombre : 'N/A' }}</td>
                    <td>{{ $documento->subgerencia ? $documento->subgerencia->nombre : 'N/A' }}</td>
                    <td>
                        @if ($documento->estado === 'Creado')
                            <span class="badge text-bg-danger">Creado</span>
                        @elseif($documento->estado === 'Validado')
                            <span class="badge text-bg-success">Validado</span>
                        @elseif($documento->estado === 'Publicado')
                            <span class="badge text-bg-primary">Publicado</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
