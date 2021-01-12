<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class ShuAnggotaExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
{
    public function __construct($data)
    {
        $this->data = $data['data'];
        $this->row = $data['total_row'];
    }

    public function array(): array
    {
        return $this->data;
    }
    public function headings(): array
    {
        return [
            ['Data Sisa Hasil Usaha Anggota'],
            ['#', 'Kode Anggota', 'Nama Anggota', 'Wilayah', 'Status', 'SHU Simpanan (Rp)', 'SHU Toko (Rp)', 'Jumlah (Rp)']
        ];
    }
    public function registerEvents(): array
    {
        $style = config('styleExport');

        return [
            AfterSheet::class => function (AfterSheet $event) use ($style) {
                $event->sheet->getStyle('A1')->applyFromArray($style['border']);
                $i = $this->row + 2;
                $event->sheet->duplicateStyle($event->sheet->getStyle('A1'), 'A1:H' . $i);
                $event->sheet->mergeCells("A1:H1");
                $event->sheet->getStyle('A1')->applyFromArray($style['periode']);
                $event->sheet->getStyle('A2:H2')->applyFromArray($style['head']);

                // angka dikanan
                $event->sheet->getStyle('F3')->getAlignment()->setWrapText(true)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $event->sheet->duplicateStyle($event->sheet->getStyle('F3'), 'F3:H' . $i);
            }
        ];
    }
}