<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class DepositTransactionExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
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
            ['Data Transaksi Simpanan'],
            ['#', 'No Rekening', 'Kode Anggota', 'Nama Anggota', 'Wilayah', 'Jenis Simpanan', 'No Ref', 'Keterangan', 'Tanggal Transaksi', 'Jenis Transaksi', 'Kredit (Rp)', 'Debit (Rp)', 'Saldo']
        ];
    }
    public function registerEvents(): array
    {
        $style = config('styleExport');

        return [
            AfterSheet::class => function (AfterSheet $event) use ($style) {
                $event->sheet->mergeCells("A1:M1");
                $event->sheet->getStyle('A1')->applyFromArray($style['title']);
                $event->sheet->mergeCells("A2:M2");
                $event->sheet->getStyle('A2')->applyFromArray($style['subtitle']);

                $i = count($this->data['data']) + 3;
                $event->sheet->getStyle('A4')->applyFromArray($style['border']);
                $event->sheet->duplicateStyle($event->sheet->getStyle('A4'), 'A3:M' . $i);

                $event->sheet->mergeCells("A4:L4");
                $event->sheet->getStyle("A4:M4")->applyFromArray($style['footer']);

                $event->sheet->getStyle('A3:M3')->applyFromArray($style['head']);

                $event->sheet->getStyle('K5')->getAlignment()->setWrapText(true)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $event->sheet->duplicateStyle($event->sheet->getStyle('K5'), 'K5:M' . $i);

                $event->sheet->mergeCells("A" . $i . ":J" . $i);
                $event->sheet->getStyle("A" . $i . ":M" . $i)->applyFromArray($style['footer']);
            }
        ];
    }
}