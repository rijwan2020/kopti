<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class DepositExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
{
    public function __construct($data)
    {
        $this->data = $data['data'];
        $this->row = $data['total_row'];
        $this->saldo = $data['saldo'];
    }
    public function array(): array
    {
        return $this->data;
    }
    public function headings(): array
    {
        return [
            [config('koperasi.nama')],
            ['Data Simpanan'],
            ['#', 'Kode Anggota', 'Nama Anggota', 'No Rekening', 'Wilayah', 'Jenis Simpanan', 'Tanggal Registrasi', 'Transaksi Terkahir', 'Saldo']
        ];
    }
    public function registerEvents(): array
    {
        $style = config('styleExport');

        return [
            AfterSheet::class => function (AfterSheet $event) use ($style) {
                $event->sheet->mergeCells("A1:I1");
                $event->sheet->getStyle('A1')->applyFromArray($style['title']);
                $event->sheet->mergeCells("A2:I2");
                $event->sheet->getStyle('A2')->applyFromArray($style['subtitle']);

                $i = $this->row + 4;
                $event->sheet->getStyle('A4')->applyFromArray($style['border']);
                $event->sheet->duplicateStyle($event->sheet->getStyle('A4'), 'A3:I' . $i);

                $event->sheet->getStyle('A3:I3')->applyFromArray($style['head']);

                $event->sheet->getStyle('I4')->getAlignment()->setWrapText(true)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $event->sheet->duplicateStyle($event->sheet->getStyle('I4'), 'I4:I' . $i);

                $event->sheet->setCellValue("A" . $i, 'Jumlah');
                $event->sheet->setCellValue("I" . $i, 'Rp' . number_format($this->saldo, 2, ',', '.'));
                $event->sheet->mergeCells("A" . $i . ":H" . $i);
                $event->sheet->getStyle("A" . $i . ":I" . $i)->applyFromArray($style['footer']);
            }
        ];
    }
}