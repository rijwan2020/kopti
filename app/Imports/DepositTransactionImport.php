<?php

namespace App\Imports;

use App\Model\Deposit;
use App\Model\DepositTransactionUpload;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DepositTransactionImport implements ToCollection, WithHeadingRow
{
    public function __construct($data)
    {
        $this->tanggal_transaksi = $data['tanggal_transaksi'];
        $this->jurnal = $data['jurnal'];
        $this->akun = $data['akun'];
        $this->jenis_transaksi = [
            1 => 'Setoran',
            2 => 'Penarikan',
            3 => 'Jasa',
            4 => 'Administrasi',
            5 => 'Penyesuaian Setoran',
            6 => 'Penyesuaian Penarikan'
        ];
    }
    public function collection(Collection $collection)
    {
        foreach ($collection as $key => $value) {
            $simpanan = Deposit::where('account_number', $value['no_rekening'])->first();
            if ($simpanan) {
                $data['created_by'] = auth()->user()->id;
                $data['no_rekening'] = $value['no_rekening'];
                $data['member_id'] = $simpanan->member_id;
                $data['jenis_transaksi'] = $value['jenis_transaksi'];
                $data['no_ref'] = $value['no_ref'] ?? 'TRXS-' . date('YmdHis');
                $data['keterangan'] = $value['keterangan'] ?? $this->jenis_transaksi[$value['jenis_transaksi']];
                $data['jumlah'] = $value['jumlah'];
                $data['tanggal_transaksi'] = $this->tanggal_transaksi . date(' H:i:s');
                $data['akun'] = $this->akun;
                $data['jurnal'] = $this->jurnal;
                $data['deposit_id'] = $simpanan->id;
                DepositTransactionUpload::create($data);
            }
        }
    }
}