<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class TrialBalanceExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
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
        if ($this->data['view'] == "all") {
            return [
                [config('koperasi.nama')],
                ['Neraca Saldo'],
                ['Per ' . date('d-m-Y', strtotime($this->data['end_date']))],
                ['#', 'Kode Akun', 'Nama Akun', 'Saldo Awal (Rp)', '', 'Mutasi (Rp)', '', 'Saldo Akhir (Rp)'],
                ['', '', '', 'Debit', 'Kredit', 'Debit', 'Kredit', 'Debit', 'Kredit']
            ];
        } else {
            return [
                [config('koperasi.nama')],
                ['Neraca Saldo'],
                ['Per ' . date('d-m-Y', strtotime($this->data['end_date']))],
                ['#', 'Kelompok Akun', 'Saldo Awal (Rp)', '', 'Mutasi (Rp)', '', 'Saldo Akhir (Rp)'],
                ['', '', 'Debit', 'Kredit', 'Debit', 'Kredit', 'Debit', 'Kredit']
            ];
        }
    }
    public function registerEvents(): array
    {
        $style = config('styleExport');

        if ($this->data['view'] == 'all') {
            return [
                AfterSheet::class => function (AfterSheet $event) use ($style) {
                    $event->sheet->mergeCells("A1:I1");
                    $event->sheet->getStyle('A1')->applyFromArray($style['title']);
                    $event->sheet->mergeCells("A2:I2");
                    $event->sheet->getStyle('A2')->applyFromArray($style['subtitle']);
                    $event->sheet->mergeCells("A3:I3");
                    $event->sheet->getStyle('A3')->applyFromArray($style['periode']);
                    $event->sheet->getStyle('A1:I3')->applyFromArray($style['border']);

                    $i = count($this->data['data']) + 6;
                    $event->sheet->getStyle('A6')->applyFromArray($style['border']);
                    $event->sheet->duplicateStyle($event->sheet->getStyle('A6'), 'A4:I' . $i);

                    $event->sheet->getStyle('A4:I5')->applyFromArray($style['head']);
                    $event->sheet->mergeCells("A4:A5");
                    $event->sheet->mergeCells("B4:B5");
                    $event->sheet->mergeCells("C4:C5");
                    $event->sheet->mergeCells("D4:E4");
                    $event->sheet->mergeCells("F4:G4");
                    $event->sheet->mergeCells("H4:I4");
                    $event->sheet->getStyle('A4')->applyFromArray($style['border']);
                    $event->sheet->duplicateStyle($event->sheet->getStyle('A4'), 'A4:I4');

                    $event->sheet->getStyle('D6')->getAlignment()->setWrapText(true)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $event->sheet->duplicateStyle($event->sheet->getStyle('D6'), 'D6:I' . $i);

                    $event->sheet->mergeCells("A" . $i . ":C" . $i);
                    $event->sheet->getStyle("A" . $i . ":I" . $i)->applyFromArray($style['footer']);
                }
            ];
        } else {
            return [
                AfterSheet::class => function (AfterSheet $event) use ($style) {
                    $event->sheet->mergeCells("A1:H1");
                    $event->sheet->getStyle('A1')->applyFromArray($style['title']);
                    $event->sheet->mergeCells("A2:H2");
                    $event->sheet->getStyle('A2')->applyFromArray($style['subtitle']);
                    $event->sheet->mergeCells("A3:H3");
                    $event->sheet->getStyle('A3')->applyFromArray($style['periode']);
                    $event->sheet->getStyle('A1:H3')->applyFromArray($style['border']);

                    $i = count($this->data['data']) + 6;
                    $event->sheet->getStyle('A6')->applyFromArray($style['border']);
                    $event->sheet->duplicateStyle($event->sheet->getStyle('A6'), 'A4:H' . $i);

                    $event->sheet->getStyle('A4:H5')->applyFromArray($style['head']);
                    $event->sheet->mergeCells("A4:A5");
                    $event->sheet->mergeCells("B4:B5");
                    $event->sheet->mergeCells("C4:D4");
                    $event->sheet->mergeCells("E4:F4");
                    $event->sheet->mergeCells("G4:H4");
                    $event->sheet->getStyle('A4')->applyFromArray($style['border']);
                    $event->sheet->duplicateStyle($event->sheet->getStyle('A4'), 'A4:H4');

                    $event->sheet->getStyle('C6')->getAlignment()->setWrapText(true)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $event->sheet->duplicateStyle($event->sheet->getStyle('C6'), 'C6:H' . $i);

                    $event->sheet->mergeCells("A" . $i . ":B" . $i);
                    $event->sheet->getStyle("A" . $i . ":H" . $i)->applyFromArray($style['footer']);
                }
            ];
        }
    }
}