{{-- Partial compartido por create y edit --}}
{{-- Variables esperadas: $tipos, $localidades, $profesionales, $caso (opcional) --}}
@php $caso = $caso ?? null; $persona = $caso?->persona; @endphp

<div class="row g-3">

    {{-- Sección: Expediente --}}
    <div class="col-12">
        <h6 class="text-muted text-uppercase fw-semibold small mb-0">
            <i class="bi bi-file-earmark-text me-1"></i> Datos del expediente
        </h6>
        <hr class="mt-1">
    </div>

    <div class="col-md-2">
        <label for="fecha_recepcion" class="form-label">Fecha recepción <span class="text-danger">*</span></label>
        <input type="date" name="fecha_recepcion" id="fecha_recepcion"
               class="form-control @error('fecha_recepcion') is-invalid @enderror"
               value="{{ old('fecha_recepcion', $caso?->fecha_recepcion?->format('Y-m-d')) }}">
        @error('fecha_recepcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-2">
        <label for="nro_legajo" class="form-label">Nro. Legajo <span class="text-danger">*</span></label>
        <input type="text" name="nro_legajo" id="nro_legajo"
               class="form-control @error('nro_legajo') is-invalid @enderror"
               value="{{ old('nro_legajo', $caso?->nro_legajo) }}" placeholder="2125/26" required>
        @error('nro_legajo') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-2">
        <label for="nro_expediente" class="form-label">Nro. Expediente</label>
        <input type="text" name="nro_expediente" id="nro_expediente"
               class="form-control @error('nro_expediente') is-invalid @enderror"
               value="{{ old('nro_expediente', $caso?->nro_expediente) }}" placeholder="S-000932/26">
        @error('nro_expediente') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label for="tipo_expediente_id" class="form-label">Tipo <span class="text-danger">*</span></label>
        <select name="tipo_expediente_id" id="tipo_expediente_id"
                class="form-select @error('tipo_expediente_id') is-invalid @enderror" required>
            <option value="">— Seleccionar —</option>
            @foreach($tipos as $tipo)
                <option value="{{ $tipo->id }}"
                    {{ old('tipo_expediente_id', $caso?->tipo_expediente_id) == $tipo->id ? 'selected' : '' }}>
                    {{ $tipo->nombre }}
                </option>
            @endforeach
        </select>
        @error('tipo_expediente_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-2">
        <label for="via_acceso" class="form-label">Vía de acceso</label>
        <select name="via_acceso" id="via_acceso"
                class="form-select @error('via_acceso') is-invalid @enderror">
            <option value="">— Seleccionar —</option>
            @foreach(['Despacho','Presencial','Telefónico','Visita','Guardia','Redes sociales','Nueva Formosa','Otro'] as $via)
                <option value="{{ $via }}"
                    {{ old('via_acceso', $caso?->via_acceso) == $via ? 'selected' : '' }}>
                    {{ $via }}
                </option>
            @endforeach
        </select>
        @error('via_acceso') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-1 d-flex align-items-end pb-1">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="urgente" id="urgente" value="1"
                   {{ old('urgente', $caso?->urgente) ? 'checked' : '' }}>
            <label class="form-check-label text-danger fw-semibold" for="urgente">Urgente</label>
        </div>
    </div>

    {{-- Sección: Persona --}}
    <div class="col-12 mt-2">
        <h6 class="text-muted text-uppercase fw-semibold small mb-0">
            <i class="bi bi-person me-1"></i> Datos de la persona
        </h6>
        <hr class="mt-1">
    </div>

    <div class="col-md-5">
        <label for="apellido_nombre" class="form-label">Apellido y Nombre <span class="text-danger">*</span></label>
        <input type="text" name="apellido_nombre" id="apellido_nombre"
               class="form-control @error('apellido_nombre') is-invalid @enderror"
               value="{{ old('apellido_nombre', $persona?->apellido_nombre ?? $caso?->apellido_nombre) }}" required>
        @error('apellido_nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-2">
        <label for="dni" class="form-label">DNI</label>
        <input type="text" name="dni" id="dni"
               class="form-control @error('dni') is-invalid @enderror"
               value="{{ old('dni', $persona?->dni ?? $caso?->dni) }}">
        @error('dni') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label for="localidad_id" class="form-label">Localidad</label>
        <select name="localidad_id" id="localidad_id"
                class="form-select @error('localidad_id') is-invalid @enderror">
            <option value="">— Seleccionar —</option>
            @foreach($localidades as $loc)
                <option value="{{ $loc->id }}"
                    {{ old('localidad_id', $persona?->localidad_id ?? $caso?->localidad_id) == $loc->id ? 'selected' : '' }}>
                    {{ $loc->nombre }}
                </option>
            @endforeach
        </select>
        @error('localidad_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-2">
        <label for="barrio" class="form-label">Barrio</label>
        <input type="text" name="barrio" id="barrio"
               class="form-control @error('barrio') is-invalid @enderror"
               value="{{ old('barrio', $persona?->barrio ?? $caso?->barrio) }}">
        @error('barrio') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label for="telefono" class="form-label">Teléfono</label>
        <input type="text" name="telefono" id="telefono"
               class="form-control @error('telefono') is-invalid @enderror"
               value="{{ old('telefono', $persona?->telefono ?? $caso?->telefono) }}">
        @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label for="direccion" class="form-label">Dirección</label>
        <input type="text" name="direccion" id="direccion"
               class="form-control @error('direccion') is-invalid @enderror"
               value="{{ old('direccion', $persona?->direccion) }}">
        @error('direccion') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-5">
        <label for="denunciado" class="form-label">Denunciado</label>
        <input type="text" name="denunciado" id="denunciado"
               class="form-control @error('denunciado') is-invalid @enderror"
               value="{{ old('denunciado', $caso?->denunciado) }}">
        @error('denunciado') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Sección: Servicios --}}
    <div class="col-12 mt-2">
        <h6 class="text-muted text-uppercase fw-semibold small mb-0">
            <i class="bi bi-people me-1"></i> Servicios y estado
        </h6>
        <hr class="mt-1">
    </div>

    <div class="col-12">
        <div class="d-flex flex-wrap gap-4">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="acepta_atencion" id="acepta_atencion"
                       value="1" {{ old('acepta_atencion', $caso?->acepta_atencion) ? 'checked' : '' }}>
                <label class="form-check-label" for="acepta_atencion">Acepta atención</label>
            </div>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="servicio_legal" id="servicio_legal"
                       value="1" {{ old('servicio_legal', $caso?->servicio_legal) ? 'checked' : '' }}>
                <label class="form-check-label" for="servicio_legal">Servicio Legal</label>
            </div>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="servicio_psicologico" id="servicio_psicologico"
                       value="1" {{ old('servicio_psicologico', $caso?->servicio_psicologico) ? 'checked' : '' }}>
                <label class="form-check-label" for="servicio_psicologico">Serv. Psicológico</label>
            </div>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="servicio_social" id="servicio_social"
                       value="1" {{ old('servicio_social', $caso?->servicio_social) ? 'checked' : '' }}>
                <label class="form-check-label" for="servicio_social">Servicio Social</label>
            </div>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="archivado" id="archivado"
                       value="1" {{ old('archivado', $caso?->archivado) ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold" for="archivado">Archivado</label>
            </div>
        </div>
    </div>

    {{-- Sección: Profesionales (solo en create) --}}
    @if(!$caso)
    <div class="col-12 mt-2">
        <h6 class="text-muted text-uppercase fw-semibold small mb-0">
            <i class="bi bi-person-badge me-1"></i> Asignación de profesionales
            <span class="text-muted fw-normal normal-case" style="text-transform:none; font-size:.8rem">(opcional)</span>
        </h6>
        <hr class="mt-1">
    </div>

    <div class="col-md-4">
        <label for="profesional_legal_id" class="form-label">Profesional Legal</label>
        <select name="profesional_legal_id" id="profesional_legal_id" class="form-select">
            <option value="">— Sin asignar —</option>
            @foreach($profesionales->where('area', 'legal') as $p)
                <option value="{{ $p->id }}" {{ old('profesional_legal_id') == $p->id ? 'selected' : '' }}>
                    {{ $p->apellido }}, {{ $p->nombre }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label for="profesional_psico_id" class="form-label">Profesional Psicología</label>
        <select name="profesional_psico_id" id="profesional_psico_id" class="form-select">
            <option value="">— Sin asignar —</option>
            @foreach($profesionales->where('area', 'psicologia') as $p)
                <option value="{{ $p->id }}" {{ old('profesional_psico_id') == $p->id ? 'selected' : '' }}>
                    {{ $p->apellido }}, {{ $p->nombre }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label for="profesional_social_id" class="form-label">Profesional Social</label>
        <select name="profesional_social_id" id="profesional_social_id" class="form-select">
            <option value="">— Sin asignar —</option>
            @foreach($profesionales->where('area', 'social') as $p)
                <option value="{{ $p->id }}" {{ old('profesional_social_id') == $p->id ? 'selected' : '' }}>
                    {{ $p->apellido }}, {{ $p->nombre }}
                </option>
            @endforeach
        </select>
    </div>
    @endif

    {{-- Resumen y observaciones --}}
    <div class="col-12 mt-2">
        <h6 class="text-muted text-uppercase fw-semibold small mb-0">
            <i class="bi bi-chat-text me-1"></i> Notas
        </h6>
        <hr class="mt-1">
    </div>

    <div class="col-md-6">
        <label for="resumen" class="form-label">Resumen</label>
        <textarea name="resumen" id="resumen" rows="3"
                  class="form-control @error('resumen') is-invalid @enderror">{{ old('resumen', $caso?->resumen) }}</textarea>
        @error('resumen') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label for="observaciones" class="form-label">Observaciones</label>
        <textarea name="observaciones" id="observaciones" rows="3"
                  class="form-control @error('observaciones') is-invalid @enderror">{{ old('observaciones', $caso?->observaciones) }}</textarea>
        @error('observaciones') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label for="fecha_devolucion" class="form-label">Fecha de devolución</label>
        <input type="date" name="fecha_devolucion" id="fecha_devolucion"
               class="form-control @error('fecha_devolucion') is-invalid @enderror"
               value="{{ old('fecha_devolucion', $caso?->fecha_devolucion?->format('Y-m-d')) }}">
        @error('fecha_devolucion') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

</div>
