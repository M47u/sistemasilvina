@extends('layouts.panel')
@section('title', 'Casos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0 fw-semibold">Casos</h4>
    <a href="{{ route('casos.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i> Nuevo caso
    </a>
</div>

{{-- Filtros --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" action="{{ route('casos.index') }}" class="row g-2 align-items-end">
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
                <select name="archivado" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="0" {{ request('archivado') === '0' ? 'selected' : '' }}>Activos</option>
                    <option value="1" {{ request('archivado') === '1' ? 'selected' : '' }}>Archivados</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                    <i class="bi bi-search"></i> Buscar
                </button>
                <a href="{{ route('casos.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Exportaciones --}}
<div class="d-flex gap-2 mb-3">
    <a href="{{ route('casos.export.excel') }}" class="btn btn-success btn-sm">
        <i class="bi bi-file-earmark-excel me-1"></i> Exportar Excel
    </a>
    <a href="{{ route('casos.export.pdf') }}" class="btn btn-danger btn-sm">
        <i class="bi bi-file-earmark-pdf me-1"></i> Exportar PDF
    </a>
    <span class="text-muted small align-self-center ms-2">
        {{ $casos->total() }} resultado(s)
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
                    @forelse($casos as $caso)
                    <tr>
                        <td class="text-muted small">{{ $caso->fecha_recepcion->format('d/m/Y') }}</td>
                        <td><code>{{ $caso->nro_legajo }}</code></td>
                        <td>{{ $caso->apellido_nombre }}</td>
                        <td class="text-muted">{{ $caso->dni }}</td>
                        <td>{{ $caso->localidad->nombre }}</td>
                        <td><span class="badge bg-secondary badge-servicio">{{ $caso->tipoExpediente->nombre }}</span></td>
                        <td>
                            @if($caso->servicio_legal)
                                <span class="badge bg-info text-dark badge-servicio" title="Legal">L</span>
                            @endif
                            @if($caso->servicio_psicologico)
                                <span class="badge bg-purple badge-servicio" style="background:#6f42c1" title="Psicológico">P</span>
                            @endif
                            @if($caso->servicio_social)
                                <span class="badge bg-teal badge-servicio" style="background:#20c997" title="Social">S</span>
                            @endif
                        </td>
                        <td>
                            @if($caso->archivado)
                                <span class="badge bg-warning text-dark">Archivado</span>
                            @else
                                <span class="badge bg-success">Activo</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('casos.show', $caso) }}" class="btn btn-sm btn-outline-primary" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('casos.edit', $caso) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{ route('casos.destroy', $caso) }}" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar este caso?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">No se encontraron casos.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Paginación --}}
@if($casos->hasPages())
<div class="mt-3 d-flex justify-content-center">
    {{ $casos->links('pagination::bootstrap-5') }}
</div>
@endif

@endsection
