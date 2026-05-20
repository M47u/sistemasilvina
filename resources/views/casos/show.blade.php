@extends('layouts.panel')
@section('title', 'Caso ' . $caso->nro_legajo)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-semibold">
        Caso <code>{{ $caso->nro_legajo }}</code>
        @if($caso->archivado)
            <span class="badge bg-warning text-dark ms-2">Archivado</span>
        @else
            <span class="badge bg-success ms-2">Activo</span>
        @endif
    </h4>
    <div class="d-flex gap-2">
        <a href="{{ route('casos.pdf.caso', $caso) }}" class="btn btn-outline-danger btn-sm" target="_blank">
            <i class="bi bi-file-earmark-pdf me-1"></i> Descargar PDF
        </a>
        <a href="{{ route('casos.edit', $caso) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil me-1"></i> Editar
        </a>
        <a href="{{ route('casos.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>
</div>

<div class="row g-3">

    {{-- Datos del expediente --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold py-3">
                <i class="bi bi-file-earmark-text me-1 text-primary"></i> Expediente
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5 text-muted">Fecha recepción</dt>
                    <dd class="col-sm-7">{{ $caso->fecha_recepcion->format('d/m/Y') }}</dd>

                    <dt class="col-sm-5 text-muted">Nro. Legajo</dt>
                    <dd class="col-sm-7"><code>{{ $caso->nro_legajo }}</code></dd>

                    <dt class="col-sm-5 text-muted">Nro. Expediente</dt>
                    <dd class="col-sm-7"><code>{{ $caso->nro_expediente }}</code></dd>

                    <dt class="col-sm-5 text-muted">Tipo</dt>
                    <dd class="col-sm-7">
                        <span class="badge bg-secondary">{{ $caso->tipoExpediente->nombre }}</span>
                    </dd>

                    <dt class="col-sm-5 text-muted">Fecha devolución</dt>
                    <dd class="col-sm-7">{{ $caso->fecha_devolucion?->format('d/m/Y') ?? '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- Datos personales --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold py-3">
                <i class="bi bi-person me-1 text-primary"></i> Persona
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5 text-muted">Apellido y Nombre</dt>
                    <dd class="col-sm-7 fw-semibold">{{ $caso->apellido_nombre }}</dd>

                    <dt class="col-sm-5 text-muted">DNI</dt>
                    <dd class="col-sm-7">{{ $caso->dni }}</dd>

                    <dt class="col-sm-5 text-muted">Localidad</dt>
                    <dd class="col-sm-7">{{ $caso->localidad->nombre }}</dd>

                    <dt class="col-sm-5 text-muted">Barrio</dt>
                    <dd class="col-sm-7">{{ $caso->barrio ?? '—' }}</dd>

                    <dt class="col-sm-5 text-muted">Teléfono</dt>
                    <dd class="col-sm-7">{{ $caso->telefono ?? '—' }}</dd>

                    <dt class="col-sm-5 text-muted">Denunciado</dt>
                    <dd class="col-sm-7">{{ $caso->denunciado ?? '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- Servicios --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold py-3">
                <i class="bi bi-people me-1 text-primary"></i> Servicios y atención
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-3">
                    <div>
                        <span class="text-muted small d-block">Acepta atención</span>
                        @if($caso->acepta_atencion)
                            <span class="badge bg-success">Sí</span>
                        @else
                            <span class="badge bg-secondary">No</span>
                        @endif
                    </div>
                    <div>
                        <span class="text-muted small d-block">Servicio Legal</span>
                        @if($caso->servicio_legal)
                            <span class="badge bg-info text-dark">Sí</span>
                        @else
                            <span class="badge bg-secondary">No</span>
                        @endif
                    </div>
                    <div>
                        <span class="text-muted small d-block">Serv. Psicológico</span>
                        @if($caso->servicio_psicologico)
                            <span class="badge" style="background:#6f42c1">Sí</span>
                        @else
                            <span class="badge bg-secondary">No</span>
                        @endif
                    </div>
                    <div>
                        <span class="text-muted small d-block">Servicio Social</span>
                        @if($caso->servicio_social)
                            <span class="badge" style="background:#20c997">Sí</span>
                        @else
                            <span class="badge bg-secondary">No</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Resumen / Observaciones --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold py-3">
                <i class="bi bi-chat-text me-1 text-primary"></i> Notas
            </div>
            <div class="card-body">
                @if($caso->resumen)
                    <p class="text-muted small mb-1">Resumen</p>
                    <p class="mb-3">{{ $caso->resumen }}</p>
                @endif
                @if($caso->observaciones)
                    <p class="text-muted small mb-1">Observaciones</p>
                    <p class="mb-0">{{ $caso->observaciones }}</p>
                @endif
                @if(!$caso->resumen && !$caso->observaciones)
                    <p class="text-muted mb-0">Sin notas.</p>
                @endif
            </div>
        </div>
    </div>

</div>

{{-- Eliminar --}}
<div class="mt-4 d-flex justify-content-end">
    <form method="POST" action="{{ route('casos.destroy', $caso) }}"
          onsubmit="return confirm('¿Eliminar este caso? Esta acción no se puede deshacer.')">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-trash me-1"></i> Eliminar caso
        </button>
    </form>
</div>
@endsection
