@extends('layouts.panel')
@section('title', 'Usuarios')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0 fw-semibold">Usuarios</h4>
    <a href="{{ route('usuarios.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-person-plus me-1"></i> Nuevo usuario
    </a>
</div>


<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Roles</th>
                        <th>Área (si profesional)</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $usuario)
                    <tr>
                        <td class="fw-semibold">{{ $usuario->name }}</td>
                        <td class="text-muted">{{ $usuario->email }}</td>
                        <td>
                            @foreach($usuario->roles as $rol)
                                @php
                                    $color = match($rol->name) {
                                        'Coordinadora'  => 'primary',
                                        'Profesional'   => 'info',
                                        'Administrativo'=> 'secondary',
                                        default         => 'dark',
                                    };
                                @endphp
                                <span class="badge bg-{{ $color }} me-1">{{ $rol->name }}</span>
                            @endforeach
                        </td>
                        <td>
                            @if($usuario->profesional)
                                {{ ucfirst($usuario->profesional->area) }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($usuario->profesional && !$usuario->profesional->activo)
                                <span class="badge bg-warning text-dark">Inactivo</span>
                            @else
                                <span class="badge bg-success">Activo</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-sm btn-outline-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if($usuario->id !== auth()->id())
                            <form method="POST" action="{{ route('usuarios.destroy', $usuario) }}" class="d-inline"
                                  data-confirm="¿Eliminar al usuario {{ $usuario->name }}?">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No hay usuarios registrados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($usuarios->hasPages())
<div class="mt-3 d-flex justify-content-center">
    {{ $usuarios->links('pagination::bootstrap-5') }}
</div>
@endif
@endsection
