<?php

namespace App\Classes;

use App\Model\StoreItem;
use App\Model\StoreItemCard;
use App\Model\StoreItemDetail;
use App\Model\StoreItemUpload;
use App\Model\StorePurchase;
use App\Model\StorePurchaseDebt;
use App\Model\StorePurchaseDebtHistory;
use App\Model\StorePurchaseDetail;
use App\Model\StorePurchaseRetur;
use App\Model\StorePurchaseTransaction;
use App\Model\StoreSale;
use App\Model\StoreSaleDebt;
use App\Model\StoreSaleDebtHistori;
use App\Model\StoreSaleDebtHistoryUpload;
use App\Model\StoreSaleDetail;
use App\Model\StoreSaleRetur;
use App\Model\StoreSaleTransaction;
use App\Model\StoreStockOpname;
use App\Model\StoreSuplier;
use App\Model\StoreWarehouse;
use App\Model\StoreWarehouseUser;
use App\Model\StoreStockOpnameHistory;
use Illuminate\Support\Facades\DB;

class StoreClass
{
    public $error = '', $last_purchase_id = 0, $last_item_id = 0, $last_sale_id = 0, $last_sale_debt_id = 0, $saldo_card = 0, $stocks = [], $jumlah_retur = 0;
    public function __construct()
    {
        DB::enableQueryLog();
        $this->accountancy = new AccountancyClass();
    }
    public function getQty($data)
    {
        $query = StoreItemDetail::query();
        if (isset($data['warehouse_id'])) {
            $query->where('warehouse_id', $data['warehouse_id']);
        }
        if (isset($data['item_id'])) {
            $query->where('item_id', $data['item_id']);
        }
        $hasil = $query->sum('qty');
        return $hasil;
    }



