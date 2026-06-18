<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Expediente {{ $expediente->nro_legajo }}</title>
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

        .membrete { margin-bottom: 22px; }
        .membrete img { width: 100%; max-height: 60px; }
        .membrete-linea { border: none; border-top: 1px solid #aaa; margin-top: 10px; }

        p { margin: 0 0 4px 0; }
        .titulo { font-weight: bold; text-decoration: underline; margin-top: 14px; margin-bottom: 4px; }
        .contenido { text-align: justify; margin-bottom: 4px; }
    </style>
</head>
<body>

    {{-- Membrete --}}
    <div class="membrete">
        @if($membrete)
            <img src="{{ $membrete }}" alt="Membrete">
        @endif
        <hr class="membrete-linea">
    </div>

    <p><b>Fecha devolución:</b> {{ $expediente->fecha_devolucion?->format('d/m/Y') ?? '—' }}</p>
    @if($expediente->profesionalesActivos->isNotEmpty())
        <p><b>Profesional:</b> {{ $expediente->profesionalesActivos->map(fn($p) => $p->apellido.', '.$p->nombre)->join(' / ') }}</p>
    @endif
    <p><b>Apellido y Nombre:</b> {{ $expediente->persona?->apellido_nombre ?? $expediente->apellido_nombre }}</p>
    <p><b>DNI:</b> {{ $expediente->persona?->dni ?? $expediente->dni ?? '—' }}</p>
    <p><b>Acepta atención:</b> {{ $expediente->acepta_atencion ? 'Sí' : 'No' }}</p>
    <p><b>Servicio Legal:</b> {{ $expediente->servicio_legal ? 'Sí' : 'No' }}</p>
    <p><b>Serv. Psicológico:</b> {{ $expediente->servicio_psicologico ? 'Sí' : 'No' }}</p>
    <p><b>Servicio Social:</b> {{ $expediente->servicio_social ? 'Sí' : 'No' }}</p>

    @if($expediente->resumen)
        <p class="titulo">Resumen</p>
        <p class="contenido">{{ $expediente->resumen }}</p>
    @endif

    @if($expediente->observaciones)
        <p class="titulo">Observaciones</p>
        <p class="contenido">{{ $expediente->observaciones }}</p>
    @endif

    <p><b>Situación:</b> {{ $expediente->archivado ? 'Para archivar' : 'Para intervenir' }}</p>

</body>
</html>
