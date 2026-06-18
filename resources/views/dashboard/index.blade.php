@extends('layouts.panel')
@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-semibold">Dashboard</h4>
    @hasanyrole('Coordinadora|Administrativo')
    <a href="{{ route('expedientes.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i> Nuevo caso
    </a>
    @endhasanyrole
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width:56px;height:56px;background:#eaf4fb;">
                    <i class="bi bi-folder2-open fs-4 text-primary"></i>
                </div>
                <div>
                    <div class="fs-2 fw-bold text-primary">{{ $totalCasos }}</div>
                    <div class="text-muted small">Total de casos</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width:56px;height:56px;background:#e9f7ef;">
                    <i class="bi bi-check-circle fs-4 text-success"></i>
                </div>
                <div>
                    <div class="fs-2 fw-bold text-success">{{ $casosActivos }}</div>
                    <div class="text-muted small">Casos activos</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width:56px;height:56px;background:#fdf2e9;">
                    <i class="bi bi-archive fs-4 text-warning"></i>
                </div>
                <div>
                    <div class="fs-2 fw-bold text-warning">{{ $casosArchivados }}</div>
                    <div class="text-muted small">Casos archivados</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Últimos casos --}}
<div class="card border-0 shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center py-3">
        <span>Últimos casos registrados</span>
        <a href="{{ route('expedientes.index') }}" class="btn btn-sm" style="color:#e8a4c8;border-color:#e8a4c8;">Ver todos</a>
    </div>
    <div class="card-body p-0">
        @if($ultimosCasos->isEmpty())
            <p class="text-muted text-center py-4 mb-0">No hay casos registrados aún.</p>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Legajo</th>
                        <th>Apellido y Nombre</th>
                        <th>Localidad</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ultimosCasos as $caso)
                    <tr>
                        <td class="text-muted small">
                            {{ $caso->fecha_recepcion?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td><code>{{ $caso->nro_legajo }}</code></td>
                        <td>{{ $caso->persona?->apellido_nombre ?? $caso->apellido_nombre }}</td>
                        <td>{{ $caso->persona?->localidad?->nombre ?? $caso->localidad?->nombre ?? '—' }}</td>
                        <td><span class="badge bg-secondary">{{ $caso->tipoExpediente->nombre }}</span></td>
                        <td>
                            @if($caso->archivado)
                                <span class="badge bg-warning text-dark">Archivado</span>
                            @else
                                <span class="badge bg-success">Activo</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('expedientes.pdf.expediente', $caso) }}" class="btn btn-sm btn-outline-secondary" title="Imprimir PDF" target="_blank">
                                <i class="bi bi-printer"></i>
                            </a>
                            <a href="{{ route('expedientes.show', $caso) }}" class="btn btn-sm btn-outline-primary" title="Ver">
                                <i class="bi bi-eye"></i>
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
