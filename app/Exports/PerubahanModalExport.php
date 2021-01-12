<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class PerubahanModalExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
{
    public function __construct($data)
    {
        $this->end_date = $data['end_date'];
        $this->data = $data['data'];
        $this->row = $data['total_row'];
        $this->view = $data['view'];
    }
    public function array(): array
    {
        return $this->data;
    }
    public function headings(): array
    {
        if ($this->view == "all") {
            return [
                [config('koperasi.nama')],
                ['Perubahan Modal'],
                ['Periode ' . date('d M Y', strtotime($this->end_date))],
                ['#', 'Kode Akun', 'Nama Akun', 'Saldo Awal (Rp)', 'Penambahan (Rp)', 'Pengurangan (Rp)', 'Saldo Akhir (Rp)']
            ];
        } else {
            return [
                [config('koperasi.nama')],
                ['Perubahan Modal'],
                ['Periode ' . date('d M Y', strtotime($this->end_date))],
                ['#', 'Kelompok Akun', 'Saldo Awal (Rp)', 'Penambahan (Rp)', 'Pengurangan (Rp)', 'Saldo Akhir (Rp)']
            ];
        }
    }
    public function registerEvents(): array
    {
        $style = config('styleExport');

        if ($this->view == 'all') {
            return [
                AfterSheet::class => function (AfterSheet $event) use ($style) {
                    $event->sheet->mergeCells("A1:G1");
                    $event->sheet->getStyle('A1')->applyFromArray($style['title']);
                    $event->sheet->mergeCells("A2:G2");
                    $event->sheet->getStyle('A2')->applyFromArray($style['subtitle']);
                    $event->sheet->mergeCells("A3:G3");
                    $event->sheet->getStyle('A3')->applyFromArray($style['periode']);
                    $event->sheet->getStyle('A1:G3')->applyFromArray($style['border']);

                    $i = $this->row + 4;
                    $event->sheet->getStyle('A4')->applyFromArray($style['border']);
                    $event->sheet->duplicateStyle($event->sheet->getStyle('A4'), 'A4:G' . $i);

                    $event->sheet->getStyle('A4:G4')->applyFromArray($style['head']);

                    $event->sheet->getStyle('D5')->getAlignment()->setWrapText(true)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $event->sheet->duplicateStyle($event->sheet->getStyle('D5'), 'D5:G' . $i);

                    $event->sheet->mergeCells("A" . $i . ":C" . $i);
                    $event->sheet->getStyle("A" . $i . ":G" . $i)->applyFromArray($style['footer']);
                }
            ];
        } else {
            return [
                AfterSheet::class => function (AfterSheet $event) use ($style) {
                    $event->sheet->mergeCells("A1:F1");
                    $event->sheet->getStyle('A1')->applyFromArray($style['title']);
                    $event->sheet->mergeCells("A2:F2");
                    $event->sheet->getStyle('A2')->applyFromArray($style['subtitle']);
                    $event->sheet->mergeCells("A3:F3");
                    $event->sheet->getStyle('A3')->applyFromArray($style['periode']);
                    $event->sheet->getStyle('A1:F3')->applyFromArray($style['border']);

                    $i = $this->row + 4;
                    $event->sheet->getStyle('A4')->applyFromArray($style['border']);
                    $event->sheet->duplicateStyle($event->sheet->getStyle('A4'), 'A4:F' . $i);

                    $event->sheet->getStyle('A4:F4')->applyFromArray($style['head']);

                    $event->sheet->getStyle('C5')->getAlignment()->setWrapText(true)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $event->sheet->duplicateStyle($event->sheet->getStyle('C5'), 'C5:F' . $i);

                    $event->sheet->mergeCells("A" . $i . ":B" . $i);
                    $event->sheet->getStyle("A" . $i . ":F" . $i)->applyFromArray($style['footer']);
                }
            ];
        }
    }
}