<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class ItemCardExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
{
    public function __construct($data)
    {
        $this->title = $data['title'];
        $this->item = $data['item'];
        $this->data = $data['data'];
        $this->start_date = $data['start_date'];
        $this->end_date = $data['end_date'];
        $this->persediaan_awal = $data['persediaan_awal'];
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
            [$this->title],
            [date('d M Y', strtotime($this->start_date)) . ' - ' . date('d M Y', strtotime($this->end_date))],
            ['Nama Barang : ' . $this->item->name],
            ['Kode Barang : ' . $this->item->code],
            ['No', 'Tanggal Transaksi', 'No Referensi / No Bukti', 'Keterangan', 'Masuk (Kg)', 'Keluar (Kg)', 'Jumlah (Kg)'],
            [('Stok s/d ' . date('d M Y', strtotime('-1 day', strtotime($this->start_date)))), '', '', '', '', '', number_format($this->persediaan_awal, 0, ',', '.')]
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

                $event->sheet->mergeCells("A4:G4");
                $event->sheet->mergeCells("A5:G5");
                $event->sheet->getStyle('A1:G3')->applyFromArray($style['border']);

                $i = count($this->data) + 7;
                $event->sheet->getStyle('A4')->applyFromArray($style['border']);
                $event->sheet->duplicateStyle($event->sheet->getStyle('A4'), 'A4:G' . $i);

                $event->sheet->getStyle('A6:G6')->applyFromArray($style['head']);
                $event->sheet->mergeCells("A7:F7");
                $event->sheet->getStyle("A7:G7")->applyFromArray($style['footer']);

                $event->sheet->getStyle('E8')->getAlignment()->setWrapText(true)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $event->sheet->duplicateStyle($event->sheet->getStyle('E8'), 'E8:G' . $i);
                $i++;

                $event->sheet->setCellValue("A" . $i, 'Stok Akhir');
                $event->sheet->setCellValue("G" . $i, number_format($this->saldo, 0, ',', '.'));
                $event->sheet->mergeCells("A" . $i . ":F" . $i);
                $event->sheet->getStyle("A" . $i . ":G" . $i)->applyFromArray($style['footer']);
            }
        ];
    }
}