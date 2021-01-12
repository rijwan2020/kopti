<?php

namespace App\Imports;

use App\Classes\MasterClass;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DepositImport implements ToCollection, WithHeadingRow
{
    public function __construct($data)
    {
        $this->deposit_type_id = $data['deposit_type_id'];
        $this->jurnal = $data['jurnal'];
        $this->account = $data['account'];
        $this->master = new MasterClass();
    }
    public function collection(Collection $collection)
    {
        $data = [];
        $i = 0;
        foreach ($collection as $key => $value) {
            $reg_date = $value['tanggal_registrasi'] != null ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['tanggal_registrasi'])->format('Y-m-d') : null;
            $member = $this->master->memberGet(['code', $value['kode_anggota']]);
            if ($reg_date != null && $member && $value['saldo'] > 0) {
                $data[$i]['created_by'] = $data[$i]['updated_by'] = auth()->user()->id;
                $data[$i]['created_at'] = $data[$i]['updated_at'] = date('Y-m-d H:i:s');
                $data[$i]['account_number'] = $value['no_rekening'];
                $data[$i]['member_id'] = $member->id;
                $data[$i]['region_id'] = $member->region_id;
                $data[$i]['deposit_type_id'] = $this->deposit_type_id;
                $data[$i]['jurnal'] = $this->jurnal;
                $data[$i]['account_code'] = $this->account;
                $data[$i]['registration_date'] = $reg_date;
                $data[$i]['beginning_balance'] = $value['saldo'];
                $i++;
            }
        }
        DB::table('deposit_uploads')->insert($data);
    }
}