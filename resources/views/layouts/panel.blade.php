<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Inicio') — SIGESM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --sm-navy:   #173a5e;
            --sm-blue:   #2d87c7;
            --sm-pink:   #e8a4c8;
            --sm-pink-light: #fdf0f7;
            --sm-blue-light: #eaf4fb;
        }

        body { background-color: var(--sm-pink-light); }

        /* Navbar */
        .navbar { background-color: var(--sm-navy) !important; }
        .navbar-brand { font-weight: 700; letter-spacing: .3px; }
        .navbar .nav-link.active,
        .navbar .nav-link:focus { color: var(--sm-pink) !important; }
        .navbar .nav-link:hover { color: #f3c8dd !important; }

        /* Tables */
        .table thead th {
            font-size: .8rem;
            text-transform: uppercase;
            letter-spacing: .5px;
            background-color: var(--sm-navy);
            color: #fff;
            border-color: #204f7e;
        }
        .table-hover tbody tr:hover { background-color: var(--sm-blue-light); }

        /* Cards */
        .card { border: none; box-shadow: 0 2px 8px rgba(23,58,94,.10); }
        .card-header {
            background-color: var(--sm-navy);
            color: #fff;
            font-weight: 600;
        }
        .card-header.bg-white {
            color: var(--sm-navy) !important;
            border-bottom: 2px solid var(--sm-blue-light);
        }
        .card-header.bg-section {
            background: linear-gradient(135deg, var(--sm-navy) 0%, #1e5c94 100%) !important;
            color: #fff !important;
            border-bottom: none;
        }

        /* Buttons */
        .btn-primary {
            background-color: var(--sm-blue);
            border-color: var(--sm-blue);
        }
        .btn-primary:hover, .btn-primary:focus {
            background-color: #1e6fa8;
            border-color: #1e6fa8;
        }

        /* Badges / pills */
        .badge-servicio { font-size: .7rem; }
        .badge.bg-primary { background-color: var(--sm-blue) !important; }
        .badge.bg-secondary { background-color: #8b5c7a !important; }

        /* Alert success */
        .alert-success {
            background-color: #e6f4ea;
            border-color: #c3e6cb;
            color: #1e5631;
        }

        /* Page titles */
        h1, h2, h3, h4, h5 { color: var(--sm-navy); }

        /* Links */
        a { color: var(--sm-blue); }
        a:hover { color: var(--sm-navy); }

        /* Accent border top on cards */
        .card.accent-top { border-top: 3px solid var(--sm-pink) !important; }

        /* Timeline de intervenciones */
        .timeline { position: relative; padding-left: 2rem; }
        .timeline::before {
            content: '';
            position: absolute;
            left: .65rem;
            top: .4rem;
            bottom: .4rem;
            width: 2px;
            background: #e5e9ef;
        }
        .timeline-item { position: relative; padding-bottom: 1.25rem; }
        .timeline-item:last-child { padding-bottom: 0; }
        .timeline-marker {
            position: absolute;
            left: -2rem;
            top: .15rem;
            width: 1.35rem;
            height: 1.35rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .65rem;
            color: #fff;
            flex-shrink: 0;
        }
        .timeline-content {
            background: #f8f9fb;
            border: 1px solid #eef1f5;
            border-radius: 8px;
            padding: .65rem .9rem;
        }

        /* Modal de confirmación */
        .modal-content {
            border: none;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 16px 40px rgba(23,58,94,.28);
        }
        .modal-header {
            background: linear-gradient(135deg, var(--sm-navy) 0%, var(--sm-blue) 100%);
            border-bottom: none;
            padding: 1.15rem 1.5rem;
            position: relative;
        }
        .modal-header::after {
            content: '';
            position: absolute;
            left: 0; right: 0; bottom: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--sm-pink), transparent 70%);
        }
        .modal-header .modal-title,
        .modal-header .modal-title * {
            color: #fff !important;
        }
        .modal-title { font-weight: 600; display: flex; align-items: center; gap: .6rem; }
        .confirm-icon-circle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: rgba(255,255,255,.16);
            font-size: 1rem;
            flex-shrink: 0;
        }
        .modal-header .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
            opacity: .8;
            transition: opacity .15s ease;
        }
        .modal-header .btn-close:hover { opacity: 1; }
        .modal-body {
            padding: 1.65rem 1.5rem;
            font-size: 1rem;
            color: #344054;
        }
        .modal-footer {
            border-top: 1px solid #eef1f5;
            background: #fafbfc;
            padding: .9rem 1.5rem;
        }
        .modal-footer .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: .5rem 1.3rem;
            transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
        }
        .modal-footer .btn-secondary {
            background: #fff;
            border: 1px solid #d8dde5;
            color: #45526b;
        }
        .modal-footer .btn-secondary:hover {
            background: #f3f5f8;
            border-color: #c2c9d4;
        }
        .confirm-accept-btn.is-danger {
            background: linear-gradient(135deg, #ef5c77 0%, #c0293f 100%);
            border: none;
            box-shadow: 0 3px 10px rgba(192,41,63,.35);
        }
        .confirm-accept-btn.is-danger:hover {
            background: linear-gradient(135deg, #e14d68 0%, #ad2236 100%);
            box-shadow: 0 5px 14px rgba(192,41,63,.45);
            transform: translateY(-1px);
        }
        .confirm-accept-btn.is-primary {
            background: linear-gradient(135deg, var(--sm-blue) 0%, #1e6fa8 100%);
            border: none;
            box-shadow: 0 3px 10px rgba(45,135,199,.35);
        }
        .confirm-accept-btn.is-primary:hover {
            background: linear-gradient(135deg, #1e6fa8 0%, #175a8a 100%);
            box-shadow: 0 5px 14px rgba(45,135,199,.45);
            transform: translateY(-1px);
        }
    </style>
    @stack('styles')
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <i class="bi bi-shield-check me-1"></i> SIGESM
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                       href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                @role('Profesional')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('mis-expedientes.*') ? 'active' : '' }}"
                       href="{{ route('mis-expedientes.index') }}">
                        <i class="bi bi-person-lines-fill"></i> Mis Expedientes
                    </a>
                </li>
                @else
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('expedientes.*') ? 'active' : '' }}"
                       href="{{ route('expedientes.index') }}">
                        <i class="bi bi-folder2-open"></i> Expedientes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('localidades.*') ? 'active' : '' }}"
                       href="{{ route('localidades.index') }}">
                        <i class="bi bi-geo-alt"></i> Localidades
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('tipos-expediente.*') ? 'active' : '' }}"
                       href="{{ route('tipos-expediente.index') }}">
                        <i class="bi bi-tags"></i> Tipos Expediente
                    </a>
                </li>
                @if(Auth::user()->hasRole('Coordinadora'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}"
                       href="{{ route('usuarios.index') }}">
                        <i class="bi bi-people"></i> Usuarios
                    </a>
                </li>
                @endif
                @endrole
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="bi bi-box-arrow-right me-1"></i> Cerrar sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid px-4 py-3">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')

</div>

{{-- Modal de confirmación reutilizable --}}
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <span class="confirm-icon-circle"><i class="bi bi-exclamation-triangle-fill"></i></span>
                    <span id="confirmModalTitle">Confirmar acción</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" id="confirmModalBody">
                ¿Estás seguro de realizar esta acción?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn confirm-accept-btn is-danger" id="confirmModalAccept">
                    <i class="bi bi-trash me-1"></i> Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalEl   = document.getElementById('confirmModal');
        const modal     = new bootstrap.Modal(modalEl);
        const body      = document.getElementById('confirmModalBody');
        const acceptBtn = document.getElementById('confirmModalAccept');
        let formPendiente = null;

        document.querySelectorAll('form[data-confirm]').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                formPendiente = form;
                document.getElementById('confirmModalTitle').textContent = form.dataset.confirmTitle || 'Confirmar acción';
                body.textContent = form.dataset.confirm;
                const icon    = form.dataset.confirmIcon    || 'bi-trash';
                const label   = form.dataset.confirmAccept  || 'Eliminar';
                const variant = form.dataset.confirmVariant || 'danger';
                acceptBtn.innerHTML = '<i class="bi ' + icon + ' me-1"></i> ' + label;
                acceptBtn.classList.remove('is-danger', 'is-primary');
                acceptBtn.classList.add('is-' + variant);
                modal.show();
            });
        });

        acceptBtn.addEventListener('click', function () {
            if (formPendiente) {
                modal.hide();
                formPendiente.submit();
            }
        });
    });
</script>
@stack('scripts')
</body>
</html>
