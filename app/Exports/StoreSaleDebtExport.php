<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class StoreSaleDebtExport implements FromArray, WithHeadings, WithEvents, ShouldAutoSize
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
            ['#', 'Kode Anggota', 'Nama Anggota', 'Tanggal Transaksi', 'No Faktur', 'Status Pembayaran', 'Qty (Kg)', 'Harga (Rp)', 'Total (Rp)'],
            ['Saldo ' . date('d-m-Y', strtotime('-1 day', strtotime($this->date))), '', '', '', '', '', number_format($this->qty_awal, 2, ',', '.'), number_format($this->saldo_awal, 2, ',', '.')]
        ];
    }
    public function registerEvents(): array
    {
        $style = config('styleExport');
        return [
            AfterSheet::class => function (AfterSheet $event) use ($style) {
                $event->sheet->mergeCells("A1:I1");
                $event->sheet->getStyle('A1')->applyFromArray($style['title']);
                $event->sheet->mergeCells("A2:I2");
                $event->sheet->getStyle('A2')->applyFromArray($style['subtitle']);
                $event->sheet->mergeCells("A3:I3");
                $event->sheet->getStyle('A3')->applyFromArray($style['periode']);
                $event->sheet->getStyle('A1:I3')->applyFromArray($style['border']);

                $i = count($this->data) + 8;
                $event->sheet->getStyle('A4')->applyFromArray($style['border']);
                $event->sheet->duplicateStyle($event->sheet->getStyle('A4'), 'A4:I' . $i);
                $event->sheet->mergeCells("A4:B4");
                $event->sheet->mergeCells("C4:I4");
                $event->sheet->mergeCells("A5:B5");
                $event->sheet->mergeCells("C5:I5");
                $event->sheet->getStyle('A6:I6')->applyFromArray($style['head']);
                $event->sheet->getStyle('A7:I7')->applyFromArray($style['footer']);
                $event->sheet->mergeCells("A7:F7");
                $event->sheet->mergeCells("H7:I7");

                $event->sheet->getStyle('F8')->getAlignment()->setWrapText(true)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $event->sheet->duplicateStyle($event->sheet->getStyle('F8'), 'F8:I' . $i);

                $event->sheet->setCellValue("A" . $i, 'Jumlah');
                $event->sheet->setCellValue("G" . $i, number_format($this->qty_akhir, 2, ',', '.'));
                $event->sheet->setCellValue("H" . $i, number_format($this->saldo_akhir, 2, ',', '.'));
                $event->sheet->mergeCells("A" . $i . ":F" . $i);
                $event->sheet->mergeCells("H" . $i . ":I" . $i);
                $event->sheet->getStyle('A' . $i . ':I' . $i)->applyFromArray($style['footer']);
            }
        ];
    }
}