@extends('layouts.panel')
@section('title', 'Caso ' . $caso->nro_legajo)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-semibold">
        Caso <code>{{ $caso->nro_legajo }}</code>
        @if($caso->urgente)
            <span class="badge bg-danger ms-2">Urgente</span>
        @endif
        @if($caso->archivado)
            <span class="badge bg-warning text-dark ms-1">Archivado</span>
        @else
            <span class="badge bg-success ms-1">Activo</span>
        @endif
    </h4>
    <div class="d-flex gap-2">
        <a href="{{ route('casos.pdf.caso', $caso) }}" class="btn btn-outline-danger btn-sm" target="_blank">
            <i class="bi bi-file-earmark-pdf me-1"></i> PDF
        </a>
        <a href="{{ route('casos.edit', $caso) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil me-1"></i> Editar
        </a>
        <a href="{{ route('casos.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-3">

    {{-- Expediente --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold py-3">
                <i class="bi bi-file-earmark-text me-1 text-primary"></i> Expediente
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5 text-muted">Fecha recepción</dt>
                    <dd class="col-sm-7">
                        @if($caso->fecha_recepcion)
                            {{ $caso->fecha_recepcion->format('d/m/Y') }}
                        @else
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-calendar-x me-1"></i>Pendiente asignación
                            </span>
                        @endif
                    </dd>

                    <dt class="col-sm-5 text-muted">Nro. Legajo</dt>
                    <dd class="col-sm-7"><code>{{ $caso->nro_legajo }}</code></dd>

                    <dt class="col-sm-5 text-muted">Nro. Expediente</dt>
                    <dd class="col-sm-7">{{ $caso->nro_expediente ? '<code>'.$caso->nro_expediente.'</code>' : '—' }}</dd>

                    <dt class="col-sm-5 text-muted">Tipo</dt>
                    <dd class="col-sm-7">
                        <span class="badge bg-secondary">{{ $caso->tipoExpediente->nombre }}</span>
                    </dd>

                    <dt class="col-sm-5 text-muted">Vía de acceso</dt>
                    <dd class="col-sm-7">{{ $caso->via_acceso ?? '—' }}</dd>

                    <dt class="col-sm-5 text-muted">Fecha devolución</dt>
                    <dd class="col-sm-7">{{ $caso->fecha_devolucion?->format('d/m/Y') ?? '—' }}</dd>

                    @if($caso->creadoPor)
                    <dt class="col-sm-5 text-muted">Registrado por</dt>
                    <dd class="col-sm-7">{{ $caso->creadoPor->name }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    {{-- Persona --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold py-3 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-person me-1 text-primary"></i> Persona</span>
                @if($caso->persona && $caso->persona->casos()->count() > 1)
                    <span class="badge bg-primary">
                        <i class="bi bi-link-45deg me-1"></i>
                        {{ $caso->persona->casos()->count() }} casos vinculados
                    </span>
                @endif
            </div>
            <div class="card-body">
                @php $p = $caso->persona; @endphp
                <dl class="row mb-0">
                    <dt class="col-sm-5 text-muted">Apellido y Nombre</dt>
                    <dd class="col-sm-7 fw-semibold">{{ $p?->apellido_nombre ?? $caso->apellido_nombre }}</dd>

                    <dt class="col-sm-5 text-muted">DNI</dt>
                    <dd class="col-sm-7">{{ $p?->dni ?? $caso->dni ?? '—' }}</dd>

                    <dt class="col-sm-5 text-muted">Localidad</dt>
                    <dd class="col-sm-7">{{ $p?->localidad?->nombre ?? $caso->localidad?->nombre ?? '—' }}</dd>

                    <dt class="col-sm-5 text-muted">Barrio</dt>
                    <dd class="col-sm-7">{{ $p?->barrio ?? $caso->barrio ?? '—' }}</dd>

                    <dt class="col-sm-5 text-muted">Teléfono</dt>
                    <dd class="col-sm-7">{{ $p?->telefono ?? $caso->telefono ?? '—' }}</dd>

                    <dt class="col-sm-5 text-muted">Dirección</dt>
                    <dd class="col-sm-7">{{ $p?->direccion ?? '—' }}</dd>

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
                <i class="bi bi-people me-1 text-primary"></i> Servicios requeridos
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-3">
                    @foreach([
                        ['acepta_atencion', 'Acepta atención', 'success'],
                        ['servicio_legal', 'Legal', 'info'],
                        ['servicio_psicologico', 'Psicológico', 'purple'],
                        ['servicio_social', 'Social', 'teal'],
                    ] as [$campo, $label, $color])
                    <div>
                        <span class="text-muted small d-block">{{ $label }}</span>
                        @if($caso->$campo)
                            <span class="badge bg-{{ $color }}" style="{{ in_array($color,['purple','teal']) ? 'background:'.($color=='purple'?'#6f42c1':'#20c997').'!important' : '' }}">Sí</span>
                        @else
                            <span class="badge bg-secondary">No</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Notas --}}
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

    {{-- Profesionales asignados --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold py-3">
                <i class="bi bi-person-badge me-1 text-primary"></i> Profesionales
            </div>
            <div class="card-body">

                {{-- Historial --}}
                @if($caso->profesionales->count())
                <div class="table-responsive mb-4">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Profesional</th>
                                <th>Área</th>
                                <th>Desde</th>
                                <th>Hasta</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($caso->profesionales->sortByDesc('pivot.fecha_asignacion') as $prof)
                            <tr>
                                <td>{{ $prof->apellido }}, {{ $prof->nombre }}</td>
                                <td><span class="badge bg-secondary">{{ ucfirst($prof->pivot->area) }}</span></td>
                                <td>{{ \Carbon\Carbon::parse($prof->pivot->fecha_asignacion)->format('d/m/Y') }}</td>
                                <td>
                                    @if($prof->pivot->fecha_fin)
                                        {{ \Carbon\Carbon::parse($prof->pivot->fecha_fin)->format('d/m/Y') }}
                                    @else
                                        <span class="badge bg-success">Activo</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                    <p class="text-muted small mb-3">Sin profesionales asignados aún.</p>
                @endif

                {{-- Asignar nuevo --}}
                <form method="POST" action="{{ route('casos.profesionales.asignar', $caso) }}" class="row g-2 align-items-end">
                    @csrf
                    <div class="col-md-5">
                        <label class="form-label form-label-sm">Profesional</label>
                        <select name="profesional_id" class="form-select form-select-sm" required>
                            <option value="">— Seleccionar —</option>
                            @foreach($profesionales as $p)
                                <option value="{{ $p->id }}">{{ $p->apellido }}, {{ $p->nombre }} ({{ ucfirst($p->area) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label form-label-sm">Área de asignación</label>
                        <select name="area" class="form-select form-select-sm" required>
                            <option value="">— Área —</option>
                            <option value="legal">Legal</option>
                            <option value="psicologia">Psicología</option>
                            <option value="social">Social</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-plus-lg me-1"></i> Asignar
                        </button>
                    </div>
                </form>

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
