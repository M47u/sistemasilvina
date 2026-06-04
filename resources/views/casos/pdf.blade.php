<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Casos</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #333; }
        h2 { font-size: 14px; margin-bottom: 4px; }
        p.sub { font-size: 9px; color: #666; margin-top: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #173a5e; color: #fff; padding: 5px 6px; font-size: 9px; text-align: left; }
        td { padding: 4px 6px; border-bottom: 1px solid #e5e7eb; }
        tr:nth-child(even) td { background-color: #f9fafb; }
        .badge { display: inline-block; padding: 1px 5px; border-radius: 3px; font-size: 8px; }
        .badge-activo { background: #d1fae5; color: #065f46; }
        .badge-archivado { background: #fef3c7; color: #92400e; }
    </style>
</head>
<body>
    <h2>Sistema Silvina — Listado de Casos</h2>
    <p class="sub">Generado el {{ now()->format('d/m/Y H:i') }} — Total: {{ $casos->count() }} casos</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Fecha</th>
                <th>Legajo</th>
                <th>Apellido y Nombre</th>
                <th>DNI</th>
                <th>Localidad</th>
                <th>Tipo</th>
                <th>Servicios</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($casos as $caso)
            <tr>
                <td>{{ $caso->id }}</td>
                <td>{{ $caso->fecha_recepcion->format('d/m/Y') }}</td>
                <td>{{ $caso->nro_legajo }}</td>
                <td>{{ $caso->apellido_nombre }}</td>
                <td>{{ $caso->dni }}</td>
                <td>{{ $caso->localidad->nombre }}</td>
                <td>{{ $caso->tipoExpediente->nombre }}</td>
                <td>
                    {{ $caso->servicio_legal ? 'Legal ' : '' }}
                    {{ $caso->servicio_psicologico ? 'Psic. ' : '' }}
                    {{ $caso->servicio_social ? 'Social' : '' }}
                </td>
                <td>
                    @if($caso->archivado)
                        <span class="badge badge-archivado">Archivado</span>
                    @else
                        <span class="badge badge-activo">Activo</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
