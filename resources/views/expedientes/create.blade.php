@extends('layouts.panel')
@section('title', 'Nuevo expediente')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-semibold">Nuevo expediente</h4>
    <a href="{{ route('expedientes.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

@php
    $persona              = $persona              ?? null;
    $dni                  = $dni                  ?? null;
    $legajoSugerido       = $legajoSugerido       ?? null;
    $profesionales        = $profesionales        ?? collect();
    $profesionalesPorArea = $profesionalesPorArea ?? [];
@endphp

{{-- ══════════════════════════════════════════════════════
     PASO 1 — Búsqueda por DNI
══════════════════════════════════════════════════════ --}}
@if(!$persona && !$dni)

<div class="row justify-content-center">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-section py-3">
                <i class="bi bi-search me-2"></i>Buscar persona por DNI
            </div>
            <div class="card-body p-4">
                <p class="text-muted small mb-4">
                    Ingresá el DNI de la persona para verificar si ya tiene un legajo en el sistema.
                </p>
                <form method="POST" action="{{ route('expedientes.buscar-persona') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="dni" class="form-label fw-semibold">DNI <span class="text-danger">*</span></label>
                        <input type="text" name="dni" id="dni" autofocus
                               class="form-control form-control-lg @error('dni') is-invalid @enderror"
                               placeholder="Ej: 30123456" value="{{ old('dni') }}" required>
                        @error('dni') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-search me-2"></i>Buscar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     PASO 2A — Persona encontrada
══════════════════════════════════════════════════════ --}}
@elseif($persona)

{{-- Card informativo de la persona --}}
<div class="card border-0 shadow-sm mb-4" style="border-left: 4px solid var(--sm-blue) !important">
    <div class="card-body py-3 px-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <div style="width:42px; height:42px; border-radius:50%; background:var(--sm-blue); display:flex; align-items:center; justify-content:center">
                    <i class="bi bi-person-check-fill text-white"></i>
                </div>
                <div>
                    <div class="fw-semibold" style="color:var(--sm-navy); font-size:1.05rem">
                        {{ $persona->apellido_nombre }}
                    </div>
                    <div class="text-muted small">
                        DNI: {{ $persona->dni ?? '—' }}
                        @if($persona->nro_legajo)
                            &nbsp;·&nbsp; Legajo: <code>{{ $persona->nro_legajo }}</code>
                        @endif
                        @if($persona->localidad)
                            &nbsp;·&nbsp; {{ $persona->localidad->nombre }}
                        @endif
                        @php $cantExp = $persona->expedientes()->count(); @endphp
                        @if($cantExp > 0)
                            &nbsp;·&nbsp;
                            <span class="badge bg-primary">{{ $cantExp }} {{ $cantExp == 1 ? 'expediente anterior' : 'expedientes anteriores' }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <a href="{{ route('expedientes.create') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-search me-1"></i>Buscar otro DNI
            </a>
        </div>
    </div>
</div>

{{-- Formulario del expediente --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-section py-3">
        <i class="bi bi-file-earmark-plus me-2"></i>Datos del nuevo expediente
    </div>
    <div class="card-body p-4">
        <form method="POST" action="{{ route('expedientes.store') }}">
            @csrf
            <input type="hidden" name="persona_id" value="{{ $persona->id }}">
            @include('expedientes._campos_expediente', [
                'mostrarProfesionales'  => false,
                'profesionalesPorArea'  => $profesionalesPorArea,
            ])
            <hr class="my-4">
            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('expedientes.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Guardar expediente
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     PASO 2B — Persona no encontrada
══════════════════════════════════════════════════════ --}}
@else

<div class="alert d-flex align-items-center gap-2 mb-4"
     style="background:#fff8e1; border:1px solid #ffe082; color:#5f4c00">
    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
    <div>
        No se encontró ninguna persona con DNI <strong>{{ $dni }}</strong> en el sistema.
        Completá los datos para crear su legajo y primer expediente.
    </div>
    <a href="{{ route('expedientes.create') }}" class="btn btn-sm btn-outline-secondary ms-auto text-nowrap">
        <i class="bi bi-search me-1"></i>Buscar otro DNI
    </a>
</div>

<form method="POST" action="{{ route('expedientes.store') }}">
    @csrf
    <input type="hidden" name="dni" value="{{ $dni }}">

    {{-- Datos del nuevo legajo --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-section py-3"
             style="background: linear-gradient(135deg, #2a547e 0%, var(--sm-blue) 100%) !important">
            <i class="bi bi-person-plus me-2"></i>Nuevo legajo
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-2">
                    <label for="nro_legajo" class="form-label fw-semibold">
                        Nro. Legajo <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="nro_legajo" id="nro_legajo"
                           class="form-control @error('nro_legajo') is-invalid @enderror"
                           value="{{ old('nro_legajo', $legajoSugerido) }}" required>
                    @error('nro_legajo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @else
                        <div class="form-text text-success">
                            <i class="bi bi-magic me-1"></i>Sugerido: {{ $legajoSugerido }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="apellido_nombre" class="form-label fw-semibold">
                        Apellido y Nombre <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="apellido_nombre" id="apellido_nombre"
                           class="form-control @error('apellido_nombre') is-invalid @enderror"
                           value="{{ old('apellido_nombre') }}" required>
                    @error('apellido_nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-2">
                    <label class="form-label">DNI</label>
                    <input type="text" class="form-control bg-light" value="{{ $dni }}" readonly>
                </div>

                <div class="col-md-4">
                    <label for="localidad_id" class="form-label">Localidad</label>
                    <select name="localidad_id" id="localidad_id"
                            class="form-select @error('localidad_id') is-invalid @enderror">
                        <option value="">— Seleccionar —</option>
                        @foreach($localidades as $loc)
                            <option value="{{ $loc->id }}" {{ old('localidad_id') == $loc->id ? 'selected' : '' }}>
                                {{ $loc->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('localidad_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label for="barrio" class="form-label">Barrio</label>
                    <input type="text" name="barrio" id="barrio"
                           class="form-control @error('barrio') is-invalid @enderror"
                           value="{{ old('barrio') }}">
                    @error('barrio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label for="telefono" class="form-label">Teléfono</label>
                    <input type="text" name="telefono" id="telefono"
                           class="form-control @error('telefono') is-invalid @enderror"
                           value="{{ old('telefono') }}">
                    @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label for="direccion" class="form-label">Dirección</label>
                    <input type="text" name="direccion" id="direccion"
                           class="form-control @error('direccion') is-invalid @enderror"
                           value="{{ old('direccion') }}">
                    @error('direccion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Datos del primer expediente --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-section py-3">
            <i class="bi bi-file-earmark-plus me-2"></i>Datos del primer expediente
        </div>
        <div class="card-body p-4">
            @include('expedientes._campos_expediente')
            <hr class="my-4">
            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('expedientes.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Crear legajo y guardar expediente
                </button>
            </div>
        </div>
    </div>

</form>

@endif
@endsection