    /*
    * =============================================================================================== START ITEM CATEGORY ===============================================================================================
    */
    public function suplierList($data = [], $limit = false, $order = ['created_at', 'asc'], $paginate = true)
    {
        //start query
        $query = StoreSuplier::query()->with(['item', 'utangHistori']);
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("name", "like", "%{$data['q']}%")
                    ->orWhere("code", "like", "%{$data['q']}%")
                    ->orWhere("phone", "like", "%{$data['q']}%")
                    ->orWhere("address", "like", "%{$data['q']}%");
            });
        }
        //order query
        $query->orderBy($order[0], $order[1]);
        //limit
        if ($limit != false) {
            if ($paginate == true) {
                $result = $query->paginate($limit);
            } else {
                $result = $query->limit($limit)->get();
            }
        } else {
            $result = $query->get();
        }

        return $result;
    }
    public function suplierGet($data)
    {
        //if $data not array
        if (!is_array($data)) {
            $query = StoreSuplier::with(['item', 'utangHistori'])->findOrFail($data);
        } else {
            $query = StoreSuplier::where($data[0], $data[1])->with(['item', 'utangHistori'])->get()->first();
        }
        $result = $query;
        return $result;
    }
    public function suplierSave($data = [])
    {
        $data['created_by'] = $data['updated_by'] = auth()->user()->id;
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');

        if ($this->suplierGet(['code', $data['code']])) {
            $this->error = 'Kode suplier sudah digunakan.';
            return false;
        }

        $account = [
            'parent_id' => 9,
            'name' => 'Hutang kedelai pada ' . $data['name'],
            'type' => 1,
            'group_id' => 20
        ];

        if (!$this->accountancy->accountSave($account)) {
            $this->error = $this->accountancy->error;
            return false;
        }
        $data['account_code'] = $this->accountancy->accountGet($this->accountancy->last_account_id)->code;

        StoreSuplier::create($data);
        return true;
    }
    public function suplierUpdate($id, $data = [])
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['updated_by'] = auth()->user()->id;

        $suplier = $this->suplierGet($id);
        if ($suplier->code != $data['code']) {
            if ($this->suplierGet(['code', $data['code']])) {
                $this->error = 'Kode suplier sudah digunakan';
                return false;
            }
        }

        $akun = $this->accountancy->accountGet(['code', $suplier->account_code]);
        $update_akun = [
            'name' => 'Hutang kedelai pada ' . $data['name']
        ];
        $this->accountancy->accountUpdate($akun->id, $update_akun);

        $suplier->update($data);
        return true;
    }
    /*
    * =============================================================================================== END ITEM CATEGORY ===============================================================================================
    */



    /*
    * =============================================================================================== START WAREHOUSE ===============================================================================================
    */
    public function warehouseList($data = [], $limit = false, $order = ['created_at', 'asc'], $paginate = true)
    {
        //start query
        $query = StoreWarehouse::query();
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("name", "like", "%{$data['q']}%")
                    ->orWhere("code", "like", "%{$data['q']}%")
                    ->orWhere("cp", "like", "%{$data['q']}%")
                    ->orWhere("phone", "like", "%{$data['q']}%")
                    ->orWhere("address", "like", "%{$data['q']}%");
            });
        }
        //order query
        $query->orderBy($order[0], $order[1]);
        //limit
        if ($limit != false) {
            if ($paginate == true) {
                $result = $query->paginate($limit);
            } else {
                $result = $query->limit($limit)->get();
            }
        } else {
            $result = $query->get();
        }

        return $result;
    }
    public function warehouseGet($data)
    {
        //if $data not array
        if (!is_array($data)) {
            $query = StoreWarehouse::find($data);
        } else {
            $query = StoreWarehouse::where($data[0], $data[1])->get()->first();
        }
        $result = $query;
        return $result;
    }
    public function warehouseSave($data = [])
    {
        $data['created_by'] = $data['updated_by'] = auth()->user()->id;
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');

        if ($this->warehouseGet(['code', $data['code']])) {
            $this->error = 'Kode gudang sudah digunakan.';
            return false;
        }

        StoreWarehouse::create($data);
        return true;
    }
    public function warehouseUpdate($id, $data = [])
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['updated_by'] = auth()->user()->id;

        $wh = $this->warehouseGet($id);
        if ($wh->code != $data['code']) {
            if ($this->warehouseGet(['code', $data['code']])) {
                $this->error = 'Kode gudang sudah digunakan';
                return false;
            }
        }
        $wh->update($data);
        return true;
    }
    /*
    * =============================================================================================== END WAREHOUSE ===============================================================================================
    */



    /*
    * =============================================================================================== START WAREHOUSE USER ===============================================================================================
    */
    public function warehouseUserList($data = [], $limit = false, $order = ['created_at', 'asc'], $paginate = true)
    {
        //start query
        $query = StoreWarehouseUser::query();
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->orWhereHas("user", function ($q) use ($data) {
                    $q->where('name', 'like', "%{$data['q']}%")
                        ->orWhere('email', 'like', "%{$data['q']}%")
                        ->orWhere('username', 'like', "%{$data['q']}%");
                });
                $q->orWhereHas("warehouse", function ($q) use ($data) {
                    $q->where('name', 'like', "%{$data['q']}%");
                });
            });
        }
        //order query
        $query->orderBy($order[0], $order[1]);
        //limit
        if ($limit != false) {
            if ($paginate == true) {
                $result = $query->paginate($limit);
            } else {
                $result = $query->limit($limit)->get();
            }
        } else {
            $result = $query->get();
        }

        return $result;
    }
    public function warehouseUserGet($data)
    {
        //if $data not array
        if (!is_array($data)) {
            $query = StoreWarehouseUser::find($data);
        } else {
            $query = StoreWarehouseUser::where($data[0], $data[1])->get()->first();
        }
        $result = $query;
        return $result;
    }
    public function warehouseUserSave($data = [])
    {
        $data['created_by'] = $data['updated_by'] = auth()->user()->id;
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');

        StoreWarehouseUser::create($data);
        return true;
    }
    /*
    * =============================================================================================== END WAREHOUSE USER ===============================================================================================
    */



    /*
    * =============================================================================================== START ITEM ===============================================================================================
    */
    public function itemList($data = [], $limit = false, $order = ['created_at', 'asc'], $paginate = true)
    {
        //start query
        $query = StoreItem::query();
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("name", "like", "%{$data['q']}%")
                    ->orWhere("code", "like", "%{$data['q']}%");
            });
        }
        //order query
        $query->orderBy($order[0], $order[1]);
        //limit
        if ($limit != false) {
            if ($paginate == true) {
                $result = $query->paginate($limit);
            } else {
                $result = $query->limit($limit)->get();
            }
        } else {
            $result = $query->get();
        }

        return $result;
    }
    public function itemGet($data)
    {
        //if $data not array
        if (!is_array($data)) {
            $query = StoreItem::find($data);
        } else {
            $query = StoreItem::where($data[0], $data[1])->get()->first();
        }
        $result = $query;
        return $result;
    }
    public function itemSave($data = [])
    {
        $data['created_by'] = $data['updated_by'] = auth()->user()->id;
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');

        if ($this->itemGet(['code', $data['code']])) {
            $this->error = 'Kode barang sudah digunakan.';
            return false;
        }
        $item = StoreItem::create($data);
        $this->last_item_id = $item->id;
        return true;
    }
    public function itemUpdate($id, $data = [])
    {
        $data['updated_by'] = auth()->user()->id;
        $data['updated_at'] = date('Y-m-d H:i:s');
        $item = $this->itemGet($id);
        if ($item->code != $data['code']) {
            if ($this->itemGet(['code', $data['code']])) {
                $this->error = 'Kode barang sudah digunakan.';
                return false;
            }
        }
        $item->update($data);
        return true;
    }
    public function calculateItem($id)
    {
        $item = $this->itemGet($id);
        $update['updated_at'] = date('Y-m-d H:i:s');
        $update['updated_by'] = auth()->user()->id;
        $update['qty'] = $item->detail->sum('qty');
        $item->update($update);
        return true;
    }
    public function itemPenambahan($data = [])
    {
        $query = StorePurchaseDetail::query()->with('purchase');
        $query->whereHas("purchase", function ($query) use ($data) {
            if (isset($data['start_date'])) {
                $query->where('tanggal_beli', '>=', $data['start_date'] . ' 00:00:00');
            }
            if (isset($data['end_date'])) {
                $query->where('tanggal_beli', '<=', $data['end_date'] . ' 23:59:59');
            }
        });
        $hasil = $query->sum('qty');
        return $hasil;
    }
    public function itemPengurangan($data = [])
    {
        $query = StoreSaleDetail::query()->with('sale');
        $query->whereHas("sale", function ($query) use ($data) {
            if (isset($data['start_date'])) {
                $query->where('tanggal_jual', '>=', $data['start_date'] . ' 00:00:00');
            }
            if (isset($data['end_date'])) {
                $query->where('tanggal_jual', '<=', $data['end_date'] . ' 23:59:59');
            }
        });
        $hasil = $query->sum('qty');
        return $hasil;
    }
    /*
    * =============================================================================================== END ITEM ===============================================================================================
    */



    /*
    * =============================================================================================== START ITEM DETAIL ===============================================================================================
    */
    public function itemDetailList($data = [], $limit = false, $order = ['created_at', 'asc'], $paginate = true)
    {
        //start query
        $query = StoreItemDetail::query()->with(['warehouse', 'suplier', 'userInput', 'item']);
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->whereHas("userInput", function ($q) use ($data) {
                    $q->where('name', 'like', "%{$data['q']}%")
                        ->orWhere('email', 'like', "%{$data['q']}%");
                });
            });
        }
        if (isset($data['warehouse_id']) && $data['warehouse_id'] != 'all') {
            $query->where('warehouse_id', $data['warehouse_id']);
        }
        if (isset($data['suplier_id']) && $data['suplier_id'] != 'all') {
            $query->where('suplier_id', $data['suplier_id']);
        }
        if (isset($data['item_id']) && $data['item_id'] != 'all') {
            $query->where('item_id', $data['item_id']);
        }
        if (isset($data['so']) && $data['so'] != 'all') {
            $query->where('so', $data['so']);
        }
        if (isset($data['stok'])) {
            if ($data['stok'] == 'ada') {
                $query->where('qty', '>', 0);
            }
            if ($data['stok'] == 'kosong') {
                $query->where('qty', 0);
            }
        }
        //order query
        $query->orderBy($order[0], $order[1]);
        //limit
        if ($limit != false) {
            if ($paginate == true) {
                $result = $query->paginate($limit);
            } else {
                $result = $query->limit($limit)->get();
            }
        } else {
            $result = $query->get();
        }

        return $result;
    }
    public function itemDetailGet($data)
    {
        //if $data not array
        if (!is_array($data)) {
            $query = StoreItemDetail::find($data);
        } else {
            $query = StoreItemDetail::where($data[0], $data[1])->get()->first();
        }
        $result = $query;
        return $result;
    }
    public function itemDetailSave($data)
    {
        $data['created_by'] = $data['updated_by'] = auth()->user()->id;
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');
        StoreItemDetail::create($data);
        return true;
    }
    public function itemDetailSale($id, $wh_id = 0)
    {
        $query = StoreItemDetail::where('item_id', $id)->where('qty', '>', 0)->where('warehouse_id', $wh_id)->first();
        return $query;
    }
    /*
    * =============================================================================================== END ITEM DETAIL ===============================================================================================
    */



    /*
    * =============================================================================================== START ITEM UPLOAD ===============================================================================================
    */
    public function itemUploadList($data = [], $limit = false, $order = ['code', 'asc'], $paginate = true)
    {
        //start query
        $query = StoreItemUpload::query()->with(['suplier']);
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("name", "like", "%{$data['q']}%")
                    ->orWhere("code", "like", "%{$data['q']}%");
            });
        }
        if (isset($data['suplier_id']) && $data['suplier_id'] != 'all') {
            $query->where('suplier_id', $data['suplier_id']);
        }
        //order query
        $query->orderBy($order[0], $order[1]);
        //limit
        if ($limit != false) {
            if ($paginate == true) {
                $result = $query->paginate($limit);
            } else {
                $result = $query->limit($limit)->get();
            }
        } else {
            $result = $query->get();
        }

        return $result;
    }
    public function itemUploadConfirm($conf)
    {
        if ($conf == 0) {
            StoreItemUpload::truncate();
        } else {
            $data = $this->itemUploadList();
            foreach ($data as $key => $value) {
                $qty_gudang = json_decode($value->qty_gudang);
                $item = [
                    'code' => $value->code,
                    'name' => $value->name,
                    'harga_jual' => $value->harga_jual,
                    'qty' => $value->qty_pusat
                ];
                foreach ($qty_gudang as $hsl => $hasil) {
                    $item['qty'] += $hasil;
                }
                if ($this->itemSave($item)) {
                    $item_id = $this->last_item_id;
                    $detail = [];
                    if ($value->qty_pusat > 0) {
                        $detail[] = [
                            'created_by' => auth()->user()->id,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => auth()->user()->id,
                            'item_id' => $item_id,
                            'warehouse_id' => 0,
                            'suplier_id' => $value->suplier_id,
                            'tanggal_masuk' => $value->tanggal_beli,
                            'tanggal_kadaluarsa' => $value->tanggal_kadaluarsa,
                            'harga_beli' => $value->harga_beli,
                            'qty_awal' => $value->qty_pusat,
                            'qty' => $value->qty_pusat,
                            'total' => $value->qty_pusat * $value->harga_beli
                        ];
                    }
                    foreach ($qty_gudang as $hsl => $hasil) {
                        if ($hasil > 0) {
                            $detail[] = [
                                'created_by' => auth()->user()->id,
                                'updated_by' => auth()->user()->id,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                                'item_id' => $item_id,
                                'warehouse_id' => $hsl,
                                'suplier_id' => $value->suplier_id,
                                'tanggal_masuk' => $value->tanggal_beli,
                                'tanggal_kadaluarsa' => $value->tanggal_kadaluarsa,
                                'harga_beli' => $value->harga_beli,
                                'qty_awal' => $hasil,
                                'qty' => $hasil,
                                'total' => $hasil * $value->harga_beli
                            ];
                        }
                    }
                    if ($item['qty'] > 0) {
                        StoreItemDetail::insert($detail);
                        foreach ($detail as $hsl => $hasil) {
                            $gudang = $this->warehouseGet($hasil['warehouse_id']);
                            $card_header = [
                                'item_id' => $hasil['item_id'],
                                'warehouse_id' => $hasil['warehouse_id'],
                                'tanggal_transaksi' => $hasil['tanggal_masuk'] . date(' H:i:s'),
                                'no_ref' => 'TRX-' . date('YmdHis'),
                                'qty' => $hasil['qty'],
                                'keterangan' => 'Persediaan awal ' . ($gudang->name ?? 'Pusat'),
                                'tipe' => 0,
                                'masuk' => ($hasil['total'])
                            ];
                            StoreItemCard::create($card_header);
                        }
                    }
                }
            }
            StoreItemUpload::truncate();
        }
        return true;
    }
    /*
    * =============================================================================================== END ITEM UPLOAD ===============================================================================================
    */



    /*
    * =============================================================================================== START PURCHASE ===============================================================================================
    */
    public function purchaseList($data = [], $limit = false, $order = ['tanggal_beli', 'desc'], $paginate = true)
    {
        //start query
        $query = StorePurchase::query();
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("no_faktur", "like", "%{$data['q']}%")
                    ->orWhere("note", "like", "%{$data['q']}%");
            });
        }
        if (isset($data['suplier_id']) && $data['suplier_id'] != 'all') {
            $query->where('suplier_id', $data['suplier_id']);
        }
        if (isset($data['status']) && $data['status'] != 'all') {
            $query->where('status', $data['status']);
        }
        //order query
        $query->orderBy($order[0], $order[1]);
        //limit
        if ($limit != false) {
            if ($paginate == true) {
                $result = $query->paginate($limit);
            } else {
                $result = $query->limit($limit)->get();
            }
        } else {
            $result = $query->get();
        }

        return $result;
    }
    public function purchaseFactur()
    {
        $data = StorePurchase::latest('no_faktur')->pluck('no_faktur');
        $result = ($data[0] ?? 0) + 1;
        return $result;
    }
    public function purchaseGet($data)
    {
        //if $data not array
        if (!is_array($data)) {
            $query = StorePurchase::find($data);
        } else {
            $query = StorePurchase::where($data[0], $data[1])->get()->first();
        }
        $result = $query;
        return $result;
    }
    public function purchaseSave($data)
    {
        $data['created_by'] = $data['updated_by'] = auth()->user()->id;
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');
        $barang = $data['barang'];
        $warehouse_id = $data['warehouse_id'];
        unset($data['barang'], $data['warehouse_id']);

        $purchase = StorePurchase::create($data);
        $last_purchase_id = $this->last_purchase_id = $purchase->id;
        $i = 0;
        foreach ($barang as $key => $value) {
            $qty = str_replace(',', '', $value->qty);
            $susut = str_replace(',', '', $value->susut);
            $harga_beli_satuan = str_replace(',', '', $value->harga_beli_satuan);
            $detail[$i] = [
                'created_by' => $data['created_by'],
                'updated_by' => $data['updated_by'],
                'created_at' => $data['created_at'],
                'updated_at' => $data['updated_at'],
                'item_id' => $value->item_id,
                'purchase_id' => $last_purchase_id,
                'harga_beli' => $harga_beli_satuan,
                'qty' => $qty,
                'total' => $value->harga_total_satuan,
                'tanggal_kadaluarsa' => date('Y-m-d'),
                'qty_susut' => $susut,
                'total_susut' => $susut * $harga_beli_satuan
            ];
            $item[$i] = [
                'created_by' => $data['created_by'],
                'updated_by' => $data['updated_by'],
                'created_at' => $data['created_at'],
                'updated_at' => $data['updated_at'],
                'item_id' => $value->item_id,
                'qty_awal' => $qty,
                'qty' => $qty,
                'suplier_id' => $data['suplier_id'],
                'tanggal_masuk' => date('Y-m-d', strtotime($data['tanggal_beli'])),
                'harga_beli' => $harga_beli_satuan,
                'tanggal_kadaluarsa' => date('Y-m-d'),
                'purchase_id' => $last_purchase_id,
                'total' => $value->harga_total_satuan,
                'warehouse_id' => $warehouse_id,
                'qty_susut' => $susut,
                'total_susut' => $susut * $harga_beli_satuan
            ];
            $card[$i] = [
                'created_by' => $data['created_by'],
                'updated_by' => $data['updated_by'],
                'created_at' => $data['created_at'],
                'updated_at' => $data['updated_at'],
                'item_id' => $value->item_id,
                'warehouse_id' => $warehouse_id,
                'tanggal_transaksi' => date('Y-m-d', strtotime($data['tanggal_beli'])),
                'no_ref' => $data['ref_number'],
                'qty' => $qty,
                'keterangan' => empty($data['note']) ? 'Pembelian baru' : $data['note'],
                'tipe' => 0,
                'masuk' => $value->harga_total_satuan
            ];
            $i++;
        }
        StorePurchaseDetail::insert($detail);
        StoreItemDetail::insert($item);
        StoreItemCard::insert($card);


        foreach ($barang as $key => $value) {
            $this->calculateItem($value->item_id);
        }
        if ($data['total_bayar'] > 0) {
            $transaction = [
                'purchase_id' => $last_purchase_id,
                'ref_number' => $data['ref_number'],
                'suplier_id' => $data['suplier_id'],
                'transaction_date' => $data['tanggal_beli'],
                'total' => $data['total_bayar'],
                'note' => $data['note']
            ];
            $this->purchaseTransactionSave($transaction);
        }

        return true;
    }
    /*
    * =============================================================================================== END PURCHASE ===============================================================================================
    */



    /*
    * =============================================================================================== START PURCHASE TRANSACTION ===============================================================================================
    */
    public function purchaseTransactionSave($data)
    {
        $data['created_by'] = $data['updated_by'] = auth()->user()->id;
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');

        StorePurchaseTransaction::create($data);
        return true;
    }
    /*
    * =============================================================================================== END PURCHASE TRANSACTION ===============================================================================================
    */



    /*
    * =============================================================================================== START PURCHASE RETUR ===============================================================================================
    */
    public function purchaseReturList($data = [], $limit = false, $order = ['tanggal_retur', 'asc'], $paginate = true)
    {
        //start query
        $query = StorePurchaseRetur::query()->with(['suplier', 'item']);
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("no_retur", "like", "%{$data['q']}%")
                    ->orWhere("note", "like", "%{$data['q']}%");
            });
        }
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->whereHas("suplier", function ($q) use ($data) {
                    $q->where('code', 'like', "%{$data['q']}%")
                        ->orWhere('name', 'like', "%{$data['q']}%");
                });
                $q->orWhereHas("item", function ($q) use ($data) {
                    $q->where('name', 'like', "%{$data['q']}%")
                        ->orWhere('code', 'like', "%{$data['q']}%");
                });
            });
        }
        //order query
        $query->orderBy($order[0], $order[1]);
        //limit
        if ($limit != false) {
            if ($paginate == true) {
                $result = $query->paginate($limit);
            } else {
                $result = $query->limit($limit)->get();
            }
        } else {
            $result = $query->get();
        }

        return $result;
    }
    public function purchaseReturSave($data)
    {
        $data['created_by'] = $data['updated_by'] = auth()->user()->id;
        $barang = $this->itemDetailGet($data['id']);
        unset($data['id']);
        $qty = $barang->qty - $data['qty'];
        $update_barang = [
            'qty' => $qty,
            'total' => $barang->harga_beli * $qty
        ];
        $barang->update($update_barang);
        StorePurchaseRetur::create($data);
        $this->calculateItem($data['item_id']);
        $card = [
            'item_id' => $data['item_id'],
            'warehouse_id' => 0,
            'tanggal_transaksi' => date('Y-m-d', strtotime($data['tanggal_retur'])),
            'no_ref' => $data['no_retur'],
            'qty' => $data['qty'],
            'keterangan' => 'Retur stok ' . $barang->item->name . ($data['note'] ? '(' . $data['note'] . ')' : ''),
            'tipe' => 1,
            'keluar' => $data['total']
        ];
        $this->itemCardSave($card);
        return true;
    }
    /*
    * =============================================================================================== END PURCHASE RETUR ===============================================================================================
    */



    /*
    * =============================================================================================== START PURCHASE DEBT ===============================================================================================
    */
    public function purchaseDebtList($data = [], $limit = false, $order = ['created_at', 'desc'], $paginate = true)
    {
        //start query
        $query = StorePurchaseDebt::query()->with(['suplier', 'purchase']);
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("no_faktur", "like", "%{$data['q']}%")
                    ->orWhere("note", "like", "%{$data['q']}%");
            });
        }
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->whereHas("suplier", function ($q) use ($data) {
                    $q->where('code', 'like', "%{$data['q']}%")
                        ->orWhere('name', 'like', "%{$data['q']}%");
                });
                $q->orWhereHas("purchase", function ($q) use ($data) {
                    $q->where('no_faktur', 'like', "%{$data['q']}%")
                        ->orWhere('ref_number', 'like', "%{$data['q']}%")
                        ->orWhere('note', 'like', "%{$data['q']}%");
                });
            });
        }
        if (isset($data['status']) && $data['status'] != 'all') {
            $query->where('status', $data['status']);
        }
        //order query
        $query->orderBy($order[0], $order[1]);
        //limit
        if ($limit != false) {
            if ($paginate == true) {
                $result = $query->paginate($limit);
            } else {
                $result = $query->limit($limit)->get();
            }
        } else {
            $result = $query->get();
        }

        return $result;
    }
    public function purchaseDebtGet($data)
    {
        //if $data not array
        if (!is_array($data)) {
            $query = StorePurchaseDebt::find($data);
        } else {
            $query = StorePurchaseDebt::where($data[0], $data[1])->get()->first();
        }
        $result = $query;
        return $result;
    }
    public function purchaseDebtSave($data)
    {
        $data['created_by'] = $data['updated_by'] = auth()->user()->id;
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');

        StorePurchaseDebt::create($data);
        return true;
    }
    /*
    * =============================================================================================== END PURCHASE DEBT ===============================================================================================
    */



    /*
    * =============================================================================================== START PURCHASE DEBT HISTORY ===============================================================================================
    */
    public function purchaseDebtHistoryList($data = [], $limit = false, $order = ['trxdate', 'asc'], $paginate = true)
    {
        //start query
        $query = StorePurchaseDebtHistory::query();
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("no_ref", "like", "%{$data['q']}%")
                    ->orWhere("note", "like", "%{$data['q']}%");
            });
        }
        if (isset($data['suplier_id']) && $data['suplier_id'] != 'all') {
            $query->where('suplier_id', $data['suplier_id']);
        }
        if (isset($data['start_date']) && !empty($data['start_date'])) {
            $query->where('trxdate', '>=', $data['start_date'] . ' 00:00:00');
        }
        if (isset($data['end_date']) && !empty($data['end_date'])) {
            $query->where('trxdate', '<=', $data['end_date'] . ' 23:59:59');
        }
        //order query
        $query->orderBy($order[0], $order[1]);
        //limit
        if ($limit != false) {
            if ($paginate == true) {
                $result = $query->paginate($limit);
            } else {
                $result = $query->limit($limit)->get();
            }
        } else {
            $result = $query->get();
        }

        return $result;
    }
    public function purchaseDebtHistorySave($data)
    {
        $data['created_by'] = $data['updated_by'] = auth()->user()->id;

        StorePurchaseDebtHistory::create($data);
        return true;
    }
    /*
    * =============================================================================================== END PURCHASE DEBT HISTORY ===============================================================================================
    */



    /*
    * =============================================================================================== START SALE ===============================================================================================
    */
    public function saleList($data = [], $limit = false, $order = ['tanggal_jual', 'desc'], $paginate = true)
    {
        //start query
        $query = StoreSale::query()->with(['member', 'detail', 'warehouse']);
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("no_faktur", "like", "%{$data['q']}%")
                    ->orWhere("note", "like", "%{$data['q']}%")
                    ->orWhere("no_faktur", "like", "%{$data['q']}%");
            });
        }
        if (isset($data['warehouse_id']) && $data['warehouse_id'] != 'all') {
            $query->where('warehouse_id', $data['warehouse_id']);
        }
        if (isset($data['member_id']) && !empty($data['member_id'])) {
            $query->where('member_id', $data['member_id']);
        }
        if (isset($data['status']) && $data['status'] != 'all') {
            $query->where('status_pembayaran', $data['status']);
        }
        if (isset($data['start_date']) && !empty($data['start_date'])) {
            $query->where('tanggal_jual', '>=', $data['start_date'] . ' 00:00:00');
        }
        if (isset($data['end_date']) && !empty($data['end_date'])) {
            $query->where('tanggal_jual', '<=', $data['end_date'] . ' 23:59:59');
        }
        $query->whereHas("member", function ($q) use ($data) {
            if (isset($data['shu'])) {
                $q->where('status', 1);
            }
        });
        //order query
        $query->orderBy($order[0], $order[1]);
        //limit
        if ($limit != false) {
            if ($paginate == true) {
                $result = $query->paginate($limit);
            } else {
                $result = $query->limit($limit)->get();
            }
        } else {
            $result = $query->get();
        }

        return $result;
    }
    public function saleGet($data)
    {
        //if $data not array
        if (!is_array($data)) {
            $query = StoreSale::find($data);
        } else {
            $query = StoreSale::where($data[0], $data[1])->get()->first();
        }
        $result = $query;
        return $result;
    }
    public function saleSave($data)
    {
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $data['updated_by'] = auth()->user()->id;
        $barang = json_decode($data['barang']);
        unset($data['barang']);

        $sale = StoreSale::create($data);
        $last_sale_id = $this->last_sale_id = $sale->id;
        foreach ($barang as $key => $value) {
            $value->sale_id = $last_sale_id;
            $value->member_id = $data['member_id'];
            $value->qty = str_replace(',', '', $value->qty);
            $this->saleDetailSave((array) $value, $data);
        }
        if ($data['total_bayar'] > 0) {
            $transaksi = [
                'sale_id' => $last_sale_id,
                'member_id' => $data['member_id'],
                'warehouse_id' => $data['warehouse_id'],
                'total' => $data['total_bayar'],
                'transaction_date' => $data['tanggal_jual'],
                'ref_number' => $data['ref_number'],
                'note' => $data['note'],
            ];
            $this->saleTransactionSave($transaksi);
        }
        return true;
    }
    /*
    * =============================================================================================== END SALE ===============================================================================================
    */



    /*
    * =============================================================================================== START SALE DETAIL ===============================================================================================
    */
    public function saleDetailList($data = [], $limit = false, $order = false, $paginate = true)
    {
        //start query
        $query = StoreSaleDetail::query()->with(['item', 'sale']);
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("no_faktur", "like", "%{$data['q']}%")
                    ->orWhere("note", "like", "%{$data['q']}%")
                    ->orWhere("no_faktur", "like", "%{$data['q']}%");
            });
        }
        if (isset($data['item_id']) && !empty($data['item_id']) && $data['item_id'] != 'all') {
            $query->where('item_id', $data['item_id']);
        }
        $query->whereHas("sale", function ($q) use ($data) {
            if (isset($data['status'])) {
                $q->where('status', $data['status']);
            }
            if (isset($data['start_date']) && !empty($data['start_date'])) {
                $q->where('tanggal_jual', '>=', $data['start_date'] . ' 00:00:00');
            }
            if (isset($data['end_date']) && !empty($data['end_date'])) {
                $q->where('tanggal_jual', '<=', $data['end_date'] . ' 23:59:59');
            }
            $q->whereHas("member", function ($m) use ($data) {
                if (isset($data['member']) && $data['member'] != 'all') {
                    $m->where('status', $data['member']);
                }
            });
            $q->orderBy('tanggal_jual', 'asc');
        });
        //order query
        if ($order) {
            $query->orderBy($order[0], $order[1]);
        }
        //limit
        if ($limit != false) {
            if ($paginate == true) {
                $result = $query->paginate($limit);
            } else {
                $result = $query->limit($limit)->get();
            }
        } else {
            $result = $query->get();
        }

        return $result;
    }
    public function saleDetailGet($data)
    {
        //if $data not array
        if (!is_array($data)) {
            $query = StoreSaleDetail::find($data);
        } else {
            $query = StoreSaleDetail::where($data[0], $data[1])->get()->first();
        }
        $result = $query;
        return $result;
    }
    public function saleDetailSave($data, $other = [])
    {
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $data['updated_by'] = auth()->user()->id;
        unset($data['code'], $data['name']);

        $data['qty'] = str_replace(',', '', $data['qty']);
        $data['harga_jual'] = str_replace(',', '', $data['harga_jual']);

        $this->updateItemDetail($data['item_id'], $data['qty'], 0, [], $other['warehouse_id']);
        $data['stocks'] = json_encode($this->stocks);
        StoreSaleDetail::create($data);
        $saldo = $this->saldo_card;

        $card = [
            'item_id' => $data['item_id'],
            'warehouse_id' => $other['warehouse_id'],
            'tanggal_transaksi' => date('Y-m-d', strtotime($other['tanggal_jual'])),
            'no_ref' => $other['ref_number'],
            'qty' => $data['qty'],
            'keterangan' => empty($other['note']) ? 'Penjualan' : $other['note'],
            'tipe' => 1,
            'keluar' => $saldo
        ];
        StoreItemCard::create($card);
        $this->calculateItem($data['item_id']);
        return true;
    }
    public function updateItemDetail($item_id, $qty_jual, $total_penjualan, $stocks, $wh_id = 0)
    {
        $data = [
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => auth()->user()->id
        ];
        $item = $this->itemDetailSale($item_id, $wh_id);
        $qty = $item->qty - $qty_jual;
        $repeat = false;
        if ($qty < 0) {
            $data['qty'] = 0;
            $repeat = true;
            $qty = $qty_jual - $item->qty;
            $qty_stocks = $item->qty;
        } else {
            $data['qty'] = $qty;
            $qty_stocks = $qty_jual;
        }

        $data['total'] = $item->harga_beli * $data['qty'];
        $total_penjualan += $item->total - $data['total'];
        $item->update($data);

        $stocks[] = [
            'item_detail_id' => $item->id,
            'harga_beli' => $item->harga_beli,
            'qty' => $qty_stocks,
            'qty_retur' => 0,
        ];
        if ($repeat) {
            $this->updateItemDetail($item_id, $qty, $total_penjualan, $stocks, $wh_id);
        } else {
            $this->stocks = $stocks;
            $this->saldo_card = $total_penjualan;
        }
        return true;
    }
    /*
    * =============================================================================================== END SALE DETAIL ===============================================================================================
    */



    /*
    * =============================================================================================== START SALE DEBT ===============================================================================================
    */
    public function saleDebtList($data = [], $limit = false, $order = ['tanggal_transaksi', 'desc'], $paginate = true)
    {
        //start query
        $query = StoreSaleDebt::query()->with(['member', 'sale', 'warehouse']);
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->whereHas("warehouse", function ($q) use ($data) {
                    $q->where('code', 'like', "%{$data['q']}%")
                        ->orWhere('name', 'like', "%{$data['q']}%");
                });
                $q->whereHas("member", function ($q) use ($data) {
                    $q->where('code', 'like', "%{$data['q']}%")
                        ->orWhere('name', 'like', "%{$data['q']}%");
                });
                $q->orWhereHas("sale", function ($q) use ($data) {
                    $q->where('no_faktur', 'like', "%{$data['q']}%")
                        ->orWhere('ref_number', 'like', "%{$data['q']}%")
                        ->orWhere('note', 'like', "%{$data['q']}%");
                });
            });
        }
        if (isset($data['status']) && $data['status'] != 'all') {
            $query->where('status', $data['status']);
        }
        if (isset($data['warehouse_id']) && $data['warehouse_id'] != 'all') {
            $query->where('warehouse_id', $data['warehouse_id']);
        }
        if (isset($data['member_id']) && !empty($data['member_id'])) {
            $query->where('member_id', $data['member_id']);
        }
        //order query
        $query->orderBy($order[0], $order[1]);
        //limit
        if ($limit != false) {
            if ($paginate == true) {
                $result = $query->paginate($limit);
            } else {
                $result = $query->limit($limit)->get();
            }
        } else {
            $result = $query->get();
        }

        return $result;
    }
    public function saleDebtGet($data)
    {
        //if $data not array
        if (!is_array($data)) {
            $query = StoreSaleDebt::find($data);
        } else {
            $query = StoreSaleDebt::where($data[0], $data[1])->get()->first();
        }
        $result = $query;
        return $result;
    }
    public function saleDebtSave($data)
    {
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $data['updated_by'] = auth()->user()->id;

        $save = StoreSaleDebt::create($data);
        $this->last_sale_debt_id = $save->id;
        return true;
    }
    /*
    * =============================================================================================== END SALE DEBT ===============================================================================================
    */



    /*
    * =============================================================================================== START SALE DEBT HISTORY ===============================================================================================
    */
    public function saleDebtHistoryList($data = [], $limit = false, $order = ['trxdate', 'desc'], $paginate = true)
    {
        //start query
        $query = StoreSaleDebtHistori::query()->with(['member', 'region']);
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("no_ref", "like", "%{$data['q']}%")
                    ->orWhere("note", "like", "%{$data['q']}%");
            });
        }
        if (isset($data['member_stat']) && $data['member_stat'] != 'all') {
            $query->where('member_stat', $data['member_stat']);
        }
        if (isset($data['region_id']) && $data['region_id'] != 'all') {
            $query->where('region_id', $data['region_id']);
        }
        if (isset($data['member_id']) && !empty($data['member_id'])) {
            $query->where('member_id', $data['member_id']);
        }

        if (isset($data['start_date']) && !empty($data['start_date'])) {
            $query->where('trxdate', '>=', $data['start_date'] . ' 00:00:00');
        }
        if (isset($data['end_date']) && !empty($data['end_date'])) {
            $query->where('trxdate', '<=', $data['end_date'] . ' 23:59:59');
        }
        //order query
        $query->orderBy($order[0], $order[1]);
        //limit
        if ($limit != false) {
            if ($paginate == true) {
                $result = $query->paginate($limit);
            } else {
                $result = $query->limit($limit)->get();
            }
        } else {
            $result = $query->get();
        }

        return $result;
    }
    public function saleDebtHistoryGet($data)
    {
        //if $data not array
        if (!is_array($data)) {
            $query = StoreSaleDebtHistori::findOrFail($data);
        } else {
            $query = StoreSaleDebtHistori::where($data[0], $data[1])->get()->first();
        }
        $result = $query;
        return $result;
    }
    public function saleDebtHistorySave($data)
    {
        $data['created_by'] = $data['updated_by'] = auth()->user()->id;
        StoreSaleDebtHistori::create($data);
        return true;
    }
    /*
    * =============================================================================================== END SALE DEBT HISTORY ===============================================================================================
    */



    /*
    * =============================================================================================== START SALE TRANSACTION ===============================================================================================
    */
    public function saleTransactionSave($data)
    {
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $data['updated_by'] = auth()->user()->id;

        StoreSaleTransaction::create($data);
        return true;
    }
    /*
    * =============================================================================================== END SALE TRANSACTION ===============================================================================================
    */



    /*
    * =============================================================================================== START ITEM CARD ===============================================================================================
    */
    public function itemCardList($data = [], $limit = false, $order = ['tanggal_transaksi', 'asc'], $paginate = true)
    {
        //start query
        $query = StoreItemCard::query();
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("keterangan", "like", "%{$data['q']}%")
                    ->orWhere("no_ref", "like", "%{$data['q']}%");
            });
        }
        if (isset($data['warehouse_id']) && $data['warehouse_id'] != 'all') {
            $query->where('warehouse_id', $data['warehouse_id']);
        }
        if (isset($data['item_id']) && $data['item_id'] != 'all') {
            $query->where('item_id', $data['item_id']);
        }
        if (isset($data['start_date']) && !empty($data['start_date'])) {
            $query->where('tanggal_transaksi', '>=', $data['start_date']);
        }
        if (isset($data['date']) && !empty($data['date'])) {
            $query->where('tanggal_transaksi', $data['date']);
        }
        if (isset($data['end_date']) && !empty($data['end_date'])) {
            $query->where('tanggal_transaksi', '<=', $data['end_date']);
        }
        //order query
        $query->orderBy($order[0], $order[1]);
        //limit
        if ($limit != false) {
            if ($paginate == true) {
                $result = $query->paginate($limit);
            } else {
                $result = $query->limit($limit)->get();
            }
        } else {
            $result = $query->get();
        }

        return $result;
    }
    public function itemCardSave($data)
    {
        $data['created_by'] = $data['updated_by'] = auth()->user()->id;

        StoreItemCard::create($data);
        return true;
    }
    /*
    * =============================================================================================== END ITEM CARD ===============================================================================================
    */

    /*
    * =============================================================================================== START SALE RETUR ===============================================================================================
    */
    public function saleReturList($data = [], $limit = false, $order = ['tanggal_transaksi', 'desc'], $paginate = true)
    {
        //start query
        $query = StoreSaleRetur::query()->with(['member', 'sale', 'region', 'warehouse']);
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("no_ref", "like", "%{$data['q']}%")
                    ->orWhere("note", "like", "%{$data['q']}%");
                $q->orWhereHas("sale", function ($q) use ($data) {
                    $q->where('no_faktur', 'like', "%{$data['q']}%");
                });
                $q->orWhereHas("member", function ($q) use ($data) {
                    $q->where('name', 'like', "%{$data['q']}%")
                        ->orWhere('code', 'like', "%{$data['q']}%");
                });
                $q->orWhereHas("region", function ($q) use ($data) {
                    $q->where('name', 'like', "%{$data['q']}%")
                        ->orWhere('code', 'like', "%{$data['q']}%");
                });
            });
        }
        if (isset($data['region_id']) && $data['region_id'] != 'all') {
            $query->where('region_id', $data['region_id']);
        }
        if (isset($data['sale_id']) && $data['sale_id'] != 'all') {
            $query->where('sale_id', $data['sale_id']);
        }
        if (isset($data['warehouse_id']) && $data['warehouse_id'] != 'all') {
            $query->where('warehouse_id', $data['warehouse_id']);
        }
        //order query
        $query->orderBy($order[0], $order[1]);
        //limit
        if ($limit != false) {
            if ($paginate == true) {
                $result = $query->paginate($limit);
            } else {
                $result = $query->limit($limit)->get();
            }
        } else {
            $result = $query->get();
        }

        return $result;
    }
    public function saleReturSave($data)
    {
        $data['created_by'] = $data['updated_by'] = auth()->user()->id;
        $item = $this->saleDetailget($data['id']);

        unset($data['id']);

        $data['member_id'] = $item->sale->member_id;
        $data['sale_id'] = $item->sale_id;
        $data['region_id'] = $item->sale->region_id;
        $data['item_id'] = $item->item_id;
        $data['warehouse_id'] = $item->sale->warehouse_id;
        $data['jumlah'] = $item->harga_jual * $data['qty'];
        $data['harga'] = $item->harga_jual;
        $jumlah = 0;
        $stok = json_decode($item->stocks);
        $qty = $data['qty'];
        for ($i = (count($stok) - 1); $i >= 0; $i--) {
            $qty_tersedia = $stok[$i]->qty - $stok[$i]->qty_retur;
            if ($qty <= $qty_tersedia) {
                $jumlah += ($qty * $stok[$i]->harga_beli);
                $qty_retur = $qty;
                $qty -= $qty;
            } else {
                $jumlah += ($stok[$i]->harga_beli * $qty_tersedia);
                $qty_retur = $qty_tersedia;
                $qty -= $qty_tersedia;
            }
            $stok[$i]->qty_retur += $qty_retur;
            $item_detail = $this->itemDetailGet($stok[$i]->item_detail_id);
            $update_item_detail['qty'] = $item_detail->qty + $qty_retur;
            $update_item_detail['total'] = $qty_retur * $item_detail->harga_beli;
            $item_detail->update($update_item_detail);
        }
        StoreSaleRetur::create($data);
        $this->calculateItem($data['item_id']);
        $card = [
            'item_id' => $data['item_id'],
            'warehouse_id' => $data['warehouse_id'],
            'tanggal_transaksi' => date('Y-m-d', strtotime($data['tanggal_transaksi'])),
            'no_ref' => $data['no_ref'],
            'qty' => $data['qty'],
            'keterangan' => 'Retur penjualan ' . $item->sale->no_faktur,
            'tipe' => 0,
            'masuk' => $jumlah
        ];
        $this->itemCardSave($card);
        $update_sale_detail['qty_retur'] = $data['qty'];
        $update_sale_detail['stocks'] = json_encode($stok);
        $item->update($update_sale_detail);
        return true;
    }
    /*
    * =============================================================================================== START SALE RETUR ===============================================================================================
    */

    /*
    * =============================================================================================== START STOCK OPNAME ===============================================================================================
    */
    public function stockOpname($data = [], $limit = false, $order = ['code', 'desc'], $paginate = true)
    {
        $query = StoreStockOpname::query()->with(['itemdetail', 'warehouse']);
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("name", "like", "%{$data['q']}%")
                    ->orWhere("code", "like", "%{$data['q']}%")
                    ->orWhere("note", "like", "%{$data['q']}%");
            });
        }
        if (isset($data['warehouse_id']) && $data['warehouse_id'] != 'all') {
            $query->where('warehouse_id', $data['warehouse_id']);
        }
        //order query
        $query->orderBy($order[0], $order[1]);
        //limit
        if ($limit != false) {
            if ($paginate == true) {
                $result = $query->paginate($limit);
            } else {
                $result = $query->limit($limit)->get();
            }
        } else {
            $result = $query->get();
        }

        return $result;
    }
    public function stockOpnameConfirm($confirm)
    {
        if ($confirm == 1) {
            $config = config('config_apps');
            if (auth()->user()->isGudang()) {
                $wh_id = auth()->user()->getWarehouseId();
                $gudang = $this->warehouseGet($wh_id);
                $title = 'gudang ' . $gudang->name;
                $akun_so = $config['akun_so_gudang'];
            } else {
                $wh_id = 0;
                $title = 'pusat';
                $akun_so = $config['akun_so_pusat'];
            }
            $data = $this->stockOpname();
            $total_susut = $data->sum('total_susut');
            foreach ($data as $key => $value) {
                $item[$value->item_id]['jumlah'] = 0;
                $item[$value->item_id]['qty'] = 0;
                $tanggal = $value->tanggal_so;
            }
            $journal = [
                'transaction_date' => $tanggal . date(' H:i:s'),
                'reference_number' => 'TRXT-' . date('YmdHis'),
                'name' => 'Stock opname penyusutan di ' . $title,
                'type' => 1,
                'unit' => 2,
            ];
            $journal['detail'][] = [
                'account_code' => $akun_so,
                'type' => 'dana_from',
                'amount' => $total_susut
            ];
            $journal['detail'][] = [
                'account_code' => $config['akun_persediaan'],
                'type' => 'dana_to',
                'amount' => $total_susut
            ];
            if (!$this->accountancy->adjustingJournalSave($journal)) {
                $this->error = $this->accountancy->error;
                return false;
            }
            $this->accountancy->journalSave($journal);
            foreach ($data as $key => $value) {
                $item[$value->item_id]['jumlah'] += $value['total_susut'];
                $item[$value->item_id]['qty'] += $value['qty_susut'];
                $update_detail['qty'] = $value->itemdetail->qty - $value->qty_susut;
                $update_detail['total'] = $update_detail['qty'] * $value->itemdetail->harga_beli;
                $update_detail['so'] = 1;
                $value->itemdetail->update($update_detail);
            }
            foreach ($item as $key => $value) {
                $this->calculateItem($key);
                $card = [
                    'item_id' => $key,
                    'warehouse_id' => $wh_id,
                    'tanggal_transaksi' => $journal['transaction_date'],
                    'no_ref' => $journal['reference_number'],
                    'qty' => $value['qty'],
                    'keterangan' => 'Stock opname penyusutan di ' . $title,
                    'tipe' => 1,
                    'keluar' => $value['jumlah']
                ];
                $this->itemCardSave($card);
            }
            $histori = [
                'tanggal_so' => $journal['transaction_date'],
                'data' => json_encode($data->toArray()),
                'total_susut' => $total_susut,
                'warehouse_id' => $wh_id,
                'created_by' => auth()->user()->id
            ];
            StoreStockOpnameHistory::create($histori);
        }
        StoreStockOpname::truncate();
        return true;
    }
    /*
    * =============================================================================================== END STOCK OPNAME ===============================================================================================
    */

    /*
    * =============================================================================================== START ITEM UPLOAD ===============================================================================================
    */
    public function reportPiutangUploadList($data = [], $limit = false, $order = ['id', 'asc'], $paginate = true)
    {
        //start query
        $query = StoreSaleDebtHistoryUpload::query()->with(['member']);
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("note", "like", "%{$data['q']}%")
                    ->orWhere("no_ref", "like", "%{$data['q']}%");
            });
        }
        //order query
        $query->orderBy($order[0], $order[1]);
        //limit
        if ($limit != false) {
            if ($paginate == true) {
                $result = $query->paginate($limit);
            } else {
                $result = $query->limit($limit)->get();
            }
        } else {
            $result = $query->get();
        }

        return $result;
    }
    public function reportPiutangUploadConfirm($conf)
    {
        if ($conf == 1) {
            $data = $this->reportPiutangUploadList();
            foreach ($data as $key => $value) {
                $piutang = $value->toArray();
                $piutang['region_id'] = $piutang['member']['region_id'];
                $piutang['member_stat'] = $piutang['member']['status'];
                unset($piutang['id'], $piutang['member']);
                $this->saleDebtHistorySave($piutang);
            }
        }
        StoreSaleDebtHistoryUpload::truncate();
        return true;
    }
    /*
    * =============================================================================================== END ITEM UPLOAD ===============================================================================================
    */
}