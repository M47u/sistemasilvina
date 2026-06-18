@extends('layouts.panel')
@section('title', 'Tipos de Expediente')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-6">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-semibold">Tipos de Expediente</h4>
</div>

{{-- Formulario agregar --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold py-3">
        <i class="bi bi-plus-circle me-1 text-primary"></i> Agregar tipo
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('tipos-expediente.store') }}" class="d-flex gap-2">
            @csrf
            <div class="flex-grow-1">
                <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                       placeholder="Nombre del tipo" value="{{ old('nombre') }}" required>
                @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Agregar
            </button>
        </form>
    </div>
</div>

{{-- Listado --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th class="text-end">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tipos as $tipo)
                <tr>
                    <td class="text-muted small">{{ $tipo->id }}</td>
                    <td>{{ $tipo->nombre }}</td>
                    <td class="text-end">
                        <form method="POST" action="{{ route('tipos-expediente.destroy', $tipo) }}"
                              class="d-inline" data-confirm="¿Eliminar el tipo de expediente {{ $tipo->nombre }}?">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center text-muted py-4">No hay tipos registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($tipos->hasPages())
<div class="mt-3 d-flex justify-content-center">
    {{ $tipos->links('pagination::bootstrap-5') }}
</div>
@endif

</div>
</div>
@endsection
