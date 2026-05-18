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
        body { background: linear-gradient(135deg, #1a252f 0%, #2980b9 100%); min-height: 100vh; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center py-5">
    <div style="width: 100%; max-width: 420px;" class="px-3">
        <div class="text-center mb-4">
            <i class="bi bi-shield-check" style="font-size: 3rem; color: #fff;"></i>
            <h4 class="text-white mt-2 fw-bold">Sistema Silvina</h4>
            <p class="text-white-50 mb-0">Sistema de Gestión de Casos</p>
        </div>
        <div class="card shadow border-0 rounded-3">
            <div class="card-body p-4">
                {{ $slot }}
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
