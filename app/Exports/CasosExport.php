<?php

namespace App\Exports;

use App\Models\Caso;
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
        return Caso::query()
            ->with(['tipoExpediente', 'localidad'])
            ->orderBy('fecha_recepcion', 'desc');
    }

    public function headings(): array
    {
        return [
            'Fecha', 'Legajo', 'Apellido y Nombre', 'DNI',
            'Localidad', 'Tipo', 'Servicio', 'Estado',
        ];
    }

    public function map($caso): array
    {
        $servicios = array_filter([
            $caso->servicio_legal       ? 'Legal'       : null,
            $caso->servicio_psicologico ? 'Psicológico' : null,
            $caso->servicio_social      ? 'Social'       : null,
        ]);

        return [
            $caso->fecha_recepcion->format('d/m/Y'),
            $caso->nro_legajo,
            $caso->apellido_nombre,
            $caso->dni,
            $caso->localidad->nombre,
            $caso->tipoExpediente->nombre,
            implode(', ', $servicios),
            $caso->archivado ? 'Archivado' : 'Activo',
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
