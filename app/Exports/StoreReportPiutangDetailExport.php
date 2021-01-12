<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class StoreReportPiutangDetailExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
{
    private $data;
    public function __construct($data)
    {
        // dd($data);
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
            ['Rekapitulasi Piutang'],
            [$this->data['periode']],
            ['Keanggotaan', '', $this->data['anggota']],
            ['Wilayah', '', $this->data['wilayah']],
            $this->data['header'],
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
                $event->sheet->mergeCells("A3:G3");
                $event->sheet->getStyle('A3')->applyFromArray($style['periode']);
                $event->sheet->getStyle('A1:G3')->applyFromArray($style['border']);

                $i = count($this->data['data']) + 7;
                $event->sheet->getStyle('A4')->applyFromArray($style['border']);
                $event->sheet->duplicateStyle($event->sheet->getStyle('A4'), 'A4:G' . $i);
                $event->sheet->mergeCells("A4:B4");
                $event->sheet->mergeCells("C4:G4");
                $event->sheet->mergeCells("A5:B5");
                $event->sheet->mergeCells("C5:G5");
                $event->sheet->getStyle('A6:G6')->applyFromArray($style['head']);

                $event->sheet->getStyle('D7')->getAlignment()->setWrapText(true)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $event->sheet->duplicateStyle($event->sheet->getStyle('D7'), 'D7:G' . $i);

                $event->sheet->setCellValue("A" . $i, 'Jumlah');
                $event->sheet->setCellValue("D" . $i, number_format($this->data['saldo_awal'], 2, ',', '.'));
                $event->sheet->setCellValue("E" . $i, number_format($this->data['penambahan'], 2, ',', '.'));
                $event->sheet->setCellValue("F" . $i, number_format($this->data['pengurangan'], 2, ',', '.'));
                $event->sheet->setCellValue("G" . $i, number_format($this->data['saldo_akhir'], 2, ',', '.'));
                $event->sheet->mergeCells("A" . $i . ":C" . $i);
                $event->sheet->getStyle('A' . $i . ':G' . $i)->applyFromArray($style['footer']);
            }
        ];
    }
}