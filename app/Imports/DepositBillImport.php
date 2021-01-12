<?php

namespace App\Imports;

use App\Classes\DepositClass;
use App\Classes\MasterClass;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DepositBillImport implements ToCollection, WithHeadingRow
{
    public function __construct($data)
    {
        $this->transaction_date = $data['transaction_date'];
        $this->account = $data['account'];
        $this->master = new MasterClass();
        $this->deposit = new DepositClass();
    }
    public function collection(Collection $collection)
    {
        $data = [];
        $i = 0;
        foreach ($collection as $key => $value) {
            $member = $this->master->memberGet(['code', $value['kode_anggota']]);
            $deposit = $this->deposit->depositGet(['account_number', $value['no_rekening']]);
            if ($member && $deposit) {
                $data[$i]['created_at'] = date('Y-m-d H:i:s');
                $data[$i]['created_by'] = auth()->user()->id;
                $data[$i]['member_id'] = $member->id;
                $data[$i]['deposit_id'] = $deposit->id;
                $data[$i]['bayar'] = $value['bayar'];
                $data[$i]['account'] = $this->account;
                $data[$i]['transaction_date'] = $this->transaction_date;
            }
        }
        DB::table('deposit_bill_uploads')->insert($data);
    }
}