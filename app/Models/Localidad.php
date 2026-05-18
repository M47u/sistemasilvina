<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Localidad extends Model
{
    protected $table = 'localidades';

    protected $fillable = ['nombre'];

    public function casos(): HasMany
    {
        return $this->hasMany(Caso::class);
    }
}
