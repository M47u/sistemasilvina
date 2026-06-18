@extends('layouts.panel')
@section('title', 'Nuevo usuario')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-semibold">Nuevo usuario</h4>
    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('usuarios.store') }}"
              data-confirm="¿Confirmás la creación del nuevo usuario?"
              data-confirm-title="Nuevo usuario"
              data-confirm-accept="Crear usuario"
              data-confirm-icon="bi-person-plus"
              data-confirm-variant="primary">
            @csrf
            @include('usuarios._form')
            <hr class="my-4">
            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> Crear usuario
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
