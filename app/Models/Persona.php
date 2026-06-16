<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Persona extends Model
{
    protected $fillable = [
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

    public function casos(): HasMany
    {
        return $this->hasMany(Caso::class);
    }
}
