<?php

namespace App\Imports;

use App\Classes\MasterClass;
use App\Model\StoreSaleDebtHistoryUpload;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StoreRekapitulasiPiutangImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        // dd($collection);
        $master = new MasterClass();
        $data = [];
        $i = 0;
        foreach ($collection as $key => $value) {
            $member = $master->memberGet(['code', $value['kode_anggota']]);
            if ($value['kode_anggota'] && $member != false) {
                $data = [
                    'created_by' => auth()->user()->id,
                    'member_id' => $member->id,
                    'no_ref' => $value['no_ref'],
                    'note' => $value['keterangan'],
                    'tipe' => (strtolower($value['tipe_transaksi']) == 'pemasukan' ? 0 : 1),
                    'total' => $value['jumlah'],
                    'trxdate' => ($value['tanggal_transaksi'] != null ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['tanggal_transaksi'])->format('Y-m-d') : date('Y-m-d')) . date(' H:i:s')
                ];
                StoreSaleDebtHistoryUpload::create($data);
            }
        }
    }
}