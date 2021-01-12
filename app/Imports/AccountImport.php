<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AccountImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $data = [];
        $i = 0;
        foreach ($collection as $value) {
            $type = strtolower($value['saldo_normal']) == 'debit' ? 0 : 1;
            $data[$i]['code'] = $value['kode_akun'];
            $data[$i]['name'] = $value['nama_akun'];
            $data[$i]['type'] = $type;
            $data[$i]['balance'] = $value['saldo_awal'] ?? 0;
            $i++;
        }
        DB::table('account_uploads')->insert($data);
    }
}