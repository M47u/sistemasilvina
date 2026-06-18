@extends('layouts.panel')
@section('title', 'Editar usuario')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-semibold">Editar usuario <span class="text-muted fw-normal">— {{ $usuario->name }}</span></h4>
    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('usuarios.update', $usuario) }}"
              data-confirm="¿Confirmás los cambios en este usuario?"
              data-confirm-title="Editar usuario"
              data-confirm-accept="Guardar cambios"
              data-confirm-icon="bi-save"
              data-confirm-variant="primary">
            @csrf @method('PUT')
            @include('usuarios._form')
            <hr class="my-4">
            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
