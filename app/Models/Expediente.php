<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Expediente extends Model
{
    protected $table = 'expedientes';

    protected $fillable = [
        'persona_id',
        'fecha_recepcion',
        'nro_expediente',
        'tipo_expediente_id',
        'via_acceso',
        'urgente',
        'denunciado',
        'resumen',
        'acepta_atencion',
        'servicio_legal',
        'servicio_psicologico',
        'servicio_social',
        'archivado',
        'observaciones',
        'fecha_devolucion',
        'creado_por',
        // Legacy read-only columns — display only, never write new data here
    ];

    protected $casts = [
        'fecha_recepcion'      => 'date',
        'fecha_devolucion'     => 'date',
        'urgente'              => 'boolean',
        'acepta_atencion'      => 'boolean',
        'servicio_legal'       => 'boolean',
        'servicio_psicologico' => 'boolean',
        'servicio_social'      => 'boolean',
        'archivado'            => 'boolean',
    ];

    // nro_legajo lives on Persona — accessor keeps all views working transparently
    public function getNroLegajoAttribute(): ?string
    {
        return $this->persona?->nro_legajo;
    }

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }

    public function tipoExpediente(): BelongsTo
    {
        return $this->belongsTo(TipoExpediente::class);
    }

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function profesionales(): BelongsToMany
    {
        return $this->belongsToMany(Profesional::class, 'expediente_profesional')
            ->withPivot(['area', 'fecha_asignacion', 'fecha_fin', 'asignado_por'])
            ->withTimestamps();
    }

    public function profesionalesActivos(): BelongsToMany
    {
        return $this->profesionales()->wherePivotNull('fecha_fin');
    }

    public function intervenciones(): HasMany
    {
        return $this->hasMany(Intervencion::class)->orderBy('fecha', 'desc')->orderBy('id', 'desc');
    }
}
