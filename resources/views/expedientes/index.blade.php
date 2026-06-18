@extends('layouts.panel')
@section('title', 'Expedientes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0 fw-semibold">Expedientes</h4>
    <a href="{{ route('expedientes.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i> Nuevo expediente
    </a>
</div>

{{-- Filtros --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" action="{{ route('expedientes.index') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" name="buscar" class="form-control form-control-sm"
                       placeholder="Nombre, DNI, legajo o expediente..."
                       value="{{ request('buscar') }}">
            </div>
            <div class="col-md-2">
                <select name="tipo_expediente_id" class="form-select form-select-sm">
                    <option value="">Todos los tipos</option>
                    @foreach($tipos as $tipo)
                        <option value="{{ $tipo->id }}" {{ request('tipo_expediente_id') == $tipo->id ? 'selected' : '' }}>
                            {{ $tipo->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="localidad_id" class="form-select form-select-sm">
                    <option value="">Todas las localidades</option>
                    @foreach($localidades as $loc)
                        <option value="{{ $loc->id }}" {{ request('localidad_id') == $loc->id ? 'selected' : '' }}>
                            {{ $loc->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="estado" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="activo"    {{ request('estado') === 'activo'    ? 'selected' : '' }}>Activos</option>
                    <option value="derivado"  {{ request('estado') === 'derivado'  ? 'selected' : '' }}>Derivados</option>
                    <option value="archivado" {{ request('estado') === 'archivado' ? 'selected' : '' }}>Archivados</option>
                </select>
            </div>
            <div class="col-md-1">
                <select name="sin_fecha" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    <option value="1" {{ request('sin_fecha') === '1' ? 'selected' : '' }}>Sin fecha</option>
                </select>
            </div>
            <div class="col-md-1 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                    <i class="bi bi-search"></i>
                </button>
                <a href="{{ route('expedientes.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Exportaciones --}}
<div class="d-flex gap-2 mb-3">
    <a href="{{ route('expedientes.export.excel') }}" class="btn btn-success btn-sm">
        <i class="bi bi-file-earmark-excel me-1"></i> Exportar Excel
    </a>
    <a href="{{ route('expedientes.export.pdf') }}" class="btn btn-danger btn-sm">
        <i class="bi bi-file-earmark-pdf me-1"></i> Exportar PDF
    </a>
    @if($totalErrores > 0)
    <a href="{{ route('expedientes.index', ['con_errores' => 1]) }}"
       class="btn btn-sm {{ request('con_errores') ? 'btn-warning' : 'btn-outline-warning' }}">
        <i class="bi bi-exclamation-triangle me-1"></i> Ver {{ $totalErrores }} {{ $totalErrores == 1 ? 'expediente' : 'expedientes' }} con posibles errores de fecha
    </a>
    @endif
    @if(request('con_errores'))
    <a href="{{ route('expedientes.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-x-lg me-1"></i> Quitar filtro
    </a>
    @endif
    <span class="text-muted small align-self-center ms-2">
        {{ $expedientes->total() }} resultado(s)
    </span>
</div>

{{-- Tabla --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Legajo</th>
                        <th>Nro. Expediente</th>
                        <th>Apellido y Nombre</th>
                        <th>DNI</th>
                        <th>Localidad</th>
                        <th>Tipo</th>
                        <th>Servicios</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expedientes as $expediente)
                    <tr>
                        <td class="text-muted small">
                            @if($expediente->fecha_recepcion)
                                {{ $expediente->fecha_recepcion->format('d/m/Y') }}
                                @if($expediente->fecha_recepcion->year < 100 || $expediente->fecha_recepcion->isAfter(today()))
                                    <span class="badge bg-danger ms-1" title="Posible error en la fecha — requiere revisión">
                                        <i class="bi bi-exclamation-triangle"></i>
                                    </span>
                                @endif
                            @else
                                <span class="badge bg-warning text-dark" title="Fecha pendiente de asignación">
                                    <i class="bi bi-calendar-x me-1"></i>Sin fecha
                                </span>
                            @endif
                            @if($expediente->urgente)
                                <span class="badge bg-danger ms-1">Urgente</span>
                            @endif
                        </td>
                        <td>
                            <code>{{ $expediente->nro_legajo ?? '—' }}</code>
                            @php
                                $anioLegajo = null;
                                if ($expediente->nro_legajo && preg_match('#/(\d{2})$#', $expediente->nro_legajo, $m)) {
                                    $anioLegajo = (int) $m[1];
                                }
                            @endphp
                            @if($anioLegajo !== null && $anioLegajo > now()->format('y'))
                                <span class="badge bg-danger ms-1" title="El año del legajo (/{{ $m[1] }}) es posterior al año actual — posible error">
                                    <i class="bi bi-exclamation-triangle"></i>
                                </span>
                            @endif
                        </td>
                        <td><code>{{ $expediente->nro_expediente ?? '—' }}</code></td>
                        <td>{{ $expediente->persona?->apellido_nombre ?? $expediente->apellido_nombre }}</td>
                        <td class="text-muted">{{ $expediente->persona?->dni ?? $expediente->dni }}</td>
                        <td>{{ $expediente->persona?->localidad?->nombre ?? '—' }}</td>
                        <td><span class="badge bg-secondary badge-servicio">{{ $expediente->tipoExpediente->nombre }}</span></td>
                        <td>
                            @if($expediente->servicio_legal)
                                <span class="badge bg-info text-dark badge-servicio" title="Legal">L</span>
                            @endif
                            @if($expediente->servicio_psicologico)
                                <span class="badge badge-servicio" style="background:#6f42c1;color:#fff" title="Psicológico">P</span>
                            @endif
                            @if($expediente->servicio_social)
                                <span class="badge badge-servicio" style="background:#20c997;color:#fff" title="Social">S</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $eb = match($expediente->estado ?? 'activo') {
                                    'archivado' => ['label' => 'Archivado', 'class' => 'bg-secondary'],
                                    'derivado'  => ['label' => 'Derivado',  'class' => 'bg-warning text-dark'],
                                    default     => ['label' => 'Activo',    'class' => 'bg-success'],
                                };
                            @endphp
                            <span class="badge {{ $eb['class'] }}">{{ $eb['label'] }}</span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('expedientes.pdf.expediente', $expediente) }}" class="btn btn-sm btn-outline-secondary" title="Imprimir PDF" target="_blank">
                                <i class="bi bi-printer"></i>
                            </a>
                            <a href="{{ route('expedientes.show', $expediente) }}" class="btn btn-sm btn-outline-primary" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('expedientes.edit', $expediente) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{ route('expedientes.destroy', $expediente) }}" class="d-inline"
                                  data-confirm="¿Eliminar este expediente? Esta acción no se puede deshacer.">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">No se encontraron expedientes.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Paginación --}}
@if($expedientes->hasPages())
<div class="mt-3 d-flex justify-content-center">
    {{ $expedientes->links('pagination::bootstrap-5') }}
</div>
@endif

@endsection
