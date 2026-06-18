{{-- Campos del expediente — usado en create (pasos 2a y 2b) --}}
<div class="row g-3">

    <div class="col-md-2">
        <label for="fecha_recepcion" class="form-label">Fecha recepción</label>
        <input type="date" name="fecha_recepcion" id="fecha_recepcion"
               class="form-control @error('fecha_recepcion') is-invalid @enderror"
               value="{{ old('fecha_recepcion') }}">
        @error('fecha_recepcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-2">
        <label for="nro_expediente" class="form-label">Nro. Expediente</label>
        <input type="text" name="nro_expediente" id="nro_expediente"
               class="form-control @error('nro_expediente') is-invalid @enderror"
               value="{{ old('nro_expediente') }}" placeholder="S-000932/26">
        @error('nro_expediente') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label for="tipo_expediente_id" class="form-label">Tipo <span class="text-danger">*</span></label>
        <select name="tipo_expediente_id" id="tipo_expediente_id"
                class="form-select @error('tipo_expediente_id') is-invalid @enderror" required>
            <option value="">— Seleccionar —</option>
            @foreach($tipos as $tipo)
                <option value="{{ $tipo->id }}" {{ old('tipo_expediente_id') == $tipo->id ? 'selected' : '' }}>
                    {{ $tipo->nombre }}
                </option>
            @endforeach
        </select>
        @error('tipo_expediente_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label for="via_acceso" class="form-label">Vía de acceso</label>
        <select name="via_acceso" id="via_acceso"
                class="form-select @error('via_acceso') is-invalid @enderror">
            <option value="">— Seleccionar —</option>
            @foreach(['Despacho','Presencial','Telefónico','Visita','Guardia','Redes sociales','Nueva Formosa','Otro'] as $via)
                <option value="{{ $via }}" {{ old('via_acceso') == $via ? 'selected' : '' }}>
                    {{ $via }}
                </option>
            @endforeach
        </select>
        @error('via_acceso') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-2 d-flex align-items-end pb-1">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="urgente" id="urgente"
                   value="1" {{ old('urgente') ? 'checked' : '' }}>
            <label class="form-check-label text-danger fw-semibold" for="urgente">Urgente</label>
        </div>
    </div>

    <div class="col-md-6">
        <label for="denunciado" class="form-label">Denunciado</label>
        <input type="text" name="denunciado" id="denunciado"
               class="form-control @error('denunciado') is-invalid @enderror"
               value="{{ old('denunciado') }}">
        @error('denunciado') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Servicios --}}
    @php
        $autoAsign = !($mostrarProfesionales ?? true);
        $profsArea = $profesionalesPorArea ?? [];
        $serviciosConArea = [
            'servicio_legal'       => ['label' => 'Servicio Legal',    'area' => 'legal',      'fallback' => 'profesional_legal_id'],
            'servicio_psicologico' => ['label' => 'Serv. Psicológico', 'area' => 'psicologia', 'fallback' => 'profesional_psico_id'],
            'servicio_social'      => ['label' => 'Servicio Social',   'area' => 'social',     'fallback' => 'profesional_social_id'],
        ];
    @endphp
    <div class="col-12 mt-1">
        <div class="d-flex flex-wrap gap-4 align-items-start">

            {{-- Acepta atención (sin área) --}}
            <div class="form-check form-switch mt-1">
                <input class="form-check-input" type="checkbox" name="acepta_atencion" id="acepta_atencion"
                       value="1" {{ old('acepta_atencion') ? 'checked' : '' }}>
                <label class="form-check-label" for="acepta_atencion">Acepta atención</label>
            </div>

            {{-- Servicios con profesional asignable --}}
            @foreach($serviciosConArea as $campo => $cfg)
            <div>
                <div class="form-check form-switch">
                    <input class="form-check-input js-servicio-check" type="checkbox"
                           name="{{ $campo }}" id="{{ $campo }}"
                           value="1" {{ old($campo) ? 'checked' : '' }}
                           data-area="{{ $cfg['area'] }}">
                    <label class="form-check-label" for="{{ $campo }}">{{ $cfg['label'] }}</label>
                </div>
                @if($autoAsign)
                <div class="js-prof-badge mt-1" data-area="{{ $cfg['area'] }}"
                     style="{{ old($campo) ? '' : 'display:none' }}">
                    @if(isset($profsArea[$cfg['area']]))
                        <span class="badge" style="background:var(--sm-blue); color:#fff; font-weight:500; font-size:.75rem">
                            <i class="bi bi-person-fill me-1"></i>{{ $profsArea[$cfg['area']] }}
                        </span>
                    @else
                        <select name="{{ $cfg['fallback'] }}" class="form-select form-select-sm mt-1"
                                style="max-width:260px">
                            <option value="">— Asignar profesional (opcional) —</option>
                            @foreach(($profesionales ?? collect())->where('area', $cfg['area']) as $p)
                                <option value="{{ $p->id }}" {{ old($cfg['fallback']) == $p->id ? 'selected' : '' }}>
                                    {{ $p->apellido }}, {{ $p->nombre }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text text-muted" style="font-size:.72rem">
                            <i class="bi bi-info-circle me-1"></i>Podés asignarlo más tarde desde el expediente.
                        </div>
                    @endif
                </div>
                @endif
            </div>
            @endforeach

        </div>
    </div>

    {{-- Profesionales opcionales (solo para primer expediente / persona nueva) --}}
    @if(!$autoAsign)
    <div class="col-12 mt-2">
        <h6 class="text-muted text-uppercase fw-semibold small mb-0">
            <i class="bi bi-person-badge me-1"></i> Asignación de profesionales
            <span class="fw-normal" style="text-transform:none; font-size:.8rem">(opcional)</span>
        </h6>
        <hr class="mt-1">
    </div>

    <div class="col-md-4">
        <label for="profesional_legal_id" class="form-label">Legal</label>
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
        <label for="profesional_psico_id" class="form-label">Psicología</label>
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
        <label for="profesional_social_id" class="form-label">Social</label>
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

    {{-- Notas --}}
    <div class="col-12 mt-2">
        <h6 class="text-muted text-uppercase fw-semibold small mb-0">
            <i class="bi bi-chat-text me-1"></i> Notas
        </h6>
        <hr class="mt-1">
    </div>

    <div class="col-md-6">
        <label for="resumen" class="form-label">Resumen</label>
        <textarea name="resumen" id="resumen" rows="3"
                  class="form-control @error('resumen') is-invalid @enderror">{{ old('resumen') }}</textarea>
        @error('resumen') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label for="observaciones" class="form-label">Observaciones</label>
        <textarea name="observaciones" id="observaciones" rows="3"
                  class="form-control @error('observaciones') is-invalid @enderror">{{ old('observaciones') }}</textarea>
        @error('observaciones') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

</div>

@if($autoAsign ?? false)
@push('scripts')
<script>
document.querySelectorAll('.js-servicio-check').forEach(function(cb) {
    var area  = cb.dataset.area;
    var badge = document.querySelector('.js-prof-badge[data-area="' + area + '"]');
    if (!badge) return;
    cb.addEventListener('change', function() {
        badge.style.display = this.checked ? '' : 'none';
    });
});
</script>
@endpush
@endif
