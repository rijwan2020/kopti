<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class StoreSaleRegionExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
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
            ['Rekapitulasi Penjualan Wilayah'],
            [$this->data['periode']],
            $this->data['header'],
        ];
    }
    public function registerEvents(): array
    {
        $style = config('styleExport');
        return [
            AfterSheet::class => function (AfterSheet $event) use ($style) {
                $event->sheet->mergeCells("A1:D1");
                $event->sheet->getStyle('A1')->applyFromArray($style['title']);
                $event->sheet->mergeCells("A2:D2");
                $event->sheet->getStyle('A2')->applyFromArray($style['subtitle']);
                $event->sheet->mergeCells("A3:D3");
                $event->sheet->getStyle('A3')->applyFromArray($style['periode']);
                $event->sheet->getStyle('A1:D3')->applyFromArray($style['border']);

                $i = count($this->data['data']) + 5;
                $event->sheet->getStyle('A4')->applyFromArray($style['border']);
                $event->sheet->duplicateStyle($event->sheet->getStyle('A4'), 'A4:D' . $i);
                $event->sheet->getStyle('A4:D4')->applyFromArray($style['head']);

                $event->sheet->getStyle('C5')->getAlignment()->setWrapText(true)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $event->sheet->duplicateStyle($event->sheet->getStyle('C5'), 'C5:D' . $i);

                $event->sheet->setCellValue("A" . $i, 'Jumlah');
                $event->sheet->setCellValue("C" . $i, $this->data['total_kebutuhan'] >= 0 ? number_format($this->data['total_kebutuhan'], 2, ',', '.') : '(' . number_format($this->data['total_kebutuhan'] * -1, 2, ',', '.') . ')');
                $event->sheet->setCellValue("D" . $i, $this->data['total_penjualan'] >= 0 ? number_format($this->data['total_penjualan'], 2, ',', '.') : '(' . number_format($this->data['total_penjualan'] * -1, 2, ',', '.') . ')');
                $event->sheet->mergeCells("A" . $i . ":B" . $i);
                $event->sheet->getStyle('A' . $i . ':D' . $i)->applyFromArray($style['footer']);
            }
        ];
    }
}