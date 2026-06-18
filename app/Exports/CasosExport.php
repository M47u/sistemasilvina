<?php

namespace App\Exports;

use App\Models\Expediente;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class CasosExport implements FromQuery, WithHeadings, WithMapping, WithEvents
{
    public function query()
    {
        return Expediente::query()
            ->with(['persona.localidad', 'tipoExpediente'])
            ->orderBy('fecha_recepcion', 'desc');
    }

    public function headings(): array
    {
        return [
            'Fecha', 'Legajo', 'Apellido y Nombre', 'DNI',
            'Localidad', 'Tipo', 'Servicio', 'Estado',
        ];
    }

    public function map($expediente): array
    {
        $servicios = array_filter([
            $expediente->servicio_legal       ? 'Legal'       : null,
            $expediente->servicio_psicologico ? 'Psicológico' : null,
            $expediente->servicio_social      ? 'Social'      : null,
        ]);

        return [
            $expediente->fecha_recepcion?->format('d/m/Y') ?? '—',
            $expediente->nro_legajo ?? '—',
            $expediente->persona?->apellido_nombre ?? $expediente->apellido_nombre ?? '—',
            $expediente->persona?->dni ?? $expediente->dni ?? '—',
            $expediente->persona?->localidad?->nombre ?? '—',
            $expediente->tipoExpediente->nombre,
            implode(', ', $servicios),
            $expediente->archivado ? 'Archivado' : 'Activo',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Insert 3 rows at top for the membrete image
                $sheet->insertNewRowBefore(1, 3);
                $sheet->mergeCells('A1:H3');
                $sheet->getRowDimension(1)->setRowHeight(70);

                $imagePath = public_path('img/membrete.png');
                if (file_exists($imagePath)) {
                    $drawing = new Drawing();
                    $drawing->setName('Membrete');
                    $drawing->setPath($imagePath);
                    $drawing->setHeight(65);
                    $drawing->setOffsetX(4);
                    $drawing->setOffsetY(4);
                    $drawing->setCoordinates('A1');
                    $drawing->setWorksheet($sheet);
                }

                // Style the header row (now row 4 after the insert)
                $sheet->getStyle('A4:H4')->applyFromArray([
                    'font' => [
                        'bold'  => true,
                        'color' => ['argb' => 'FFFFFFFF'],
                    ],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF173a5e'],
                    ],
                ]);
            },
        ];
    }
}
