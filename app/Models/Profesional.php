<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Profesional extends Model
{
    protected $fillable = [
        'user_id',
        'apellido',
        'nombre',
        'area',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function casos(): BelongsToMany
    {
        return $this->belongsToMany(Caso::class, 'caso_profesional')
            ->withPivot(['area', 'fecha_asignacion', 'fecha_fin', 'asignado_por'])
            ->withTimestamps();
    }

    public function casosActivos(): BelongsToMany
    {
        return $this->casos()->wherePivotNull('fecha_fin');
    }

    public function getNombreCompletoAttribute(): string
    {
        return $this->apellido . ', ' . $this->nombre;
    }
}
