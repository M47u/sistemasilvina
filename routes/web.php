<?php

use App\Http\Controllers\CasoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocalidadController;
use App\Http\Controllers\TipoExpedienteController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/casos/export/excel', [CasoController::class, 'exportExcel'])->name('casos.export.excel');
    Route::get('/casos/export/pdf',   [CasoController::class, 'exportPdf'])->name('casos.export.pdf');
    Route::resource('casos', CasoController::class);

    Route::resource('localidades', LocalidadController::class)
        ->parameters(['localidades' => 'localidad'])
        ->only(['index', 'store', 'destroy']);

    Route::resource('tipos-expediente', TipoExpedienteController::class)
        ->parameters(['tipos-expediente' => 'tipoExpediente'])
        ->only(['index', 'store', 'destroy']);
});

require __DIR__.'/auth.php';
