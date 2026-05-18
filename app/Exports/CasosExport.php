<?php

namespace App\Exports;

use App\Models\Caso;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CasosExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    public function query()
    {
        return Caso::query()
            ->with(['tipoExpediente', 'localidad'])
            ->orderBy('fecha_recepcion', 'desc');
    }

    public function headings(): array
    {
        return [
            'ID', 'Fecha Recepción', 'Nro Legajo', 'Nro Expediente', 'Tipo',
            'Apellido y Nombre', 'DNI', 'Localidad', 'Barrio', 'Teléfono',
            'Denunciado', 'Acepta Atención', 'Serv. Legal', 'Serv. Psicológico',
            'Serv. Social', 'Archivado', 'Fecha Devolución', 'Resumen',
        ];
    }

    public function map($caso): array
    {
        return [
            $caso->id,
            $caso->fecha_recepcion->format('d/m/Y'),
            $caso->nro_legajo,
            $caso->nro_expediente,
            $caso->tipoExpediente->nombre,
            $caso->apellido_nombre,
            $caso->dni,
            $caso->localidad->nombre,
            $caso->barrio ?? '',
            $caso->telefono ?? '',
            $caso->denunciado ?? '',
            $caso->acepta_atencion ? 'Sí' : 'No',
            $caso->servicio_legal ? 'Sí' : 'No',
            $caso->servicio_psicologico ? 'Sí' : 'No',
            $caso->servicio_social ? 'Sí' : 'No',
            $caso->archivado ? 'Sí' : 'No',
            $caso->fecha_devolucion?->format('d/m/Y') ?? '',
            $caso->resumen ?? '',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
