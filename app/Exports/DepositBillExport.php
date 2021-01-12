<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class DepositBillExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
{
    public function __construct($data)
    {
        $this->data = $data['data'];
        $this->row = $data['row'];
    }
    public function array(): array
    {
        return $this->data;
    }
    public function headings(): array
    {
        return ['#', 'Kode Anggota', 'Nama Anggota', 'No Rekening', 'Wilayah', 'Jenis Simpanan', 'Tagihan', 'Bayar'];
    }
    public function registerEvents(): array
    {
        $style = config('styleExport');

        return [
            AfterSheet::class => function (AfterSheet $event) use ($style) {

                $i = $this->row + 1;
                $event->sheet->getStyle('A2')->applyFromArray($style['border']);
                $event->sheet->duplicateStyle($event->sheet->getStyle('A2'), 'A2:H' . $i);

                $event->sheet->getStyle('A1:H1')->applyFromArray($style['head']);

                $event->sheet->getStyle('G2')->getAlignment()->setWrapText(true)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $event->sheet->duplicateStyle($event->sheet->getStyle('G2'), 'G2:H' . $i);
            }
        ];
    }
}