<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - Acceso</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(145deg, #173a5e 0%, #2d87c7 65%, #e8a4c8 100%);
            min-height: 100vh;
        }
        .login-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 8px 32px rgba(23,58,94,.30);
            border-top: 4px solid #e8a4c8;
        }
        .btn-primary {
            background-color: #2d87c7;
            border-color: #2d87c7;
        }
        .btn-primary:hover {
            background-color: #1e6fa8;
            border-color: #1e6fa8;
        }
        .form-control:focus {
            border-color: #2d87c7;
            box-shadow: 0 0 0 .2rem rgba(45,135,199,.25);
        }
        .logo-circle {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2d87c7, #173a5e);
            border: 3px solid #e8a4c8;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center py-5">
    <div style="width: 100%; max-width: 420px;" class="px-3">
        <div class="text-center mb-4">
            <div class="logo-circle mx-auto mb-3">
                <i class="bi bi-shield-check" style="font-size: 2rem; color: #fff;"></i>
            </div>
            <h4 class="text-white mt-2 fw-bold">SIGESM</h4>
            <p class="mb-0" style="color: rgba(255,255,255,.75); font-size:.9rem;">Sistema Integral de Gestión de Expedientes de la Secretaría de La Mujer</p>
        </div>
        <div class="card login-card">
            <div class="card-body p-4">
                {{ $slot }}
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
