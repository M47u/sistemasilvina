<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Caso extends Model
{
    protected $fillable = [
        'fecha_recepcion',
        'nro_legajo',
        'nro_expediente',
        'tipo_expediente_id',
        'apellido_nombre',
        'dni',
        'localidad_id',
        'barrio',
        'telefono',
        'denunciado',
        'resumen',
        'acepta_atencion',
        'servicio_legal',
        'servicio_psicologico',
        'servicio_social',
        'archivado',
        'observaciones',
        'fecha_devolucion',
    ];

    protected $casts = [
        'fecha_recepcion'    => 'date',
        'fecha_devolucion'   => 'date',
        'acepta_atencion'    => 'boolean',
        'servicio_legal'     => 'boolean',
        'servicio_psicologico' => 'boolean',
        'servicio_social'    => 'boolean',
        'archivado'          => 'boolean',
    ];

    public function tipoExpediente(): BelongsTo
    {
        return $this->belongsTo(TipoExpediente::class);
    }

    public function localidad(): BelongsTo
    {
        return $this->belongsTo(Localidad::class);
    }
}
