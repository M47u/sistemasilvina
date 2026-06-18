{{-- Variables esperadas: $roles, $usuario (opcional) --}}
@php $usuario = $usuario ?? null; @endphp

<div class="row g-3">

    <div class="col-12">
        <h6 class="text-muted text-uppercase fw-semibold small mb-0">
            <i class="bi bi-person me-1"></i> Datos de acceso
        </h6>
        <hr class="mt-1">
    </div>

    <div class="col-md-3">
        <label for="apellido" class="form-label">Apellido <span class="text-danger">*</span></label>
        <input type="text" name="apellido" id="apellido"
               class="form-control @error('apellido') is-invalid @enderror"
               value="{{ old('apellido', $usuario?->profesional?->apellido ?? explode(' ', $usuario?->name ?? '', 2)[0] ?? '') }}" required>
        @error('apellido') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
        <input type="text" name="nombre" id="nombre"
               class="form-control @error('nombre') is-invalid @enderror"
               value="{{ old('nombre', $usuario?->profesional?->nombre ?? (count(explode(' ', $usuario?->name ?? '', 2)) > 1 ? explode(' ', $usuario?->name ?? '', 2)[1] : '')) }}" required>
        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
        <input type="email" name="email" id="email"
               class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $usuario?->email) }}" required>
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label for="password" class="form-label">
            Contraseña {{ $usuario ? '(dejar vacío para no cambiar)' : '' }}
            @if(!$usuario) <span class="text-danger">*</span> @endif
        </label>
        <input type="password" name="password" id="password"
               class="form-control @error('password') is-invalid @enderror"
               autocomplete="new-password">
        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
        <input type="password" name="password_confirmation" id="password_confirmation"
               class="form-control" autocomplete="new-password">
    </div>

    <div class="col-12 mt-2">
        <h6 class="text-muted text-uppercase fw-semibold small mb-0">
            <i class="bi bi-shield me-1"></i> Roles
        </h6>
        <hr class="mt-1">
    </div>

    <div class="col-12">
        @error('roles') <div class="alert alert-danger py-2">{{ $message }}</div> @enderror
        <div class="d-flex flex-wrap gap-4">
            @foreach($roles as $rol)
            @php
                $checked = in_array($rol->name, old('roles', $usuario?->roles->pluck('name')->toArray() ?? []));
                $color   = match($rol->name) {
                    'Coordinadora'   => 'primary',
                    'Profesional'    => 'info',
                    'Administrativo' => 'secondary',
                    default          => 'dark',
                };
            @endphp
            <div class="form-check">
                <input class="form-check-input rol-checkbox" type="checkbox"
                       name="roles[]" id="rol_{{ $rol->name }}"
                       value="{{ $rol->name }}" {{ $checked ? 'checked' : '' }}
                       data-rol="{{ $rol->name }}">
                <label class="form-check-label" for="rol_{{ $rol->name }}">
                    <span class="badge bg-{{ $color }}">{{ $rol->name }}</span>
                </label>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Sección profesional: visible solo si el rol Profesional está marcado --}}
    <div class="col-12 mt-2" id="seccion-profesional" style="{{ in_array('Profesional', old('roles', $usuario?->roles->pluck('name')->toArray() ?? [])) ? '' : 'display:none' }}">
        <h6 class="text-muted text-uppercase fw-semibold small mb-0">
            <i class="bi bi-person-badge me-1"></i> Datos del profesional
        </h6>
        <hr class="mt-1">
        <div class="row g-3">
            <div class="col-md-6">
                <label for="area" class="form-label">Área principal <span class="text-danger">*</span></label>
                <select name="area" id="area"
                        class="form-select @error('area') is-invalid @enderror">
                    <option value="">— Seleccionar —</option>
                    <option value="legal"      {{ old('area', $usuario?->profesional?->area) === 'legal'      ? 'selected' : '' }}>Legal</option>
                    <option value="psicologia" {{ old('area', $usuario?->profesional?->area) === 'psicologia' ? 'selected' : '' }}>Psicología</option>
                    <option value="social"     {{ old('area', $usuario?->profesional?->area) === 'social'     ? 'selected' : '' }}>Social</option>
                </select>
                @error('area') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.querySelectorAll('.rol-checkbox').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        const esProfesional = document.querySelector('[data-rol="Profesional"]').checked;
        document.getElementById('seccion-profesional').style.display = esProfesional ? '' : 'none';
    });
});
</script>
@endpush
