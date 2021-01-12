<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class ItemUploadFormatExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
{
    public function __construct($data)
    {
        // dd($data);
        $this->data = $data['data'];
        $this->header = $data['header'];
    }

    public function array(): array
    {
        return $this->data;
    }
    public function headings(): array
    {
        return $this->header;
    }
    public function registerEvents(): array
    {
        $style = config('styleExport');
        $column = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
        $col = count($this->header);

        return [
            AfterSheet::class => function (AfterSheet $event) use ($style, $column, $col) {
                $event->sheet->getStyle('A1')->applyFromArray($style['border']);
                $i = count($this->data) + 1;
                $event->sheet->duplicateStyle($event->sheet->getStyle('A1'), 'A1:' . $column[$col - 1] . $i);
                $event->sheet->getStyle("A1:" . $column[$col - 1] . "1")->applyFromArray($style['head']);
            }
        ];
    }
}