<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class BalanceExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
{
    private $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        // dd($this->data);
        return $this->data['data'];
    }
    public function headings(): array
    {
        if ($this->data['view'] == 'all') {
            return [
                [config('koperasi.nama')],
                ['Neraca'],
                ['Periode ' . date('d M Y', strtotime($this->data['end_date']))],
                ['#', 'Kode Akun', 'Nama Akun', date('d M Y', strtotime($this->data['end_date'])) . ' (Rp)', '31 Dec ' . date('Y', strtotime('-1 year', strtotime($this->data['start_date']))) . ' (Rp)', '#', 'Kode Akun', 'Nama Akun', date('d M Y', strtotime($this->data['end_date'])) . ' (Rp)', '31 Dec ' . date('Y', strtotime('-1 year', strtotime($this->data['start_date']))) . ' (Rp)'],
                ['Aktiva', '', '', '', '', 'Pasiva']
            ];
        } else {
            return [
                [config('koperasi.nama')],
                ['Neraca'],
                ['Periode ' . date('d M Y', strtotime($this->data['end_date']))],
                ['#', 'Kelompok Akun', date('d M Y', strtotime($this->data['end_date'])) . ' (Rp)', '31 Dec ' . date('Y', strtotime('-1 year', strtotime($this->data['start_date']))) . ' (Rp)', '#', 'Kelompok Akun', date('d M Y', strtotime($this->data['end_date'])) . ' (Rp)', '31 Dec ' . date('Y', strtotime('-1 year', strtotime($this->data['start_date']))) . ' (Rp)'],
                ['Aktiva', '', '', '', 'Pasiva']
            ];
        }
    }
    public function registerEvents(): array
    {
        $style = config('styleExport');

        if ($this->data['view'] == 'all') {
            return [
                AfterSheet::class => function (AfterSheet $event) use ($style) {
                    $event->sheet->mergeCells("A1:J1");
                    $event->sheet->getStyle('A1')->applyFromArray($style['title']);
                    $event->sheet->mergeCells("A2:J2");
                    $event->sheet->getStyle('A2')->applyFromArray($style['subtitle']);
                    $event->sheet->mergeCells("A3:J3");
                    $event->sheet->getStyle('A3')->applyFromArray($style['periode']);
                    $event->sheet->getStyle('A1:J3')->applyFromArray($style['border']);

                    $i = $this->data['total_row'] + 5;
                    $event->sheet->getStyle('A4')->applyFromArray($style['border']);
                    $event->sheet->duplicateStyle($event->sheet->getStyle('A4'), 'A4:J' . $i);
                    $event->sheet->getStyle('A4:J4')->applyFromArray($style['head']);

                    $event->sheet->mergeCells("A5:E5");
                    $event->sheet->getStyle('A5')->applyFromArray($style['periode']);
                    $event->sheet->mergeCells("F5:J5");
                    $event->sheet->getStyle('F5')->applyFromArray($style['periode']);

                    $event->sheet->mergeCells("B6:E6");
                    $event->sheet->mergeCells("G6:J6");
                    // angka dikanan
                    $event->sheet->getStyle('D7')->getAlignment()->setWrapText(true)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $event->sheet->duplicateStyle($event->sheet->getStyle('D7'), 'D7:E' . $i);
                    $event->sheet->duplicateStyle($event->sheet->getStyle('D7'), 'I7:J' . $i);
                    // nomor ditengah
                    $event->sheet->getStyle('A6')->getAlignment()->setWrapText(true)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $event->sheet->duplicateStyle($event->sheet->getStyle('A6'), 'A6:A' . $i);
                    $event->sheet->duplicateStyle($event->sheet->getStyle('A6'), 'F6:F' . $i);
                    // aktiva lancar
                    $aktiva_lancar = $this->data['row_aktiva_lancar'] + 7;
                    $event->sheet->mergeCells("A" . $aktiva_lancar . ":C" . $aktiva_lancar);
                    $event->sheet->getStyle("A" . $aktiva_lancar . ":E" . $aktiva_lancar)->applyFromArray($style['footer']);
                    $aktiva_lancar++;
                    $event->sheet->mergeCells("B" . $aktiva_lancar . ":E" . $aktiva_lancar);
                    // investasi
                    $investasi = $this->data['row_investasi'] + $aktiva_lancar + 1;
                    $event->sheet->mergeCells("A" . $investasi . ":C" . $investasi);
                    $event->sheet->getStyle("A" . $investasi . ":E" . $investasi)->applyFromArray($style['footer']);
                    $investasi++;
                    $event->sheet->mergeCells("B" . $investasi . ":E" . $investasi);
                    // aktiva tetap
                    $aktiva_tetap = $this->data['row_aktiva_tetap'] + $investasi + 1;
                    $event->sheet->mergeCells("A" . $aktiva_tetap . ":C" . $aktiva_tetap);
                    $event->sheet->getStyle("A" . $aktiva_tetap . ":E" . $aktiva_tetap)->applyFromArray($style['footer']);
                    $aktiva_tetap++;
                    // kewajiban jk pendek
                    $kewajiban_jk_pendek = $this->data['row_kewajiban_jk_pendek'] + 7;
                    $event->sheet->mergeCells("F" . $kewajiban_jk_pendek . ":H" . $kewajiban_jk_pendek);
                    $event->sheet->getStyle("F" . $kewajiban_jk_pendek . ":J" . $kewajiban_jk_pendek)->applyFromArray($style['footer']);
                    $kewajiban_jk_pendek++;
                    $event->sheet->mergeCells("G" . $kewajiban_jk_pendek . ":J" . $kewajiban_jk_pendek);
                    // kewajiban jk panjang
                    $kewajiban_jk_panjang = $this->data['row_kewajiban_jk_panjang'] + 1 + $kewajiban_jk_pendek;
                    $event->sheet->mergeCells("F" . $kewajiban_jk_panjang . ":H" . $kewajiban_jk_panjang);
                    $event->sheet->getStyle("F" . $kewajiban_jk_panjang . ":J" . $kewajiban_jk_panjang)->applyFromArray($style['footer']);
                    $kewajiban_jk_panjang++;
                    $event->sheet->mergeCells("G" . $kewajiban_jk_panjang . ":J" . $kewajiban_jk_panjang);
                    // modal
                    $modal = $this->data['row_modal'] + 1 + $kewajiban_jk_panjang;
                    $event->sheet->mergeCells("F" . $modal . ":H" . $modal);
                    $event->sheet->getStyle("F" . $modal . ":J" . $modal)->applyFromArray($style['footer']);
                    $modal++;
                    $event->sheet->mergeCells("G" . $modal . ":J" . $modal);
                    // phu
                    $phu = $this->data['row_phu'] + 1 + $modal;
                    $event->sheet->mergeCells("F" . $phu . ":H" . $phu);
                    $event->sheet->getStyle("F" . $phu . ":J" . $phu)->applyFromArray($style['footer']);
                    $phu++;
                    // total aktiva pasiva
                    $event->sheet->mergeCells("A" . $i . ":C" . $i);
                    $event->sheet->mergeCells("F" . $i . ":H" . $i);
                    $event->sheet->getStyle("A" . $i . ":J" . $i)->applyFromArray($style['footer']);
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

                    $i = $this->data['total_row'] + 5;
                    $event->sheet->getStyle('A4')->applyFromArray($style['border']);
                    $event->sheet->duplicateStyle($event->sheet->getStyle('A4'), 'A4:H' . $i);
                    $event->sheet->getStyle('A4:H4')->applyFromArray($style['head']);

                    $event->sheet->mergeCells("A5:D5");
                    $event->sheet->getStyle('A5')->applyFromArray($style['periode']);
                    $event->sheet->mergeCells("E5:H5");
                    $event->sheet->getStyle('E5')->applyFromArray($style['periode']);

                    $event->sheet->mergeCells("B6:D6");
                    $event->sheet->mergeCells("F6:H6");
                    // angka dikanan
                    $event->sheet->getStyle('C7')->getAlignment()->setWrapText(true)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $event->sheet->duplicateStyle($event->sheet->getStyle('C7'), 'C7:D' . $i);
                    $event->sheet->duplicateStyle($event->sheet->getStyle('C7'), 'G7:H' . $i);
                    // nomor ditengah
                    $event->sheet->getStyle('A6')->getAlignment()->setWrapText(true)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $event->sheet->duplicateStyle($event->sheet->getStyle('A6'), 'A6:A' . $i);
                    $event->sheet->duplicateStyle($event->sheet->getStyle('A6'), 'E6:E' . $i);
                    // aktiva lancar
                    $aktiva_lancar = $this->data['row_aktiva_lancar'] + 7;
                    $event->sheet->mergeCells("A" . $aktiva_lancar . ":B" . $aktiva_lancar);
                    $event->sheet->getStyle("A" . $aktiva_lancar . ":D" . $aktiva_lancar)->applyFromArray($style['footer']);
                    $aktiva_lancar++;
                    $event->sheet->mergeCells("B" . $aktiva_lancar . ":D" . $aktiva_lancar);
                    // investasi
                    $investasi = $this->data['row_investasi'] + $aktiva_lancar + 1;
                    $event->sheet->mergeCells("A" . $investasi . ":B" . $investasi);
                    $event->sheet->getStyle("A" . $investasi . ":D" . $investasi)->applyFromArray($style['footer']);
                    $investasi++;
                    $event->sheet->mergeCells("B" . $investasi . ":D" . $investasi);
                    // aktiva tetap
                    $aktiva_tetap = $this->data['row_aktiva_tetap'] + $investasi + 1;
                    $event->sheet->mergeCells("A" . $aktiva_tetap . ":B" . $aktiva_tetap);
                    $event->sheet->getStyle("A" . $aktiva_tetap . ":D" . $aktiva_tetap)->applyFromArray($style['footer']);
                    $aktiva_tetap++;
                    // kewajiban jk pendek
                    $kewajiban_jk_pendek = $this->data['row_kewajiban_jk_pendek'] + 7;
                    $event->sheet->mergeCells("E" . $kewajiban_jk_pendek . ":F" . $kewajiban_jk_pendek);
                    $event->sheet->getStyle("E" . $kewajiban_jk_pendek . ":G" . $kewajiban_jk_pendek)->applyFromArray($style['footer']);
                    $kewajiban_jk_pendek++;
                    $event->sheet->mergeCells("F" . $kewajiban_jk_pendek . ":G" . $kewajiban_jk_pendek);
                    // kewajiban jk panjang
                    $kewajiban_jk_panjang = $this->data['row_kewajiban_jk_panjang'] + 1 + $kewajiban_jk_pendek;
                    $event->sheet->mergeCells("E" . $kewajiban_jk_panjang . ":F" . $kewajiban_jk_panjang);
                    $event->sheet->getStyle("E" . $kewajiban_jk_panjang . ":G" . $kewajiban_jk_panjang)->applyFromArray($style['footer']);
                    $kewajiban_jk_panjang++;
                    $event->sheet->mergeCells("F" . $kewajiban_jk_panjang . ":G" . $kewajiban_jk_panjang);
                    // modal
                    $modal = $this->data['row_modal'] + 1 + $kewajiban_jk_panjang;
                    $event->sheet->mergeCells("E" . $modal . ":F" . $modal);
                    $event->sheet->getStyle("E" . $modal . ":G" . $modal)->applyFromArray($style['footer']);
                    $modal++;
                    $event->sheet->mergeCells("F" . $modal . ":G" . $modal);
                    // phu
                    $phu = $this->data['row_phu'] + 1 + $modal;
                    $event->sheet->mergeCells("E" . $phu . ":F" . $phu);
                    $event->sheet->getStyle("E" . $phu . ":G" . $phu)->applyFromArray($style['footer']);
                    $phu++;
                    // total aktiva pasiva
                    $event->sheet->mergeCells("A" . $i . ":B" . $i);
                    $event->sheet->mergeCells("E" . $i . ":F" . $i);
                    $event->sheet->getStyle("A" . $i . ":H" . $i)->applyFromArray($style['footer']);
                }
            ];
        }
    }
}