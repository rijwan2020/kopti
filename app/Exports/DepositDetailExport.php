<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class DepositDetailExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
{
    public function __construct($data)
    {
        $this->data = $data['data'];
        $this->row = $data['total_row'];
        $this->header = $data['header'];
    }
    public function array(): array
    {
        return $this->data;
    }
    public function headings(): array
    {
        return [
            [config('koperasi.nama')],
            ['Data Transaksi Simpanan'],
            ['No Rekening : ' . $this->header['account_number'], '', '', '', '', 'Saldo : Rp' . number_format($this->header['balance'], 2, ',', '.')],
            ['Kode Anggota : ' . $this->header['code']],
            ['Nama Anggota : ' . $this->header['name']],
            ['Jenis Simpanan : ' . $this->header['type']],
            ['Wilayah : ' . $this->header['region']],
            ['#', 'No Ref', 'Keterangan', 'Tanggal Transaksi', 'Jenis Transaksi', 'Kredit (Rp)', 'Debit (Rp)']
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
                $event->sheet->mergeCells("A3:E3");
                $event->sheet->mergeCells("A4:E4");
                $event->sheet->mergeCells("A5:E5");
                $event->sheet->mergeCells("A6:E6");
                $event->sheet->mergeCells("A7:E7");
                $event->sheet->mergeCells("F3:G7");
                $event->sheet->getStyle('A3')->applyFromArray($style['border']);
                $event->sheet->duplicateStyle($event->sheet->getStyle('A3'), 'A3:G7');
                $event->sheet->getStyle('G3')->getAlignment()->setWrapText(true)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $event->sheet->getStyle('F3')->applyFromArray($style['periode']);


                $i = $this->row + 9;
                $event->sheet->getStyle('A9')->applyFromArray($style['border']);
                $event->sheet->duplicateStyle($event->sheet->getStyle('A9'), 'A9:G' . $i);

                $event->sheet->getStyle('A8:G8')->applyFromArray($style['head']);

                $event->sheet->getStyle('F9')->getAlignment()->setWrapText(true)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $event->sheet->duplicateStyle($event->sheet->getStyle('F9'), 'F9:G' . $i);

                $event->sheet->setCellValue("A" . $i, 'Jumlah');
                $event->sheet->setCellValue("F" . $i, number_format($this->header['total_kredit'], 2, ',', '.'));
                $event->sheet->setCellValue("G" . $i, number_format($this->header['total_debit'], 2, ',', '.'));
                $event->sheet->mergeCells("A" . $i . ":E" . $i);
                $event->sheet->getStyle("A" . $i . ":G" . $i)->applyFromArray($style['footer']);
            }
        ];
    }
}