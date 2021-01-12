<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class CashflowExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
{
    public function __construct($data)
    {
        $this->end_date = $data['end_date'];
        $this->data = $data['data'];
        $this->view = $data['view'];
        $this->account = $data['account'];
        $this->row = $data['total_row'];
        $this->row_aktivitas_opr = $data['row_aktivitas_opr'];
        $this->row_aktivitas_inv = $data['row_aktivitas_inv'];
        $this->row_aktivitas_pend = $data['row_aktivitas_pend'];
    }

    public function array(): array
    {
        return $this->data;
    }
    public function headings(): array
    {
        if ($this->view == 'all') {
            return [
                [config('koperasi.nama')],
                ['Arus ' . $this->account->name],
                ['Periode ' . date('d M Y', strtotime($this->end_date))],
                ['#', 'Kode Akun', 'Nama Akun', 'Penambahan (Rp)', 'Pengurangan (Rp)', 'Rp']
            ];
        } else {
            return [
                [config('koperasi.nama')],
                ['Arus ' . $this->account->name],
                ['Periode ' . date('d M Y', strtotime($this->end_date))],
                ['#', 'Kelompok Akun', 'Penambahan (Rp)', 'Pengurangan (Rp)', 'Rp']
            ];
        }
    }
    public function registerEvents(): array
    {
        $style = config('styleExport');

        if ($this->view == 'all') {
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

                    // angka dikanan
                    $event->sheet->mergeCells("A5:F5");
                    $event->sheet->getStyle('D6')->getAlignment()->setWrapText(true)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $event->sheet->duplicateStyle($event->sheet->getStyle('D6'), 'D6:F' . $i);
                    // aktivitas operasional
                    $aktivitas_opr = $this->row_aktivitas_opr + 6;
                    $event->sheet->mergeCells("A" . $aktivitas_opr . ":C" . $aktivitas_opr);
                    $event->sheet->getStyle("A" . $aktivitas_opr . ":F" . ($aktivitas_opr + 1))->applyFromArray($style['footer']);
                    $aktivitas_opr++;
                    $event->sheet->mergeCells("A" . $aktivitas_opr . ":E" . $aktivitas_opr);
                    $aktivitas_opr++;
                    // aktivitas investasi
                    $event->sheet->mergeCells("A" . $aktivitas_opr . ":F" . $aktivitas_opr);
                    $aktivitas_inv = $this->row_aktivitas_inv + 1 + $aktivitas_opr;
                    $event->sheet->mergeCells("A" . $aktivitas_inv . ":C" . $aktivitas_inv);
                    $event->sheet->getStyle("A" . $aktivitas_inv . ":F" . ($aktivitas_inv + 1))->applyFromArray($style['footer']);
                    $aktivitas_inv++;
                    $event->sheet->mergeCells("A" . $aktivitas_inv . ":E" . $aktivitas_inv);
                    $aktivitas_inv++;
                    // aktivitas pendanaan
                    $event->sheet->mergeCells("A" . $aktivitas_inv . ":F" . $aktivitas_inv);
                    $aktivitas_pend = $this->row_aktivitas_pend + 1 + $aktivitas_inv;
                    $event->sheet->mergeCells("A" . $aktivitas_pend . ":C" . $aktivitas_pend);
                    $event->sheet->getStyle("A" . $aktivitas_pend . ":F" . ($aktivitas_pend + 3))->applyFromArray($style['footer']);
                    $aktivitas_pend++;
                    $event->sheet->mergeCells("A" . $aktivitas_pend . ":E" . $aktivitas_pend);
                    $aktivitas_pend++;
                    $event->sheet->mergeCells("A" . $aktivitas_pend . ":E" . $aktivitas_pend);
                    $aktivitas_pend++;
                    $event->sheet->mergeCells("A" . $aktivitas_pend . ":E" . $aktivitas_pend);
                }
            ];
        } else {
            return [
                AfterSheet::class => function (AfterSheet $event) use ($style) {
                    $event->sheet->mergeCells("A1:E1");
                    $event->sheet->getStyle('A1')->applyFromArray($style['title']);
                    $event->sheet->mergeCells("A2:E2");
                    $event->sheet->getStyle('A2')->applyFromArray($style['subtitle']);
                    $event->sheet->mergeCells("A3:E3");
                    $event->sheet->getStyle('A3')->applyFromArray($style['periode']);
                    $event->sheet->getStyle('A1:E3')->applyFromArray($style['border']);

                    $i = $this->row + 4;
                    $event->sheet->getStyle('A4')->applyFromArray($style['border']);
                    $event->sheet->duplicateStyle($event->sheet->getStyle('A4'), 'A4:E' . $i);
                    $event->sheet->getStyle('A4:E4')->applyFromArray($style['head']);

                    // angka dikanan
                    $event->sheet->mergeCells("A5:E5");
                    $event->sheet->getStyle('C6')->getAlignment()->setWrapText(true)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $event->sheet->duplicateStyle($event->sheet->getStyle('C6'), 'C6:E' . $i);
                    // aktivitas operasional
                    $aktivitas_opr = $this->row_aktivitas_opr + 6;
                    $event->sheet->mergeCells("A" . $aktivitas_opr . ":B" . $aktivitas_opr);
                    $event->sheet->getStyle("A" . $aktivitas_opr . ":E" . ($aktivitas_opr + 1))->applyFromArray($style['footer']);
                    $aktivitas_opr++;
                    $event->sheet->mergeCells("A" . $aktivitas_opr . ":D" . $aktivitas_opr);
                    $aktivitas_opr++;
                    // aktivitas investasi
                    $event->sheet->mergeCells("A" . $aktivitas_opr . ":E" . $aktivitas_opr);
                    $aktivitas_inv = $this->row_aktivitas_inv + 1 + $aktivitas_opr;
                    $event->sheet->mergeCells("A" . $aktivitas_inv . ":B" . $aktivitas_inv);
                    $event->sheet->getStyle("A" . $aktivitas_inv . ":E" . ($aktivitas_inv + 1))->applyFromArray($style['footer']);
                    $aktivitas_inv++;
                    $event->sheet->mergeCells("A" . $aktivitas_inv . ":D" . $aktivitas_inv);
                    $aktivitas_inv++;
                    // aktivitas pendanaan
                    $event->sheet->mergeCells("A" . $aktivitas_inv . ":E" . $aktivitas_inv);
                    $aktivitas_pend = $this->row_aktivitas_pend + 1 + $aktivitas_inv;
                    $event->sheet->mergeCells("A" . $aktivitas_pend . ":B" . $aktivitas_pend);
                    $event->sheet->getStyle("A" . $aktivitas_pend . ":E" . ($aktivitas_pend + 3))->applyFromArray($style['footer']);
                    $aktivitas_pend++;
                    $event->sheet->mergeCells("A" . $aktivitas_pend . ":D" . $aktivitas_pend);
                    $aktivitas_pend++;
                    $event->sheet->mergeCells("A" . $aktivitas_pend . ":D" . $aktivitas_pend);
                    $aktivitas_pend++;
                    $event->sheet->mergeCells("A" . $aktivitas_pend . ":D" . $aktivitas_pend);
                }
            ];
        }
    }
}