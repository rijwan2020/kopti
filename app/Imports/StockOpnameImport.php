<?php

namespace App\Imports;

use App\Model\StoreItemDetail;
use App\Model\StoreStockOpname;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StockOpnameImport implements ToCollection, WithHeadingRow
{
    public function __construct($tanggal)
    {
        $this->tanggal = $tanggal;
    }
    public function collection(Collection $collection)
    {
        foreach ($collection as $key => $value) {
            $itemdetail = StoreItemDetail::find($value['id']);
            if ($itemdetail) {
                $data = [
                    'item_detail_id' => $itemdetail->id,
                    'item_id' => $itemdetail->item_id,
                    'code' => $itemdetail->item->code,
                    'name' => $itemdetail->item->name,
                    'warehouse_id' => $itemdetail->warehouse_id,
                    'harga_beli' => $itemdetail->harga_beli,
                    'qty' => $itemdetail->qty,
                    'total_persediaan' => ($itemdetail->qty * $itemdetail->harga_beli),
                    'qty_susut' => $value['qty_susut'],
                    'total_susut' => ($value['qty_susut'] * $itemdetail->harga_beli),
                    'tanggal_so' => $this->tanggal
                ];
                StoreStockOpname::create($data);
            }
        }
    }
}