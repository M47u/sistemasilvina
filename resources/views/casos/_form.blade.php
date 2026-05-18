{{-- Partial compartido por create y edit --}}
{{-- Variables esperadas: $tipos, $localidades, $caso (opcional) --}}
@php $caso = $caso ?? null; @endphp

<div class="row g-3">

    {{-- Fila 1: fechas e identificadores --}}
    <div class="col-md-3">
        <label for="fecha_recepcion" class="form-label">Fecha de recepción <span class="text-danger">*</span></label>
        <input type="date" name="fecha_recepcion" id="fecha_recepcion"
               class="form-control @error('fecha_recepcion') is-invalid @enderror"
               value="{{ old('fecha_recepcion', $caso?->fecha_recepcion?->format('Y-m-d')) }}" required>
        @error('fecha_recepcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label for="nro_legajo" class="form-label">Nro. Legajo <span class="text-danger">*</span></label>
        <input type="text" name="nro_legajo" id="nro_legajo"
               class="form-control @error('nro_legajo') is-invalid @enderror"
               value="{{ old('nro_legajo', $caso?->nro_legajo) }}"
               placeholder="2125/26" required>
        @error('nro_legajo') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label for="nro_expediente" class="form-label">Nro. Expediente <span class="text-danger">*</span></label>
        <input type="text" name="nro_expediente" id="nro_expediente"
               class="form-control @error('nro_expediente') is-invalid @enderror"
               value="{{ old('nro_expediente', $caso?->nro_expediente) }}"
               placeholder="S-000932/26" required>
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

    {{-- Fila 2: persona --}}
    <div class="col-md-6">
        <label for="apellido_nombre" class="form-label">Apellido y Nombre <span class="text-danger">*</span></label>
        <input type="text" name="apellido_nombre" id="apellido_nombre"
               class="form-control @error('apellido_nombre') is-invalid @enderror"
               value="{{ old('apellido_nombre', $caso?->apellido_nombre) }}" required>
        @error('apellido_nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label for="dni" class="form-label">DNI <span class="text-danger">*</span></label>
        <input type="text" name="dni" id="dni"
               class="form-control @error('dni') is-invalid @enderror"
               value="{{ old('dni', $caso?->dni) }}" required>
        @error('dni') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label for="localidad_id" class="form-label">Localidad <span class="text-danger">*</span></label>
        <select name="localidad_id" id="localidad_id"
                class="form-select @error('localidad_id') is-invalid @enderror" required>
            <option value="">— Seleccionar —</option>
            @foreach($localidades as $loc)
                <option value="{{ $loc->id }}"
                    {{ old('localidad_id', $caso?->localidad_id) == $loc->id ? 'selected' : '' }}>
                    {{ $loc->nombre }}
                </option>
            @endforeach
        </select>
        @error('localidad_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Fila 3: contacto --}}
    <div class="col-md-4">
        <label for="barrio" class="form-label">Barrio</label>
        <input type="text" name="barrio" id="barrio"
               class="form-control @error('barrio') is-invalid @enderror"
               value="{{ old('barrio', $caso?->barrio) }}">
        @error('barrio') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label for="telefono" class="form-label">Teléfono</label>
        <input type="text" name="telefono" id="telefono"
               class="form-control @error('telefono') is-invalid @enderror"
               value="{{ old('telefono', $caso?->telefono) }}">
        @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label for="denunciado" class="form-label">Denunciado</label>
        <input type="text" name="denunciado" id="denunciado"
               class="form-control @error('denunciado') is-invalid @enderror"
               value="{{ old('denunciado', $caso?->denunciado) }}">
        @error('denunciado') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Resumen --}}
    <div class="col-12">
        <label for="resumen" class="form-label">Resumen</label>
        <textarea name="resumen" id="resumen" rows="3"
                  class="form-control @error('resumen') is-invalid @enderror">{{ old('resumen', $caso?->resumen) }}</textarea>
        @error('resumen') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Switches --}}
    <div class="col-12">
        <label class="form-label d-block mb-2">Servicios y estado</label>
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

    {{-- Observaciones --}}
    <div class="col-12">
        <label for="observaciones" class="form-label">Observaciones</label>
        <textarea name="observaciones" id="observaciones" rows="3"
                  class="form-control @error('observaciones') is-invalid @enderror">{{ old('observaciones', $caso?->observaciones) }}</textarea>
        @error('observaciones') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Fecha devolución --}}
    <div class="col-md-4">
        <label for="fecha_devolucion" class="form-label">Fecha de devolución</label>
        <input type="date" name="fecha_devolucion" id="fecha_devolucion"
               class="form-control @error('fecha_devolucion') is-invalid @enderror"
               value="{{ old('fecha_devolucion', $caso?->fecha_devolucion?->format('Y-m-d')) }}">
        @error('fecha_devolucion') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

</div>
