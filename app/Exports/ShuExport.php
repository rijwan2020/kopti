<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class ShuExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
{
    public function __construct($data)
    {
        $this->end_date = $data['end_date'];
        $this->data = $data['data'];
        $this->shu = $data['shu'];
        $this->zakat = $data['zakat'];
        $this->row = $data['total_row'];
    }

    public function array(): array
    {
        return $this->data;
    }
    public function headings(): array
    {
        return [
            [config('koperasi.nama')],
            ['Rencana Pembagian Sisa Hasil Usaha'],
            ['Periode ' . date('d M Y', strtotime($this->end_date))],
            ['Perhitungan Hasil Usaha', '', '', ($this->shu >= 0 ? 'Rp' . number_format($this->shu + $this->zakat, 2, ',', '.') : '(Rp' . number_format(($this->shu + $this->zakat) * -1, 2, ',', '.')) . ')'],
            ['Pengeluaran Zakat (2.5%)', '', '', ($this->shu >= 0 ? number_format($this->zakat, 2, ',', '.') : 'Rp0')],
            ['Dana yang dibagikan (PHU - Zakat)', '', '', ($this->shu >= 0 ? number_format($this->shu, 2, ',', '.') : 'Rp0')],
            ['Rencana Pembagian PHU sesuai dengan Anggaran Rumah Tangga ' . config('koperasi.nama') . ' Bab XII pasal 63 sebagai berikut :'],
        ];
    }
    public function registerEvents(): array
    {
        $style = config('styleExport');

        return [
            AfterSheet::class => function (AfterSheet $event) use ($style) {
                $event->sheet->mergeCells("A1:D1");
                $event->sheet->getStyle('A1')->applyFromArray($style['title']);
                $event->sheet->mergeCells("A2:D2");
                $event->sheet->getStyle('A2')->applyFromArray($style['subtitle']);
                $event->sheet->mergeCells("A3:D3");
                $event->sheet->getStyle('A3')->applyFromArray($style['periode']);
                $event->sheet->getStyle('A1:D3')->applyFromArray($style['border']);

                $i = $this->row + 7;
                $event->sheet->getStyle('A4')->applyFromArray($style['border']);
                $event->sheet->duplicateStyle($event->sheet->getStyle('A4'), 'A4:D' . $i);

                $event->sheet->getStyle('D4')->getAlignment()->setWrapText(true)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $event->sheet->duplicateStyle($event->sheet->getStyle('D4'), 'D4:D' . $i);

                $event->sheet->mergeCells("A4:C4");
                $event->sheet->mergeCells("A5:C5");
                $event->sheet->mergeCells("A6:C6");
                $event->sheet->mergeCells("A7:D7");

                $event->sheet->getRowDimension('7')->setRowHeight(40);
                $event->sheet->getStyle('A7')->getAlignment()->setWrapText(true);
            }
        ];
    }
}