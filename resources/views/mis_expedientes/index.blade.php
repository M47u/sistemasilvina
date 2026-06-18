@extends('layouts.panel')
@section('title', 'Mis Expedientes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-semibold">Mis Expedientes</h4>
        <p class="text-muted small mb-0">
            <i class="bi bi-person-badge me-1"></i>
            {{ $profesional->apellido }}, {{ $profesional->nombre }} —
            <span class="fw-semibold">{{ ucfirst($profesional->area) }}</span>
        </p>
    </div>
</div>

{{-- Activos --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-section py-3 d-flex justify-content-between align-items-center">
        <span><i class="bi bi-folder2-open me-2"></i>Expedientes activos</span>
        <span class="badge" style="background:rgba(255,255,255,.25); color:#fff; font-size:.8rem">
            {{ $activos->count() }}
        </span>
    </div>
    <div class="card-body p-0">
        @if($activos->isNotEmpty())
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Legajo</th>
                        <th>Apellido y Nombre</th>
                        <th>Localidad</th>
                        <th>Tipo</th>
                        <th>Área</th>
                        <th>Asignado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activos as $exp)
                    <tr>
                        <td><code class="text-primary fw-semibold">{{ $exp->nro_legajo ?? '—' }}</code></td>
                        <td class="fw-semibold" style="color:var(--sm-navy)">
                            {{ $exp->persona?->apellido_nombre ?? $exp->apellido_nombre }}
                        </td>
                        <td>{{ $exp->persona?->localidad?->nombre ?? '—' }}</td>
                        <td><span class="badge bg-secondary">{{ $exp->tipoExpediente->nombre }}</span></td>
                        <td><span class="badge bg-primary">{{ ucfirst($exp->pivot->area) }}</span></td>
                        <td style="color:var(--sm-navy); font-size:.85rem">
                            {{ \Carbon\Carbon::parse($exp->pivot->fecha_asignacion)->format('d/m/Y H:i') }}
                        </td>
                        <td class="text-end">
                            <a href="{{ route('expedientes.show', $exp) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-eye me-1"></i> Ver
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
            <div class="py-5 text-center">
                <i class="bi bi-folder2 text-muted" style="font-size:2rem"></i>
                <p class="text-muted mt-2 mb-0">No tenés expedientes activos asignados.</p>
            </div>
        @endif
    </div>
</div>

{{-- Historial --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-section py-3 d-flex justify-content-between align-items-center"
         style="background: linear-gradient(135deg, #2a547e 0%, var(--sm-blue) 100%) !important">
        <span><i class="bi bi-clock-history me-2"></i>Historial de asignaciones</span>
        <span class="badge" style="background:rgba(255,255,255,.25); color:#fff; font-size:.8rem">
            {{ $historial->count() }}
        </span>
    </div>
    <div class="card-body p-0">
        @if($historial->isNotEmpty())
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Legajo</th>
                        <th>Apellido y Nombre</th>
                        <th>Área</th>
                        <th>Asignado</th>
                        <th>Cerrado</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($historial as $exp)
                    <tr>
                        <td><code class="text-primary fw-semibold">{{ $exp->nro_legajo ?? '—' }}</code></td>
                        <td style="color:var(--sm-navy)">{{ $exp->persona?->apellido_nombre ?? $exp->apellido_nombre }}</td>
                        <td><span class="badge bg-primary">{{ ucfirst($exp->pivot->area) }}</span></td>
                        <td style="font-size:.85rem; color:var(--sm-navy)">
                            {{ \Carbon\Carbon::parse($exp->pivot->fecha_asignacion)->format('d/m/Y H:i') }}
                        </td>
                        <td style="font-size:.85rem; color:#6b7280">
                            {{ $exp->pivot->fecha_fin ? \Carbon\Carbon::parse($exp->pivot->fecha_fin)->format('d/m/Y H:i') : '—' }}
                        </td>
                        <td>
                            @if($exp->pivot->fecha_fin)
                                <span class="badge bg-secondary">Cerrado</span>
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
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
            <div class="py-5 text-center">
                <i class="bi bi-clock text-muted" style="font-size:2rem"></i>
                <p class="text-muted mt-2 mb-0">Sin historial de asignaciones.</p>
            </div>
        @endif
    </div>
</div>

@endsection
