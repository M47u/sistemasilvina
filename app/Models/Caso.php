<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Caso extends Model
{
    protected $fillable = [
        'persona_id',
        'fecha_recepcion',
        'nro_legajo',
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
        // Campos legacy — se mantienen durante la migración del Excel
        'apellido_nombre',
        'dni',
        'localidad_id',
        'barrio',
        'telefono',
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
        return $this->belongsToMany(Profesional::class, 'caso_profesional')
            ->withPivot(['area', 'fecha_asignacion', 'fecha_fin', 'asignado_por'])
            ->withTimestamps();
    }

    public function profesionalesActivos(): BelongsToMany
    {
        return $this->profesionales()->wherePivotNull('fecha_fin');
    }
}
