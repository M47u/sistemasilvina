<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoExpediente extends Model
{
    protected $table = 'tipos_expediente';

    protected $fillable = ['nombre'];

    public function casos(): HasMany
    {
        return $this->hasMany(Caso::class);
    }
}
