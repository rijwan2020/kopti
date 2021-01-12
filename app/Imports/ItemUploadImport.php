<?php

namespace App\Imports;

use App\Classes\StoreClass;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ItemUploadImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    public function __construct()
    {
        $this->store = new StoreClass();
    }
    public function collection(Collection $collection)
    {
        $data = [];
        $gudang = $this->store->warehouseList();
        $i = 0;
        foreach ($collection as $key => $value) {
            $data[$i]['code'] = $value['kode_barang'];
            $data[$i]['name'] = $value['nama_barang'];
            $data[$i]['harga_beli'] = $value['harga_beli_satuan_rp'];
            $data[$i]['harga_jual'] = $value['harga_jual_rp'];
            $data[$i]['qty_pusat'] = $value['qty_pusat_kg'];
            $qty_gudang = [];
            foreach ($gudang as $hsl => $hasil) {
                $qty_gudang[$hasil->id] = $value['qty_' . str_replace(' ', '_', strtolower($hasil->name)) . '_kg'];
            }
            $suplier = $this->store->suplierGet(['code', $value['kode_suplier']]);
            if ($suplier) {
                $data[$i]['suplier_id'] = $suplier->id;
            } else {
                $data[$i]['suplier_id'] = 0;
            }
            $data[$i]['qty_gudang'] = json_encode($qty_gudang);
            $data[$i]['tanggal_beli'] = $value['tanggal_beli'] != null ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value['tanggal_beli'])->format('Y-m-d') : date('Y-m-d');
            $data[$i]['tanggal_kadaluarsa'] = date('Y-m-d');
            $i++;
        }
        DB::table('store_item_uploads')->insert($data);
    }

    public function chunkSize(): int
    {
        return 50;
    }
}