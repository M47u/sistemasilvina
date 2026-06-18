@extends('layouts.panel')
@section('title', 'Expediente ' . $expediente->nro_legajo)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-semibold">
        Legajo <code>{{ $expediente->nro_legajo ?? '—' }}</code>
        @if($expediente->urgente)
            <span class="badge bg-danger ms-2">Urgente</span>
        @endif
        @php
            $estadoBadge = match($expediente->estado) {
                'archivado' => ['label' => 'Archivado', 'class' => 'bg-secondary'],
                'derivado'  => ['label' => 'Derivado',  'class' => 'bg-warning text-dark'],
                default     => ['label' => 'Activo',    'class' => 'bg-success'],
            };
        @endphp
        <span class="badge {{ $estadoBadge['class'] }} ms-1">{{ $estadoBadge['label'] }}</span>
    </h4>
    <div class="d-flex gap-2">
        <a href="{{ route('expedientes.pdf.expediente', $expediente) }}" class="btn btn-outline-danger btn-sm" target="_blank">
            <i class="bi bi-file-earmark-pdf me-1"></i> PDF
        </a>
        @hasanyrole('Coordinadora|Administrativo')
        <a href="{{ route('expedientes.edit', $expediente) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil me-1"></i> Editar
        </a>
        @endhasanyrole
        @role('Profesional')
        @if($miAsignacionActiva)
        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalSalida">
            <i class="bi bi-box-arrow-right me-1"></i> Dar salida
        </button>
        @endif
        <a href="{{ route('mis-expedientes.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
        @else
        <a href="{{ route('expedientes.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
        @endrole
    </div>
