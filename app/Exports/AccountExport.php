<?php

namespace App\Exports;

use App\Model\Account;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class AccountExport implements FromQuery, WithMapping, WithHeadings, WithEvents, ShouldAutoSize
{
    private $no = 0;
    public function query()
    {
        $query = Account::where('level', 3)->orderBy('code', 'asc');
        return $query;
    }
    public function map($account): array
    {
        return [
            ++$this->no,
            $account->code,
            $account->name,
            $account->type == 0 ? 'Debit' : 'Kredit',
            '0',
        ];
    }
    public function headings(): array
    {
        return [
            '#', //A
            'Kode Akun', //B
            'Nama Akun', //C
            'Saldo Normal', //D
            'Saldo Awal', //E
        ];
    }
    public function registerEvents(): array
    {
        $style = config('styleExport');

        return [
            AfterSheet::class => function (AfterSheet $event) use ($style) {
                //give borders
                $i = $this->no + 1;
                $event->sheet->getStyle('A2')->applyFromArray($style['border']);
                $event->sheet->duplicateStyle($event->sheet->getStyle('A2'), 'A1:E' . $i);
                //give style for heading
                $event->sheet->getStyle('A1:E1')->applyFromArray($style['head']);
            }
        ];
    }
}