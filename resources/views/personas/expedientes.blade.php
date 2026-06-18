@extends('layouts.panel')
@section('title', 'Historial de ' . $persona->apellido_nombre)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-semibold">{{ $persona->apellido_nombre }}</h4>
        <small class="text-muted">
            Legajo <code>{{ $persona->nro_legajo ?? '—' }}</code>
            @if($persona->dni) &nbsp;·&nbsp; DNI {{ $persona->dni }} @endif
        </small>
    </div>
    <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 fw-semibold">
        <i class="bi bi-clock-history me-1 text-primary"></i>
        {{ $expedientes->count() }} {{ $expedientes->count() === 1 ? 'expediente' : 'expedientes' }} en este legajo
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Nro. Expediente</th>
                        <th>Tipo</th>
                        <th>Servicios</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expedientes as $exp)
                    <tr>
                        <td class="text-muted small">
                            @if($exp->fecha_recepcion)
                                {{ $exp->fecha_recepcion->format('d/m/Y') }}
                            @else
                                <span class="badge bg-warning text-dark">Sin fecha</span>
                            @endif
                        </td>
                        <td>
                            @if($exp->nro_expediente)
                                <code>{{ $exp->nro_expediente }}</code>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $exp->tipoExpediente->nombre }}</span>
                        </td>
                        <td>
                            @if($exp->servicio_legal)      <span class="badge bg-primary me-1">L</span> @endif
                            @if($exp->servicio_psicologico)<span class="badge bg-info me-1">P</span> @endif
                            @if($exp->servicio_social)     <span class="badge bg-success me-1">S</span> @endif
                        </td>
                        <td>
                            @if($exp->archivado)
                                <span class="badge bg-warning text-dark">Archivado</span>
                            @else
                                <span class="badge bg-success">Activo</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('expedientes.show', $exp) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Sin expedientes registrados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
