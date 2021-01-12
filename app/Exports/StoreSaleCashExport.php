<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class StoreSaleCashExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
{
    public function __construct($data)
    {
        // dd($data);
        $this->title = $data['title'];
        $this->periode = $data['periode'];
        $this->barang = $data['barang'];
        $this->data = $data['data'];
        $this->qty_awal = $data['qty_awal'];
        $this->saldo_awal = $data['saldo_awal'];
        $this->qty_akhir = $data['qty_akhir'];
        $this->saldo_akhir = $data['saldo_akhir'];
        $this->date = $data['date'];
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
            [$this->periode],
            ['Kode Barang', '', $this->barang->code],
            ['Nama Barang', '', $this->barang->name],
            ['#', 'Kode Anggota', 'Nama Anggota', 'Tanggal Transaksi', 'No Faktur', 'Qty (Kg)', 'Harga (Rp)', 'Total (Rp)'],
            ['Saldo ' . date('d-m-Y', strtotime('-1 day', strtotime($this->date))), '', '', '', '', number_format($this->qty_awal, 2, ',', '.'), number_format($this->saldo_awal, 2, ',', '.')]
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

                $i = count($this->data) + 8;
                $event->sheet->getStyle('A4')->applyFromArray($style['border']);
                $event->sheet->duplicateStyle($event->sheet->getStyle('A4'), 'A4:H' . $i);
                $event->sheet->mergeCells("A4:B4");
                $event->sheet->mergeCells("C4:H4");
                $event->sheet->mergeCells("A5:B5");
                $event->sheet->mergeCells("C5:H5");
                $event->sheet->getStyle('A6:H6')->applyFromArray($style['head']);
                $event->sheet->getStyle('A7:H7')->applyFromArray($style['footer']);
                $event->sheet->mergeCells("A7:E7");
                $event->sheet->mergeCells("G7:H7");

                $event->sheet->getStyle('F8')->getAlignment()->setWrapText(true)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $event->sheet->duplicateStyle($event->sheet->getStyle('F8'), 'F8:H' . $i);

                $event->sheet->setCellValue("A" . $i, 'Jumlah');
                $event->sheet->setCellValue("F" . $i, number_format($this->qty_akhir, 2, ',', '.'));
                $event->sheet->setCellValue("G" . $i, number_format($this->saldo_akhir, 2, ',', '.'));
                $event->sheet->mergeCells("A" . $i . ":E" . $i);
                $event->sheet->mergeCells("G" . $i . ":H" . $i);
                $event->sheet->getStyle('A' . $i . ':H' . $i)->applyFromArray($style['footer']);
            }
        ];
    }
}