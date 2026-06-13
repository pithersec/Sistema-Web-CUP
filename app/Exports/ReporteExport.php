<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReporteExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithEvents
{
    public function __construct(
        private string $titulo,
        private array $columnas,
        private $filas
    ) {}

    public function collection()
    {
        return $this->filas->map(fn($row) => (array) $row);
    }

    public function headings(): array
    {
        return $this->columnas;
    }

    public function title(): string
    {
        return substr($this->titulo, 0, 31);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '0d3b6e']]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                foreach ($event->sheet->getDelegate()->getColumnIterator() as $column) {
                    $event->sheet->getDelegate()->getColumnDimension($column->getColumnIndex())
                        ->setAutoSize(true);
                }
            },
        ];
    }
}