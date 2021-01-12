<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class DepositReportMemberDetailExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
{
    public function __construct($data)
    {
        $this->data = $data['data'];
        $this->row = $data['total_row'];
        $this->header = $data['header'];
        $this->periode = $data['periode'];
        $this->title = $data['title'];
    }
    public function array(): array
    {
        return $this->data;
    }
    public function headings(): array
    {
        return [
            [config('koperasi.nama')],
            [$this->title],
            [$this->periode],
            $this->header
        ];
    }
    public function registerEvents(): array
    {
        $style = config('styleExport');

        return [
            AfterSheet::class => function (AfterSheet $event) use ($style) {
                $event->sheet->mergeCells("A1:H1");
                $event->sheet->getStyle('A1')->applyFromArray($style['title']);
                $event->sheet->mergeCells("A2:H2");
                $event->sheet->getStyle('A2')->applyFromArray($style['subtitle']);
                $event->sheet->mergeCells("A3:H3");
                $event->sheet->getStyle('A3')->applyFromArray($style['periode']);

                $i = $this->row + 4;
                $event->sheet->getStyle('A5')->applyFromArray($style['border']);
                $event->sheet->duplicateStyle($event->sheet->getStyle('A5'), 'A4:H' . $i);

                $event->sheet->getStyle('A4:H4')->applyFromArray($style['head']);

                $event->sheet->getStyle('D5')->getAlignment()->setWrapText(true)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $event->sheet->duplicateStyle($event->sheet->getStyle('D5'), 'D5:H' . $i);

                $event->sheet->mergeCells("A" . $i . ":C" . $i);
                $event->sheet->getStyle("A" . $i . ":H" . $i)->applyFromArray($style['footer']);
            }
        ];
    }
}