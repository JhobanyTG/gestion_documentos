

    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <title>Historial de Cambios</title>
        <style>
            /* Estilos básicos para el PDF */
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
        <h1>Historial de Cambios</h1>
        <div>
            <table id="example1" class="table mt-4 table-hover pt-serif-regular" role="grid"
                aria-describedby="example1_info">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Documento</th>
                        <th>Estado Anterior</th>
                        <th>Estado Nuevo</th>
                        <th>Descripción</th>
                        <th>Usuario</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($historial as $cambio)
                        <tr>
                            <td>{{ $cambio->id }}</td>
                            <td>{{ $cambio->documento->titulo ?? 'Sin título' }}</td>
                            <td>
                                @if ($cambio->estado_anterior === 'Creado')
                                    <span class="badge text-bg-danger">Creado</span>
                                @elseif($cambio->estado_anterior === 'Validado')
                                    <span class="badge text-bg-success">Validado</span>
                                @elseif($cambio->estado_anterior === 'Publicado')
                                    <span class="badge text-bg-primary">Publicado</span>
                                @endif
                            </td>
                            <td>
                                @if ($cambio->estado_nuevo === 'Creado')
                                    <span class="badge text-bg-danger">Creado</span>
                                @elseif($cambio->estado_nuevo === 'Validado')
                                    <span class="badge text-bg-success">Validado</span>
                                @elseif($cambio->estado_nuevo === 'Publicado')
                                    <span class="badge text-bg-primary">Publicado</span>
                                @endif
                            </td>
                            <td>{{ $cambio->descripcion }}</td>
                            <td>{{ $cambio->user->nombre_usuario }}</td>
                            <td>{{ $cambio->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </body>

    </html>
