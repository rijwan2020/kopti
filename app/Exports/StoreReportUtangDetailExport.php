<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class StoreReportUtangDetailExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
{
    public function __construct($data)
    {
        $this->data = $data;
    }
    public function array(): array
    {
        return $this->data['data'];
    }
    public function headings(): array
    {
        return [
            [config('koperasi.nama')],
            ['Buku Besar Pembantu Utang'],
            [$this->data['periode']],
            ['Nama Suplier : '.$this->data['suplier']],
            $this->data['header'],
            ['Saldo Awal', '', '', '', '', '', number_format($this->data['saldo_awal'], 2, ',', '.')]
        ];
    }
    public function registerEvents(): array
    {
        $style = config('styleExport');
        return [
            AfterSheet::class => function (AfterSheet $event) use ($style) {
                $event->sheet->mergeCells("A1:G1");
                $event->sheet->getStyle('A1')->applyFromArray($style['title']);
                $event->sheet->mergeCells("A2:G2");
                $event->sheet->getStyle('A2')->applyFromArray($style['subtitle']);
                $event->sheet->mergeCells("A3:G3");
                $event->sheet->getStyle('A3')->applyFromArray($style['periode']);
                $event->sheet->getStyle('A1:G3')->applyFromArray($style['border']);

                $i = count($this->data['data']) + 7;
                $event->sheet->getStyle('A4')->applyFromArray($style['border']);
                $event->sheet->duplicateStyle($event->sheet->getStyle('A4'), 'A4:G' . $i);
                
                $event->sheet->mergeCells("A4:G4");
                $event->sheet->getStyle('A5:G5')->applyFromArray($style['head']);
                $event->sheet->mergeCells("A6:F6");
                $event->sheet->getStyle('A6:G6')->applyFromArray($style['footer']);

                $event->sheet->getStyle('E7')->getAlignment()->setWrapText(true)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $event->sheet->duplicateStyle($event->sheet->getStyle('E7'), 'E7:G' . $i);

                $event->sheet->setCellValue("A" . $i, 'Saldo Akhir');
                $event->sheet->setCellValue("G" . $i, number_format($this->data['saldo_akhir'], 2, ',', '.'));
                $event->sheet->mergeCells("A" . $i . ":F" . $i);
                $event->sheet->getStyle('A' . $i . ':G' . $i)->applyFromArray($style['footer']);
            }
        ];
    }
}