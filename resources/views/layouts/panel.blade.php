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
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('casos.*') ? 'active' : '' }}"
                       href="{{ route('casos.index') }}">
                        <i class="bi bi-folder2-open"></i> Casos
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
