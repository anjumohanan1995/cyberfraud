<?php

namespace App\exports;

// use App\Models\YourModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Font;

class SampleExport implements FromCollection, WithHeadings
{
    protected $firstRow;
    protected $additionalRows;

    public function __construct(array $firstRow , array $additionalRows)
    {
        $this->firstRow = $firstRow;
        $this->additionalRows = $additionalRows;
    }

    public function collection()
    {
        return new Collection($this->additionalRows);
    }

    public function headings(): array
    {
        return $this->firstRow;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Apply bold font style to the first row
                $event->sheet->getStyle('A1:J1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                ]);

                // Apply bold font style to additional rows
                $lastRow = count($this->additionalRows) + 1; // Calculate the last row
                $event->sheet->getStyle("A2:C{$lastRow}")->applyFromArray([
                    'font' => [
                        'bold' => false,
                    ],
                ]);
            },
        ];
    }
}






?>