</div>

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
                        @if($expediente->fecha_recepcion)
                            {{ $expediente->fecha_recepcion->format('d/m/Y') }}
                        @else
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-calendar-x me-1"></i>Pendiente asignación
                            </span>
                        @endif
                    </dd>

                    <dt class="col-sm-5 text-muted">Nro. Legajo</dt>
                    <dd class="col-sm-7"><code>{{ $expediente->nro_legajo ?? '—' }}</code></dd>

                    <dt class="col-sm-5 text-muted">Nro. Expediente</dt>
                    <dd class="col-sm-7">{!! $expediente->nro_expediente ? '<code>'.$expediente->nro_expediente.'</code>' : '—' !!}</dd>

                    <dt class="col-sm-5 text-muted">Tipo</dt>
                    <dd class="col-sm-7">
                        <span class="badge bg-secondary">{{ $expediente->tipoExpediente->nombre }}</span>
                    </dd>

                    <dt class="col-sm-5 text-muted">Vía de acceso</dt>
                    <dd class="col-sm-7">{{ $expediente->via_acceso ?? '—' }}</dd>

                    <dt class="col-sm-5 text-muted">Fecha devolución</dt>
                    <dd class="col-sm-7">{{ $expediente->fecha_devolucion?->format('d/m/Y') ?? '—' }}</dd>

                    @if($expediente->creadoPor)
                    <dt class="col-sm-5 text-muted">Registrado por</dt>
                    <dd class="col-sm-7">{{ $expediente->creadoPor->name }}</dd>
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
                @if($expediente->persona && $expediente->persona->expedientes()->count() > 1)
                    <a href="{{ route('personas.expedientes', $expediente->persona) }}" class="badge bg-primary text-decoration-none">
                        <i class="bi bi-clock-history me-1"></i>
                        {{ $expediente->persona->expedientes()->count() }} expedientes en este legajo
                    </a>
                @endif
            </div>
            <div class="card-body">
                @php $p = $expediente->persona; @endphp
                <dl class="row mb-0">
                    <dt class="col-sm-5 text-muted">Apellido y Nombre</dt>
                    <dd class="col-sm-7 fw-semibold">{{ $p?->apellido_nombre ?? $expediente->apellido_nombre }}</dd>

                    <dt class="col-sm-5 text-muted">DNI</dt>
                    <dd class="col-sm-7">{{ $p?->dni ?? $expediente->dni ?? '—' }}</dd>

                    <dt class="col-sm-5 text-muted">Localidad</dt>
                    <dd class="col-sm-7">{{ $p?->localidad?->nombre ?? '—' }}</dd>

                    <dt class="col-sm-5 text-muted">Barrio</dt>
                    <dd class="col-sm-7">{{ $p?->barrio ?? $expediente->barrio ?? '—' }}</dd>

                    <dt class="col-sm-5 text-muted">Teléfono</dt>
                    <dd class="col-sm-7">{{ $p?->telefono ?? $expediente->telefono ?? '—' }}</dd>

                    <dt class="col-sm-5 text-muted">Dirección</dt>
                    <dd class="col-sm-7">{{ $p?->direccion ?? '—' }}</dd>

                    <dt class="col-sm-5 text-muted">Denunciado</dt>
                    <dd class="col-sm-7">{{ $expediente->denunciado ?? '—' }}</dd>
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
                        ['acepta_atencion', 'Acepta atención', 'success', null],
                        ['servicio_legal', 'Legal', 'info', null],
                        ['servicio_psicologico', 'Psicológico', null, '#6f42c1'],
                        ['servicio_social', 'Social', null, '#20c997'],
                    ] as [$campo, $label, $color, $hex])
                    <div>
                        <span class="text-muted small d-block">{{ $label }}</span>
                        @if($expediente->$campo)
                            <span class="badge" style="{{ $hex ? 'background:'.$hex : 'background-color:var(--bs-'.$color.')' }}; color:#fff">Sí</span>
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
                @if($expediente->resumen)
                    <p class="text-muted small mb-1">Resumen</p>
                    <p class="mb-3">{{ $expediente->resumen }}</p>
                @endif
                @if($expediente->observaciones)
                    <p class="text-muted small mb-1">Observaciones</p>
                    <p class="mb-0">{{ $expediente->observaciones }}</p>
                @endif
                @if(!$expediente->resumen && !$expediente->observaciones)
                    <p class="text-muted mb-0">Sin notas.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Intervenciones --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold py-3 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history me-1 text-primary"></i> Intervenciones</span>
                <span class="badge bg-secondary">{{ $expediente->intervenciones->count() }}</span>
            </div>
            <div class="card-body">

                {{-- Formulario nueva intervención --}}
                <form method="POST" action="{{ route('expedientes.intervenciones.store', $expediente) }}" class="row g-2 mb-4">
                    @csrf
                    <div class="col-md-2">
                        <label class="form-label form-label-sm">Tipo</label>
                        <select name="tipo" class="form-select form-select-sm @error('tipo') is-invalid @enderror" required>
                            <option value="">— Tipo —</option>
                            <option value="entrevista">Entrevista</option>
                            <option value="llamada">Llamada</option>
                            <option value="seguimiento">Seguimiento</option>
                            <option value="derivacion">Derivación</option>
                            <option value="nota">Nota</option>
                        </select>
                        @error('tipo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label form-label-sm">Fecha</label>
                        <input type="datetime-local" name="fecha" value="{{ old('fecha', now()->format('Y-m-d\TH:i')) }}"
                               class="form-control form-control-sm @error('fecha') is-invalid @enderror" required>
                        @error('fecha') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label form-label-sm">Descripción</label>
                        <input type="text" name="descripcion" value="{{ old('descripcion') }}"
                               class="form-control form-control-sm @error('descripcion') is-invalid @enderror"
                               placeholder="Describí la intervención..." required maxlength="2000">
                        @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-plus-lg me-1"></i> Registrar
                        </button>
                    </div>
                </form>

                {{-- Timeline --}}
                @if($expediente->intervenciones->isNotEmpty())
                @php
                    $tipoConfig = [
                        'entrevista'  => ['label' => 'Entrevista',  'color' => 'primary',   'icon' => 'bi-person-lines-fill'],
                        'llamada'     => ['label' => 'Llamada',     'color' => 'info',       'icon' => 'bi-telephone-fill'],
                        'seguimiento' => ['label' => 'Seguimiento', 'color' => 'success',    'icon' => 'bi-arrow-repeat'],
                        'derivacion'  => ['label' => 'Derivación',  'color' => 'warning',    'icon' => 'bi-arrow-right-circle-fill'],
                        'nota'        => ['label' => 'Nota',        'color' => 'secondary',  'icon' => 'bi-sticky-fill'],
                    ];
                @endphp
                <div class="timeline">
                    @foreach($expediente->intervenciones as $intervencion)
                    @php $cfg = $tipoConfig[$intervencion->tipo] ?? ['label' => $intervencion->tipo, 'color' => 'secondary', 'icon' => 'bi-dot']; @endphp
                    <div class="timeline-item">
                        <div class="timeline-marker bg-{{ $cfg['color'] }}">
                            <i class="bi {{ $cfg['icon'] }}"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div>
                                    <span class="badge bg-{{ $cfg['color'] }} {{ $cfg['color'] === 'warning' ? 'text-dark' : '' }} me-1">{{ $cfg['label'] }}</span>
                                    <span class="text-muted small">{{ $intervencion->fecha->format('d/m/Y H:i') }}</span>
                                    <span class="text-muted small ms-2">— {{ $intervencion->creadoPor?->name ?? '—' }}</span>
                                </div>
                                @role('Coordinadora')
                                <form method="POST"
                                      action="{{ route('expedientes.intervenciones.destroy', [$expediente, $intervencion]) }}"
                                      class="d-inline flex-shrink-0"
                                      data-confirm="¿Eliminar esta intervención? No se puede deshacer."
                                      data-confirm-title="Eliminar intervención">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-link btn-sm text-danger p-0" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endrole
                            </div>
                            <p class="mb-0 mt-1">{{ $intervencion->descripcion }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                    <p class="text-muted small mb-0">Sin intervenciones registradas.</p>
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
                @if($expediente->profesionales->count())
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
                            @foreach($expediente->profesionales->sortByDesc('pivot.fecha_asignacion') as $prof)
                            <tr>
                                <td>{{ $prof->apellido }}, {{ $prof->nombre }}</td>
                                <td><span class="badge bg-secondary">{{ ucfirst($prof->pivot->area) }}</span></td>
                                <td>{{ \Carbon\Carbon::parse($prof->pivot->fecha_asignacion)->format('d/m/Y H:i') }}</td>
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
                @hasanyrole('Coordinadora|Administrativo')
                <form method="POST" action="{{ route('expedientes.profesionales.asignar', $expediente) }}"
                      class="row g-2 align-items-end"
                      data-confirm="¿Confirmás la asignación de este profesional al expediente?"
                      data-confirm-title="Asignar profesional"
                      data-confirm-accept="Asignar"
                      data-confirm-icon="bi-person-check"
                      data-confirm-variant="primary">
                    @csrf
                    <div class="col-md-6">
                        <label class="form-label form-label-sm">Profesional</label>
                        <select name="profesional_id" id="selectProfesional" class="form-select form-select-sm" required>
                            <option value="">— Seleccionar —</option>
                            @foreach($profesionales as $p)
                                <option value="{{ $p->id }}" data-area="{{ $p->area }}">
                                    {{ $p->apellido }}, {{ $p->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end pb-1">
                        <span id="areaBadge" class="badge bg-secondary" style="display:none!important"></span>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-plus-lg me-1"></i> Asignar
                        </button>
                    </div>
                </form>
                @endhasanyrole

                @push('scripts')
                <script>
                (function () {
                    const sel   = document.getElementById('selectProfesional');
                    const badge = document.getElementById('areaBadge');
                    if (!sel) return;

                    const labels = { legal: 'Legal', psicologia: 'Psicología', social: 'Social' };
                    const colors = { legal: 'info', psicologia: 'primary', social: 'success' };

                    sel.addEventListener('change', function () {
                        const area = this.selectedOptions[0]?.dataset.area;
                        if (area) {
                            badge.textContent = labels[area] ?? area;
                            badge.className   = 'badge bg-' + (colors[area] ?? 'secondary');
                            badge.style.removeProperty('display');
                        } else {
                            badge.style.setProperty('display', 'none', 'important');
                        }
                    });
                })();
                </script>
                @endpush

            </div>
        </div>
    </div>

</div>

{{-- Eliminar (solo Coordinadora/Administrativo) --}}
@hasanyrole('Coordinadora|Administrativo')
<div class="mt-4 d-flex justify-content-end">
    <form method="POST" action="{{ route('expedientes.destroy', $expediente) }}"
          data-confirm="¿Eliminar este expediente? Esta acción no se puede deshacer.">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-trash me-1"></i> Eliminar expediente
        </button>
    </form>
</div>
@endhasanyrole

{{-- Modal "Dar salida" (solo Profesional con asignación activa) --}}
@role('Profesional')
@if($miAsignacionActiva)
<div class="modal fade" id="modalSalida" tabindex="-1" aria-labelledby="modalSalidaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="modalSalidaLabel">
                    <i class="bi bi-box-arrow-right me-2"></i>Dar salida al expediente
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('expedientes.salida', $expediente) }}">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        Al confirmar, tu asignación activa en este expediente se cierra. Elegí qué pasa después:
                    </p>
                    <div class="d-flex flex-column gap-3">
                        <label class="card border p-3 d-flex flex-row align-items-start gap-3 cursor-pointer mb-0"
                               style="cursor:pointer" for="accion_derivar">
                            <input class="form-check-input mt-1 flex-shrink-0" type="radio"
                                   name="accion" id="accion_derivar" value="derivar" required>
                            <div>
                                <div class="fw-semibold">
                                    <i class="bi bi-arrow-right-circle me-1 text-warning"></i>Derivar a otra área
                                </div>
                                <div class="text-muted small">Mi intervención terminó. El expediente continúa y puede asignarse a otro profesional.</div>
                            </div>
                        </label>
                        <label class="card border p-3 d-flex flex-row align-items-start gap-3 cursor-pointer mb-0"
                               style="cursor:pointer" for="accion_archivar">
                            <input class="form-check-input mt-1 flex-shrink-0" type="radio"
                                   name="accion" id="accion_archivar" value="archivar">
                            <div>
                                <div class="fw-semibold">
                                    <i class="bi bi-archive me-1 text-secondary"></i>Archivar expediente
                                </div>
                                <div class="text-muted small">El caso finalizó conmigo. Se archiva el expediente.</div>
                            </div>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-box-arrow-right me-1"></i>Confirmar salida
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endrole

@endsection
