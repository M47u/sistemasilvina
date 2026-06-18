<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Persona extends Model
{
    protected $fillable = [
        'nro_legajo',
        'apellido_nombre',
        'dni',
        'localidad_id',
        'barrio',
        'telefono',
        'direccion',
    ];

    public function localidad(): BelongsTo
    {
        return $this->belongsTo(Localidad::class);
    }

    public function expedientes(): HasMany
    {
        return $this->hasMany(Expediente::class);
    }
}
