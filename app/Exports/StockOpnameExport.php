<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class StockOpnameExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
{
    public function __construct($data)
    {
        $this->data = $data['data'];
    }
    public function array(): array
    {
        return $this->data;
    }
    public function headings(): array
    {
        return [
            'No',
            'Id',
            'Kode Barang',
            'Nama Barang',
            'Tanggal Masuk',
            'Gudang',
            'Harga Beli',
            'Qty',
            'Total Persediaan',
            'Qty Susut',
            'Total Penyusutan'
        ];
    }
    public function registerEvents(): array
    {
        $style = config('styleExport');

        return [
            AfterSheet::class => function (AfterSheet $event) use ($style) {
                $i = count($this->data) + 1;
                $event->sheet->getStyle('A1')->applyFromArray($style['border']);
                $event->sheet->duplicateStyle($event->sheet->getStyle('A1'), 'A1:K' . $i);

                $event->sheet->getStyle('A1:K1')->applyFromArray($style['head']);

                $event->sheet->getStyle('G2')->getAlignment()->setWrapText(true)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $event->sheet->duplicateStyle($event->sheet->getStyle('G2'), 'G2:G' . $i);
                $event->sheet->duplicateStyle($event->sheet->getStyle('G2'), 'I2:I' . $i);
                $event->sheet->duplicateStyle($event->sheet->getStyle('G2'), 'K2:K' . $i);
            }
        ];
    }
}