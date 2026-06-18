<?php

use App\Http\Controllers\ExpedienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IntervencionController;
use App\Http\Controllers\MisExpedientesController;
use App\Http\Controllers\LocalidadController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\TipoExpedienteController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/mis-expedientes', [MisExpedientesController::class, 'index'])->name('mis-expedientes.index');

    Route::get('/personas/{persona}/expedientes', [PersonaController::class, 'expedientes'])->name('personas.expedientes');

    // Accessible to all authenticated roles (including Profesional)
    Route::get('/expedientes/{expediente}',     [ExpedienteController::class,   'show'])->name('expedientes.show');
    Route::get('/expedientes/{expediente}/pdf', [ExpedienteController::class,   'pdfExpediente'])->name('expedientes.pdf.expediente');
    Route::post('/expedientes/{expediente}/intervenciones',                      [IntervencionController::class, 'store'])->name('expedientes.intervenciones.store');
    Route::delete('/expedientes/{expediente}/intervenciones/{intervencion}',     [IntervencionController::class, 'destroy'])->name('expedientes.intervenciones.destroy');

    // Coordinadora / Administrativo only
    Route::middleware('role:Coordinadora|Administrativo')->group(function () {
        Route::post('/expedientes/buscar-persona',           [ExpedienteController::class, 'buscarPersona'])->name('expedientes.buscar-persona');
        Route::get('/expedientes/export/excel',              [ExpedienteController::class, 'exportExcel'])->name('expedientes.export.excel');
        Route::get('/expedientes/export/pdf',                [ExpedienteController::class, 'exportPdf'])->name('expedientes.export.pdf');
        Route::post('/expedientes/{expediente}/profesionales', [ExpedienteController::class, 'asignarProfesional'])->name('expedientes.profesionales.asignar');
        Route::resource('expedientes', ExpedienteController::class)->except(['show']);

        Route::resource('localidades', LocalidadController::class)
            ->parameters(['localidades' => 'localidad'])
            ->only(['index', 'store', 'destroy']);

        Route::resource('tipos-expediente', TipoExpedienteController::class)
            ->parameters(['tipos-expediente' => 'tipoExpediente'])
            ->only(['index', 'store', 'destroy']);

        Route::resource('usuarios', UserController::class)
            ->parameters(['usuarios' => 'usuario'])
            ->except(['show']);
    });
});

require __DIR__.'/auth.php';
