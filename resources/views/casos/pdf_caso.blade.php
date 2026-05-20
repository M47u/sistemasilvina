<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Caso {{ $caso->nro_legajo }}</title>
    <style>
        @font-face {
            font-family: 'Calibri';
            src: url('C:/Windows/Fonts/calibri.ttf');
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: 'Calibri';
            src: url('C:/Windows/Fonts/calibrib.ttf');
            font-weight: bold;
            font-style: normal;
        }

        body {
            font-family: 'Calibri', DejaVu Sans, sans-serif;
            font-size: 12pt;
            color: #000;
            margin: 0;
            padding: 30px 50px 40px 50px;
            line-height: 1.5;
        }

        /* Membrete */
        .membrete {
            margin-bottom: 22px;
        }
        .membrete img {
            width: 100%;
            max-height: 60px;
        }
        .membrete-linea {
            border: none;
            border-top: 1px solid #aaa;
            margin-top: 10px;
        }

        /* Cuerpo */
        p {
            margin: 0 0 4px 0;
        }
        .titulo {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 14px;
            margin-bottom: 4px;
        }
        .contenido {
            text-align: justify;
            margin-bottom: 4px;
        }
    </style>
</head>
<body>

    {{-- Membrete --}}
    <div class="membrete">
        <img src="{{ public_path('img/membrete.png') }}" alt="Membrete">
        <hr class="membrete-linea">
    </div>

    <p><b>Fecha devolución:</b> {{ $caso->fecha_devolucion?->format('d/m/Y') ?? '—' }}</p>
    <p><b>Profesional:</b> Lic. Ortiz Jara Silvina B.</p>
    <p><b>Apellido y Nombre:</b> {{ $caso->apellido_nombre }}</p>
    <p><b>DNI:</b> {{ $caso->dni }}</p>
    <p><b>Acepta atención:</b> {{ $caso->acepta_atencion ? 'Sí' : 'No' }}</p>
    <p><b>Servicio Legal:</b> {{ $caso->servicio_legal ? 'Sí' : 'No' }}</p>
    <p><b>Serv. Psicológico:</b> {{ $caso->servicio_psicologico ? 'Sí' : 'No' }}</p>
    <p><b>Servicio Social:</b> {{ $caso->servicio_social ? 'Sí' : 'No' }}</p>

    @if($caso->resumen)
        <p class="titulo">Resumen</p>
        <p class="contenido">{{ $caso->resumen }}</p>
    @endif

    @if($caso->observaciones)
        <p class="titulo">Observaciones</p>
        <p class="contenido">{{ $caso->observaciones }}</p>
    @endif

    <p><b>Situación:</b> {{ $caso->archivado ? 'Para archivar' : 'Para intervenir' }}</p>

</body>
</html>
