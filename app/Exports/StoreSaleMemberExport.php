<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class StoreSaleMemberExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize


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
                $event->sheet->mergeCells("A1:F1");
                $event->sheet->getStyle('A1')->applyFromArray($style['title']);
                $event->sheet->mergeCells("A2:F2");
                $event->sheet->getStyle('A2')->applyFromArray($style['subtitle']);
                $event->sheet->mergeCells("A3:F3");
                $event->sheet->getStyle('A3')->applyFromArray($style['periode']);
                $event->sheet->getStyle('A1:F3')->applyFromArray($style['border']);

                $i = count($this->data['data']) + 5;
                $event->sheet->getStyle('A4')->applyFromArray($style['border']);
                $event->sheet->duplicateStyle($event->sheet->getStyle('A4'), 'A4:F' . $i);
                $event->sheet->getStyle('A4:F4')->applyFromArray($style['head']);

                $event->sheet->getStyle('E5')->getAlignment()->setWrapText(true)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $event->sheet->duplicateStyle($event->sheet->getStyle('E5'), 'E5:F' . $i);

                $event->sheet->setCellValue("A" . $i, 'Jumlah');
                $event->sheet->setCellValue("E" . $i, $this->data['total_kebutuhan'] >= 0 ? number_format($this->data['total_kebutuhan'], 2, ',', '.') : '(' . number_format($this->data['total_kebutuhan'] * -1, 2, ',', '.') . ')');
                $event->sheet->setCellValue("F" . $i, $this->data['total_penjualan'] >= 0 ? number_format($this->data['total_penjualan'], 2, ',', '.') : '(' . number_format($this->data['total_penjualan'] * -1, 2, ',', '.') . ')');
                $event->sheet->mergeCells("A" . $i . ":D" . $i);
                $event->sheet->getStyle('A' . $i . ':F' . $i)->applyFromArray($style['footer']);
            }
        ];
    }
}