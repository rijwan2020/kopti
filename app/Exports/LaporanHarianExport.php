<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class LaporanHarianExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
{
    public function __construct($data, $date)
    {
        $this->data = $data;
        $this->date = $date;
    }
    public function array(): array
    {
        return $this->data;
    }
    public function headings(): array
    {
        return [
            [config('koperasi.nama')],
            ["Laporan Harian"],
            ['Tanggal ' . date('d-m-Y', strtotime($this->date))],
            ['#', 'Bidang', 'Saldo Lalu (Rp)', 'Penerimaan (Rp)', 'Pengeluaran (Rp)', 'Saldo (Rp)'],
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

                $i = count($this->data) + 4;
                $event->sheet->getStyle('A4')->applyFromArray($style['border']);
                $event->sheet->duplicateStyle($event->sheet->getStyle('A4'), 'A4:F' . $i);

                $event->sheet->getStyle('A4:F4')->applyFromArray($style['head']);

                $event->sheet->getStyle('A5')->getAlignment()->setWrapText(true)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->duplicateStyle($event->sheet->getStyle('A5'), 'A5:A' . $i);

                $event->sheet->getStyle('C5')->getAlignment()->setWrapText(true)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $event->sheet->duplicateStyle($event->sheet->getStyle('C5'), 'C5:F' . $i);
                $i++;
            }
        ];
    }
}