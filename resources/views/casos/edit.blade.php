@extends('layouts.panel')
@section('title', 'Editar caso')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-semibold">Editar caso <span class="text-muted fw-normal">— {{ $caso->nro_legajo }}</span></h4>
    <a href="{{ route('casos.show', $caso) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('casos.update', $caso) }}">
            @csrf @method('PUT')
            @include('casos._form')
            <hr class="my-4">
            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('casos.show', $caso) }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> Actualizar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
