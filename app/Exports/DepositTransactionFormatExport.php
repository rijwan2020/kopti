<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class DepositTransactionFormatExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
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
            'Kode Anggota',
            'Nama Anggota',
            'Wilayah',
            'No Rekening',
            'Jenis Transaksi',
            'No Ref',
            'Keterangan',
            'Jumlah'
        ];
    }
    public function registerEvents(): array
    {
        $style = config('styleExport');

        return [
            AfterSheet::class => function (AfterSheet $event) use ($style) {
                $i = count($this->data) + 1;
                $event->sheet->getStyle('A1')->applyFromArray($style['border']);
                $event->sheet->duplicateStyle($event->sheet->getStyle('A1'), 'A1:I' . $i);

                $event->sheet->getStyle('A1:I1')->applyFromArray($style['head']);
                $event->sheet->getComment('F1')->getText()->createTextRun("Input dengan kode transaksi \n1 = Setoran \n2 = Penarikan \n3 = Jasa \n4 = Administrasi \n5 = Penyesuaian Setoran \n6 = Penyesuaian Penarikan");
            }
        ];
    }
}