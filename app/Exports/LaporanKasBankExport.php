<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class LaporanKasBankExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
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
            ["Laporan Pemasukan/Pengeluaran Kas & Bank"],
            ['Tanggal ' . date('d-m-Y', strtotime($this->date))],
            ['#', 'Tanggal Transaksi', 'No Ref', 'Kode Akun', 'Nama Akun', 'Keterangan', 'Debit (Rp)', 'Kredit (Rp)'],
        ];
    }
    public function registerEvents(): array
    {
        $style = config('styleExport');

        return [
            AfterSheet::class => function (AfterSheet $event) use ($style) {
                $event->sheet->mergeCells("A1:H1");
                $event->sheet->getStyle('A1')->applyFromArray($style['title']);
                $event->sheet->mergeCells("A2:H2");
                $event->sheet->getStyle('A2')->applyFromArray($style['subtitle']);
                $event->sheet->mergeCells("A3:H3");
                $event->sheet->getStyle('A3')->applyFromArray($style['periode']);
                $event->sheet->getStyle('A1:H3')->applyFromArray($style['border']);

                $i = count($this->data) + 4;
                $event->sheet->getStyle('A4')->applyFromArray($style['border']);
                $event->sheet->duplicateStyle($event->sheet->getStyle('A4'), 'A4:H' . $i);

                $event->sheet->getStyle('A4:H4')->applyFromArray($style['head']);

                $event->sheet->getStyle('A5')->getAlignment()->setWrapText(true)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->duplicateStyle($event->sheet->getStyle('A5'), 'A5:A' . $i);

                $event->sheet->getStyle('G5')->getAlignment()->setWrapText(true)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $event->sheet->duplicateStyle($event->sheet->getStyle('G5'), 'G5:H' . $i);

                $event->sheet->mergeCells("A" . ($i - 3) . ":G" . ($i - 3));
                $event->sheet->getStyle("A" . ($i - 3) . ":H" . ($i - 3))->applyFromArray($style['footer']);
                $event->sheet->mergeCells("A" . ($i - 2) . ":G" . ($i - 2));
                $event->sheet->getStyle("A" . ($i - 2) . ":H" . ($i - 2))->applyFromArray($style['footer']);
                $event->sheet->mergeCells("A" . ($i - 1) . ":G" . ($i - 1));
                $event->sheet->getStyle("A" . ($i - 1) . ":H" . ($i - 1))->applyFromArray($style['footer']);
                $event->sheet->mergeCells("A" . $i . ":G" . $i);
                $event->sheet->getStyle("A" . $i . ":H" . $i)->applyFromArray($style['footer']);
                $i++;
            }
        ];
    }
}