@extends('layouts.panel')
@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-semibold">Dashboard</h4>
        <p class="text-muted small mb-0">
            <i class="bi bi-person-badge me-1"></i>
            {{ $profesional->apellido }}, {{ $profesional->nombre }} &mdash;
            <span class="fw-semibold">{{ ucfirst($profesional->area) }}</span>
        </p>
    </div>
    <a href="{{ route('mis-expedientes.index') }}" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-person-lines-fill me-1"></i> Ver todos mis expedientes
    </a>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width:56px;height:56px;background:#e9f7ef;">
                    <i class="bi bi-folder2-open fs-4 text-success"></i>
                </div>
                <div>
                    <div class="fs-2 fw-bold text-success">{{ $activos->count() }}</div>
                    <div class="text-muted small">Expedientes activos asignados</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width:56px;height:56px;background:#eaf4fb;">
                    <i class="bi bi-clock-history fs-4 text-primary"></i>
                </div>
                <div>
                    <div class="fs-2 fw-bold text-primary">{{ $totalHistorial }}</div>
                    <div class="text-muted small">Casos atendidos en total</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Expedientes activos --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-section py-3 d-flex justify-content-between align-items-center">
        <span><i class="bi bi-folder2-open me-2"></i>Mis expedientes activos</span>
        <span class="badge" style="background:rgba(255,255,255,.25); color:#fff; font-size:.8rem">
            {{ $activos->count() }}
        </span>
    </div>
    <div class="card-body p-0">
        @if($activos->isEmpty())
            <div class="py-5 text-center">
                <i class="bi bi-folder2 text-muted" style="font-size:2rem"></i>
                <p class="text-muted mt-2 mb-0">No tenés expedientes activos asignados.</p>
            </div>
        @else
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
        @endif
    </div>
</div>
@endsection
