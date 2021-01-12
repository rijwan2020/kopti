<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class StoreSaleItemStockExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
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
            [$this->data['title']],
            [$this->data['periode']],
            $this->data['header'],
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

                $i = count($this->data['data']) + 5;
                $event->sheet->getStyle('A4')->applyFromArray($style['border']);
                $event->sheet->duplicateStyle($event->sheet->getStyle('A4'), 'A4:G' . $i);
                $event->sheet->getStyle('A4:G4')->applyFromArray($style['head']);

                $event->sheet->getStyle('D5')->getAlignment()->setWrapText(true)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $event->sheet->duplicateStyle($event->sheet->getStyle('D5'), 'D5:G' . $i);

                $event->sheet->setCellValue("A" . $i, 'Jumlah');
                $event->sheet->setCellValue("D" . $i, $this->data['total_saldo_awal'] >= 0 ? number_format($this->data['total_saldo_awal'], 2, ',', '.') : '(' . number_format($this->data['total_saldo_awal'] * -1, 2, ',', '.') . ')');
                $event->sheet->setCellValue("E" . $i, number_format($this->data['total_masuk'], 2, ',', '.'));
                $event->sheet->setCellValue("F" . $i, number_format($this->data['total_keluar'], 2, ',', '.'));
                $event->sheet->setCellValue("G" . $i, $this->data['total_persediaan'] >= 0 ? number_format($this->data['total_persediaan'], 2, ',', '.') : '(' . number_format($this->data['total_persediaan'] * -1, 2, ',', '.') . ')');
                $event->sheet->mergeCells("A" . $i . ":C" . $i);
                $event->sheet->getStyle('A' . $i . ':G' . $i)->applyFromArray($style['footer']);
            }
        ];
    }
}