<?php

namespace App\Http\Controllers;

use App\Classes\AccountancyClass;
use App\Classes\DepositClass;
use App\Classes\MasterClass;
use App\Classes\StoreClass;
use App\Classes\UserClass;
use App\Exports\ItemCardExport;
use App\Exports\ItemUploadFormatExport;
use App\Exports\StockOpnameExport;
use App\Exports\StoreReportPiutangDetailAnggotaExport;
use App\Exports\StoreReportPiutangDetailExport;
use App\Exports\StoreReportPiutangExport;
use App\Exports\StoreReportUtangDetailExport;
use App\Exports\StoreReportUtangExport;
use App\Exports\StoreSaleCashExport;
use App\Exports\StoreSaleDebtExport;
use App\Exports\StoreSaleItemStockExport;
use App\Exports\StoreSaleMemberExport;
use App\Exports\StoreSaleRegionExport;
use App\Http\Requests\PurchaseDebtRequest;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\PurchaseReturRequest;
use App\Http\Requests\RekapitulasiPiutangRequest;
use App\Http\Requests\RekapitulasiUtangRequest;
use App\Http\Requests\SaleDebtRequest;
use App\Http\Requests\SaleRequest;
use App\Http\Requests\SaleReturRequest;
use App\Http\Requests\UserRequest;
use App\Imports\ItemUploadImport;
use App\Imports\StockOpnameImport;
use App\Imports\StoreRekapitulasiPiutangImport;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use NumberToWords\NumberToWords;

class StoreController extends Controller
{
    private $store, $user, $master, $accountancy, $deposit;
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role');
        Config::set('title', 'Unit Toko');
        $this->store = new StoreClass();
        $this->user = new UserClass();
        $this->master = new MasterClass();
        $this->deposit = new DepositClass();
        $this->accountancy = new AccountancyClass();
    }



    /*
    * ========================================================================================== START KATEGORI BARANG ==========================================================================================
    */
    public function suplierList()
    {
        $data['limit'] = $_GET['limit'] ?? 20;
        $data['q'] = $_GET['q'] ?? '';
        $data['data'] = $this->store->suplierList($data, $data['limit']);
        $data['active_menu'] = 'suplier';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Data Suplier' => url()->current(),
        ];
        return view('store.suplier-list', compact('data'));
    }
    public function suplierAdd()
    {
        $data['mode'] = 'add';
        $data['active_menu'] = 'suplier';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Data Suplier' => route('suplierList'),
            'Tambah' => url()->current(),
        ];
        return view('store.suplier-form', compact('data'));
    }
    public function suplierEdit($id)
    {
        $data['data'] = $this->store->suplierGet($id);
        if ($data['data'] == false) {
            return redirect()->route('suplierList')->with(['warning' => 'Data suplier tidak ditemukan.']);
        }
        $data['mode'] = 'edit';
        $data['active_menu'] = 'suplier';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Data Suplier' => route('suplierList'),
            'Edit: ' . $data['data']->name => url()->current(),
        ];
        return view('store.suplier-form', compact('data'));
    }
    public function suplierSave(Request $request)
    {
        $data = $request->validate([
            'code' => 'required',
            'name' => 'required',
            'phone' => 'nullable',
            'address' => 'nullable',
        ]);

        if ($request->mode == 'add') {
            if (!$this->store->suplierSave($data)) {
                return back()->with(['warning' => $this->store->error]);
            }
            $message = 'Data suplier berhasil ditambahkan.';
        } else {
            if (!$this->store->suplierUpdate($request->id, $data)) {
                return back()->with(['warning' => $this->store->error]);
            }
            $message = 'Data suplier berhasil diperbaharui.';
        }
        return redirect()->route('suplierList')->with(['success' => $message]);
    }
    public function suplierDelete($id)
    {
        $suplier = $this->store->suplierGet($id);
        if ($suplier == false) {
            return redirect()->route('suplierList')->with(['warning' => 'Data suplier tidak ditemukan.']);
        }
        // cek data barang
        if ($suplier->item->count() > 0) {
            return redirect()->route('suplierList')->with(['warning' => 'Data suplier tidak dapat dihapus.']);
        }
        $suplier->delete();
        return redirect()->route('suplierList')->with(['success' => 'Data suplier berhasil dihapus.']);
    }
    /*
    * ========================================================================================== END KATEGORI BARANG ==========================================================================================
    */



    /*
    * ========================================================================================== START GUDANG ==========================================================================================
    */
    public function warehouseList()
    {
        $data['limit'] = $_GET['limit'] ?? 20;
        $data['q'] = $_GET['q'] ?? '';
        $data['data'] = $this->store->warehouseList($data, $data['limit']);
        $data['active_menu'] = 'warehouse';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Data Gudang' => url()->current(),
        ];
        return view('store.warehouse-list', compact('data'));
    }
    public function warehouseAdd()
    {
        $data['mode'] = 'add';
        $data['active_menu'] = 'warehouse';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Data Gudang' => route('warehouseList'),
            'Tambah' => url()->current(),
        ];
        return view('store.warehouse-form', compact('data'));
    }
    public function warehouseEdit($id)
    {
        $data['data'] = $this->store->warehouseGet($id);
        if ($data['data'] == false) {
            return redirect()->route('warehouseList')->with(['warning' => 'Data gudang tidak ditemukan.']);
        }
        $data['mode'] = 'edit';
        $data['active_menu'] = 'warehouse';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Data Gudang' => route('warehouseList'),
            'Edit: ' . $data['data']->name => url()->current(),
        ];
        return view('store.warehouse-form', compact('data'));
    }
    public function warehouseSave(Request $request)
    {
        $data = $request->validate([
            'code' => 'required',
            'name' => 'required',
            'phone' => 'nullable',
            'cp' => 'nullable',
            'address' => 'nullable',
        ]);

        if ($request->mode == 'add') {
            if (!$this->store->warehouseSave($data)) {
                return back()->with(['warning' => $this->store->error]);
            }
            $message = 'Data gudang berhasil ditambahkan.';
        } else {
            if (!$this->store->warehouseUpdate($request->id, $data)) {
                return back()->with(['warning' => $this->store->error]);
            }
            $message = 'Data gudang berhasil diperbaharui.';
        }
        return redirect()->route('warehouseList')->with(['success' => $message]);
    }
    public function warehouseDelete($id)
    {
        $warehouse = $this->store->warehouseGet($id);
        if ($warehouse == false) {
            return redirect()->route('warehouseList')->with(['warning' => 'Data gudang tidak ditemukan.']);
        }
        // cek data barang
        // disini
        $warehouse->delete();
        return redirect()->route('warehouseList')->with(['success' => 'Data gudang berhasil dihapus.']);
    }
    public function warehouseUser()
    {
        $data['wh_id'] = $_GET['wh_id'] ?? 'all';
        $data['limit'] = $_GET['limit'] ?? 25;
        $data['q'] = $_GET['q'] ?? '';
        $data['data'] = $this->store->warehouseUserList($data, $data['limit']);
        $data['gudang'] = $this->store->warehouseList();
        $data['active_menu'] = 'warehouse';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Data Gudang' => route('warehouseList'),
            'User Gudang' => url()->current(),
        ];
        return view('store.warehouse-user-list', compact('data'));
    }
    public function warehouseUserAdd()
    {
        $data['mode'] = 'add';
        $data['gudang'] = $this->store->warehouseList();
        $data['active_menu'] = 'warehouse';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Data Gudang' => route('warehouseList'),
            'User Gudang' => route('warehouseUser'),
            'Tambah' => url()->current(),
        ];
        return view('store.warehouse-user-form', compact('data'));
    }
    public function warehouseUserEdit($id)
    {
        $data['mode'] = 'edit';
        $data['data'] = $this->store->warehouseUserGet($id);
        $data['active_menu'] = 'warehouse';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Data Gudang' => route('warehouseList'),
            'User Gudang' => route('warehouseUser'),
            'Edit: ' . $data['data']->user->name => url()->current(),
        ];
        return view('store.warehouse-user-form', compact('data'));
    }
    public function warehouseUserSave(UserRequest $request)
    {
        $data = $request->validated();
        if ($request->mode == 'add') {
            if (!$this->user->userSave($data)) {
                return back()->with(['warning' => $this->user->error])->withInput();
            }
            $warehouse = [
                'warehouse_id' => $request->warehouse_id,
                'user_id' => $this->user->last_user_id
            ];
            $this->store->warehouseUserSave($warehouse);
            $message = 'Data user gudang berhasil disimpan.';
        } else {
            if (!$this->user->userUpdate($request->id, $data)) {
                return back()->with(['warning' => $this->user->error])->withInput();
            }
            $message = 'Data user gudang berhasil diperbaharui.';
        }
        return redirect()->route('warehouseUser')->with(['success' => $message]);
    }
    public function warehouseUserDelete($id)
    {
        $warehouse = $this->store->warehouseUserGet($id);
        if ($warehouse == false) {
            return redirect()->route('warehouseUser')->with(['warning' => 'Data user gudang tidak ditemukan.']);
        }
        $user = $this->user->userGet($warehouse->user_id);
        $user->deleted_by = auth()->user()->id ?? 1;
        $user->deleted_at = date('Y-m-d H:i:s');
        $user->username = base64_encode($user->username);
        $user->email = base64_encode($user->email);
        $user->update();
        $warehouse->delete();
        return redirect()->route('warehouseUser')->with(['success' => 'Data user gudang berhasil dihapus.']);
    }
    /*
    * ========================================================================================== END GUDANG ==========================================================================================
    */



    /*
    * ========================================================================================== START DATA BARANG ==========================================================================================
    */
    public function itemList()
    {
        $data['limit'] = $_GET['limit'] ?? 25;
        $data['q'] = $_GET['q'] ?? '';
        $data['data'] = $this->store->itemList($data, $data['limit']);

        $data['warehouse_id'] = $_GET['warehouse_id'] ?? '0';
        if (auth()->user()->isGudang()) {
            $data['warehouse_id'] = auth()->user()->getWarehouseId();
        }
        if ($data['warehouse_id'] != 'all') {
            foreach ($data['data'] as $key => $value) {
                $qty = $this->store->getQty(['warehouse_id' => $data['warehouse_id'], 'item_id' => $value->id]);
                $data['data'][$key]['qty'] = $qty;
            }
        }
        $data['warehouse'] = $this->store->warehouseList();
        $data['active_menu'] = 'item';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Data Barang' => url()->current(),
        ];
        return view('store.item-list', compact('data'));
    }
    public function itemAdd()
    {
        $data['mode'] = 'add';
        $data['active_menu'] = 'item';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Data Barang' => route('itemList'),
            'Tambah' => url()->current(),
        ];
        return view('store.item-form', compact('data'));
    }
    public function itemEdit($id)
    {
        $data['data'] = $this->store->itemGet($id);
        if (!$data['data']) {
            return redirect()->route('itemList')->with(['warning' => 'Data tidak ditemukan']);
        }
        $data['mode'] = 'edit';
        $data['active_menu'] = 'item';
        $data['suplier'] = $this->store->suplierList();
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Data Barang' => route('itemList'),
            'Edit ' . $data['data']->code => url()->current(),
        ];
        return view('store.item-form', compact('data'));
    }
    public function itemSave(Request $request)
    {
        $data = $request->validate([
            'code' => 'required',
            'name' => 'required',
            'harga_jual' => 'required',
        ]);
        $data['harga_jual'] = str_replace(',', '', $data['harga_jual']);
        if ($request->mode == 'add') {
            if (!$this->store->itemSave($data)) {
                return back()->with(['warning' => $this->store->error])->withInput();
            }
            $message = 'Data persediaan barang berhasil disimpan.';
        } else {
            if (!$this->store->itemUpdate($request->id, $data)) {
                return back()->with(['warning' => $this->store->error])->withInput();
            }
            $message = 'Data persediaan barang berhasil diperbaharui.';
        }
        return redirect()->route('itemList')->with(['success' => $message]);
    }
    public function itemDelete($id)
    {
        $item = $this->store->itemGet($id);
        if (!$item) {
            return redirect()->route('itemList')->with(['warning' => 'Data tidak ditemukan']);
        }
        // cek qty
        $item->delete();
        return back()->with(['success' => 'Barang berhasil dihapus.']);
    }
    public function itemDetail($id)
    {
        $data['item'] = $this->store->itemGet($id);
        $data['item_id'] = $data['item']->id;
        $data['stok'] = $_GET['stok'] ?? 'ada';
        $data['warehouse_id'] = $_GET['warehouse_id'] ?? '0';
        if (auth()->user()->isGudang()) {
            $data['warehouse_id'] = auth()->user()->getWarehouseId();
        }
        $data['data'] = $this->store->itemDetailList($data);

        if ($data['warehouse_id'] != 'all') {
            $data['qty'] = $this->store->getQty(['warehouse_id' => $data['warehouse_id'], 'item_id' => $id]);
        } else {
            $data['qty'] = $data['item']->qty;
        }

        $data['warehouse'] = $this->store->warehouseList();

        $data['active_menu'] = 'item';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Data Barang' => route('itemList'),
            $data['item']->code => url()->current(),
        ];
        return view('store.item-detail-list', compact('data'));
    }
    public function itemDistribution($id)
    {
        $data['data'] = $this->store->itemDetailGet($id);
        $data['warehouse'] = $this->store->warehouseList();
        $data['active_menu'] = 'item';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Data Barang' => route('itemList'),
            $data['data']->item->code => route('itemDetail', ['id' => $data['data']->item_id]),
            'Distribusi' => url()->current(),
        ];
        return view('store.item-distribution', compact('data'));
    }
    public function itemDistributionSave(Request $request)
    {
        $data = $request->validate([
            'tanggal_distribusi' => 'required',
            'qty' => 'required',
            'warehouse_id' => 'nullable',
            'id' => 'required',
        ]);
        $item = $this->store->itemDetailGet($data['id']);
        if ($item->warehouse_id == 0) {
            $card[0] = [
                'item_id' => $item->item_id,
                'warehouse_id' => 0,
                'tanggal_transaksi' => $data['tanggal_distribusi'],
                'no_ref' => 'TRX-' . date('YmdHis'),
                'keterangan' => 'Distribusi ke gudang',
                'tipe' => 1,
            ];
            $total_qty = 0;
            for ($i = 1; $i <= count($data['warehouse_id']); $i++) {
                $total_qty += $data['qty'][$i];
                $saveItemDetail = [
                    'tanggal_masuk' => $data['tanggal_distribusi'],
                    'item_id' => $item->item_id,
                    'warehouse_id' => $data['warehouse_id'][$i],
                    'suplier_id' => $item->suplier_id,
                    'tanggal_kadaluarsa' => $item->tanggal_kadaluarsa,
                    'purchase_id' => $item->purchase_id,
                    'harga_beli' => $item->harga_beli,
                    'qty_awal' => $data['qty'][$i],
                    'qty' => $data['qty'][$i],
                    'total' => $data['qty'][$i] * $item->harga_beli,
                ];
                $this->store->itemDetailSave($saveItemDetail);
                $card[$i] = [
                    'item_id' => $item->item_id,
                    'warehouse_id' => $data['warehouse_id'][$i],
                    'tanggal_transaksi' => $data['tanggal_distribusi'],
                    'no_ref' => 'TRX-' . (date('YmdHis') + $i),
                    'qty' => $data['qty'][$i],
                    'keterangan' => 'Distribusi dari pusat',
                    'tipe' => 0,
                    'masuk' => ($data['qty'][$i] * $item->harga_beli)
                ];
            }
            $card[0]['qty'] = $total_qty;
            $card[0]['keluar'] = $total_qty * $item->harga_beli;
            foreach ($card as $key => $value) {
                $this->store->itemCardSave($value);
            }
            $qty = $item->qty - $total_qty;
            $update_item = [
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => auth()->user()->id,
                'qty' => $qty,
                'total' => ($item->harga_beli * $qty)
            ];
            $item->update($update_item);
            return redirect()->route('itemDetail', ['id' => $item->item_id])->with(['success' => 'Persediaan berhasil didistribuksikan.']);
        } else {
            $gudang = $this->store->warehouseGet($item->warehouse_id);
            $saveItemDetail = [
                'tanggal_masuk' => $data['tanggal_distribusi'],
                'item_id' => $item->item_id,
                'warehouse_id' => 0,
                'suplier_id' => $item->suplier_id,
                'tanggal_kadaluarsa' => $item->tanggal_kadaluarsa,
                'purchase_id' => $item->purchase_id,
                'harga_beli' => $item->harga_beli,
                'qty_awal' => $data['qty'],
                'qty' => $data['qty'],
                'total' => $data['qty'] * $item->harga_beli,
            ];
            $this->store->itemDetailSave($saveItemDetail);
            $card = [
                [
                    'item_id' => $item->item_id,
                    'warehouse_id' => $item->warehouse_id,
                    'tanggal_transaksi' => $data['tanggal_distribusi'],
                    'no_ref' => 'TRX-' . date('YmdHis'),
                    'keterangan' => 'Pengembalian barang ke pusat',
                    'tipe' => 1,
                    'qty' => $data['qty'],
                    'keluar' => $data['qty'] * $item->harga_beli
                ],
                [
                    'item_id' => $item->item_id,
                    'warehouse_id' => 0,
                    'tanggal_transaksi' => $data['tanggal_distribusi'],
                    'no_ref' => 'TRX-' . date('YmdHis'),
                    'keterangan' => 'Pengembalikan barang dari gudang ' . $gudang->name,
                    'tipe' => 0,
                    'qty' => $data['qty'],
                    'masuk' => $data['qty'] * $item->harga_beli
                ],
            ];
            foreach ($card as $key => $value) {
                $this->store->itemCardSave($value);
            }
            $qty = $item->qty - $data['qty'];
            $update_item = [
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => auth()->user()->id,
                'qty' => $qty,
                'total' => ($item->harga_beli * $qty)
            ];
            $item->update($update_item);
            return redirect()->route('itemDetail', ['id' => $item->item_id, 'warehouse_id' => $gudang->id])->with(['success' => 'Pengembalian barang ke pusat berhasil.']);
        }
    }
    public function itemUpload()
    {
        $data['limit'] = $_GET['limit'] ?? 25;
        $data['q'] = $_GET['q'] ?? '';
        $data['data'] = $this->store->itemUploadList($data, $data['limit']);
        $data['gudang'] = $this->store->warehouseList();
        $data['active_menu'] = 'item';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Data Barang' => route('itemList'),
            'Upload Data' => url()->current(),
        ];

        if (isset($_GET['confirm'])) {
            $this->store->itemUploadConfirm($_GET['confirm']);
            if ($_GET['confirm'] == 0) {
                return redirect()->route('itemUpload')->with(['info' => 'Upload data barang dibatalkan.']);
            } else {
                return redirect()->route('itemList')->with(['success' => 'Upload data barang berhasil.']);
            }
        }
        return view('store.item-upload', compact('data'));
    }
    public function itemUploadFormat()
    {
        $data['header'] = [
            'NO',
            'KODE BARANG',
            'NAMA BARANG',
            'TANGGAL BELI',
            'HARGA BELI SATUAN (RP)',
            'KODE SUPLIER',
            'HARGA JUAL (RP)',
            'QTY PUSAT (KG)',
        ];
        $data['data'][0] = [
            1,
            'Brg-01',
            'Contoh Barang',
            date_format(date_create('2020-01-15'), 'Y-m-d'),
            '2000',
            'Sup-01',
            '2500',
            '300',
        ];
        $gudang = $this->store->warehouseList();
        foreach ($gudang as $key => $value) {
            $data['header'][] = 'QTY ' . strtoupper($value->name) . ' (KG)';
            $data['data'][0][] = '20';
        }
        return Excel::download(new ItemUploadFormatExport($data), 'FormatUploadDataBarang.xlsx');
    }
    public function itemUploadSave(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);
        $file = $request->file('file')->storeAs('import/store/item', date('YmdHis-') . $_FILES['file']['name']);
        Excel::import(new ItemUploadImport(), $file);
        return redirect()->route('itemUpload')->with(['info' => 'Pastikan data telah sesuai, lalu klik tombol konfirmasi.']);
    }
    public function itemCard($id)
    {
        $data['limit'] = $_GET['limit'] ?? '25';
        $data['q'] = $_GET['q'] ?? '';
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $filter['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime($data['start_date'])));
        $data['end_date'] =  $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data['item'] = $this->store->itemGet($id);
        if (!$data['item']) {
            return redirect()->route('itemList')->with(['warning' => 'Data tidak ditemukan']);
        }
        $data['item_id'] = $filter['item_id'] = $data['item']->id;
        $data['warehouse_id'] = $filter['warehouse_id'] = $_GET['warehouse_id'] ?? '0';
        if (auth()->user()->isGudang()) {
            $data['warehouse_id'] = $filter['item_id'] = auth()->user()->getWarehouseId();
        }
        $data['data'] = $this->store->itemCardList($data, $data['limit']);
        // persediaan awal
        $persediaan = $this->store->itemCardList($filter);
        $data['persediaan_awal'] = $persediaan->where('tipe', 0)->sum('qty') - $persediaan->where('tipe', 1)->sum('qty');
        if ($data['data']->currentPage() > 1) {
            
            $limit = ($data['data']->currentPage() - 1) * $data['data']->perPage();
            $kartu = $this->store->itemCardList($data, $limit, ['tanggal_transaksi', 'asc'], false);
            $data['persediaan_awal'] += $kartu->where('tipe', 0)->sum('qty') - $kartu->where('tipe', 1)->sum('qty');
        }

        $data['warehouse'] = $this->store->warehouseList();
        $data['active_menu'] = 'item';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Data Barang' => route('itemList'),
            'Kartu Persediaan ' . $data['item']->name => url()->current(),
        ];
        $data['title'] = 'Kartu Persediaan';
        if ($data['warehouse_id'] === '0') {
            $data['title'] .= ' Pusat';
        } elseif ($data['warehouse_id'] != 'all') {
            $wh = $this->store->warehouseGet($data['warehouse_id']);
            $data['title'] .= ' ' . $wh->name;
        }
        return view('store.item-card-list', compact('data'));
    }
    public function itemCardPrint($id)
    {
        $data['q'] = $_GET['q'] ?? '';
        $data['start_date'] = $filter['end_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] =  $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data['item'] = $this->store->itemGet($id);
        if (!$data['item']) {
            return redirect()->route('itemList')->with(['warning' => 'Data tidak ditemukan']);
        }
        $data['item_id'] = $filter['item_id'] = $data['item']->id;
        $data['warehouse_id'] = $filter['warehouse_id'] = $_GET['warehouse_id'] ?? '0';
        if (auth()->user()->isGudang()) {
            $data['warehouse_id'] = $filter['item_id'] = auth()->user()->getWarehouseId();
        }
        $data['data'] = $this->store->itemCardList($data);
        // persediaan awal
        $persediaan = $this->store->itemCardList($filter);
        $data['persediaan_awal'] = $persediaan->where('tipe', 0)->sum('qty') - $persediaan->where('tipe', 1)->sum('qty');
        $data['title'] = 'Kartu Persediaan';
        if ($data['warehouse_id'] === '0') {
            $data['title'] .= ' Pusat';
        } elseif ($data['warehouse_id'] != 'all') {
            $wh = $this->store->warehouseGet($data['warehouse_id']);
            $data['title'] .= ' ' . $wh->name;
        }
        $data['assignment'] = $this->master->pengurusAssignment();
        return view('store.item-card-print', compact('data'));
    }
    public function itemCardDownload($id)
    {
        $data['q'] = $_GET['q'] ?? '';
        $data['start_date'] = $filter['end_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] =  $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data['item'] = $this->store->itemGet($id);
        if (!$data['item']) {
            return redirect()->route('itemList')->with(['warning' => 'Data tidak ditemukan']);
        }
        $data['item_id'] = $filter['item_id'] = $data['item']->id;
        $data['warehouse_id'] = $filter['warehouse_id'] = $_GET['warehouse_id'] ?? '0';
        if (auth()->user()->isGudang()) {
            $data['warehouse_id'] = $filter['item_id'] = auth()->user()->getWarehouseId();
        }
        $data['data'] = $this->store->itemCardList($data);
        // persediaan awal
        $persediaan = $this->store->itemCardList($filter);
        $saldo = $persediaan->where('tipe', 0)->sum('qty') - $persediaan->where('tipe', 1)->sum('qty');
        $data['title'] = 'Kartu Persediaan';
        if ($data['warehouse_id'] === '0') {
            $data['title'] .= ' Pusat';
        } elseif ($data['warehouse_id'] != 'all') {
            $wh = $this->store->warehouseGet($data['warehouse_id']);
            $data['title'] .= ' ' . $wh->name;
        }
        $export['item'] = $data['item'];
        $export['start_date'] = $data['start_date'];
        $export['end_date'] = $data['end_date'];
        $export['persediaan_awal'] = $saldo;
        $export['title'] = $data['title'];
        $export['data'] = [];
        $i = 0;
        foreach ($data['data'] as $key => $value) {
            if ($value->tipe) {
                $stok_masuk = 0;
                $stok_keluar = $value->qty;
            } else {
                $stok_masuk = $value->qty;
                $stok_keluar = 0;
            }
            $saldo += $stok_masuk - $stok_keluar;
            $i++;
            $export['data'][$i] = [
                'no' => $i,
                'tanggal_transaksi' => $value->tanggal_transaksi,
                'no_ref' => $value->no_ref,
                'keterangan' => $value->keterangan,
                'masuk' => number_format($stok_masuk, 0, ',', '.'),
                'keluar' => number_format($stok_keluar, 0, ',', '.'),
                'keluar' => number_format($saldo, 0, ',', '.'),
            ];
        }
        $export['saldo'] = $saldo;
        return Excel::download(new ItemCardExport($export), 'Kartu Persediaan ' . $data['item']->name . '.xlsx');
    }
    /*
    * ========================================================================================== END DATA BARANG ==========================================================================================
    */



    /*
    * ========================================================================================== START DATA PEMBELIAN ==========================================================================================
    */
    public function purchaseList()
    {
        $data['limit'] = $_GET['limit'] ?? 25;
        $data['q'] = $_GET['q'] ?? '';
        $data['suplier_id'] = $_GET['suplier_id'] ?? 'all';
        $data['status'] = $_GET['status'] ?? 'all';
        $data['data'] = $this->store->purchaseList($data, $data['limit']);
        $data['suplier'] = $this->store->suplierList();
        $data['active_menu'] = 'purchase';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Data Pembelian' => url()->current(),
        ];
        return view('store.purchase-list', compact('data'));
    }
    public function purchaseAdd()
    {
        $data['suplier'] = $this->store->suplierList();
        $data['no_faktur'] = $this->store->purchaseFactur();
        $data['cash'] = $this->accountancy->accountList(['group_id' => 1]);
        $data['gudang'] = $this->store->warehouseList();
        $data['active_menu'] = 'purchase';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Data Pembelian' => ('purchaseList'),
            'Tambah' => url()->current(),
        ];
        return view('store.purchase-form', compact('data'));
    }
    public function purchaseConfirm(PurchaseRequest $request)
    {
        $data['data'] = $request->validated();
        $data['data']['total_bayar'] = str_replace(',', '', $data['data']['total_bayar']);
        $data['suplier'] = $this->store->suplierGet($data['data']['suplier_id']);
        $data['account'] = $this->accountancy->accountGet(['code', $data['data']['account']]);
        $data['warehouse'] = $data['data']['warehouse_id'] == 0 ? 'Pusat' : $this->store->warehouseGet($data['data']['warehouse_id'])->name;
        $data['active_menu'] = 'purchase';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Data Pembelian' => ('purchaseList'),
            'Tambah' => route('purchaseAdd'),
        ];
        return view('store.purchase-confirm', compact('data'));
    }
    public function purchaseSave(PurchaseRequest $request)
    {
        $data = $request->validated();
        $akun_pembelian = config('config_apps.akun_pembelian');
        $akun_susut_pembelian = config('config_apps.akun_susut_pembelian');
        $akun_diskon = config('config_apps.akun_diskon_pembelian');
        $data['barang'] = json_decode($data['barang']);

        $susut = 0;
        foreach ($data['barang'] as $key => $value) {
            $qty_susut = str_replace(',', '', $value->susut);
            $harga_beli_satuan = str_replace(',', '', $value->harga_beli_satuan);
            $susut += ($qty_susut * $harga_beli_satuan);
        }


        $utang = $data['total'] - $data['diskon'] - $data['total_bayar'];
        $suplier = $this->store->suplierGet($data['suplier_id']);
        $data['tanggal_beli'] = $data['tanggal_beli'] . date(' H:i:s');
        $journal = [
            'transaction_date' => $data['tanggal_beli'],
            'reference_number' => $data['ref_number'],
            'name' => 'Pembelian Kedelai (' . $data['note'] . ')',
            'type' => 1,
            'unit' => 2
        ];
        $journal['detail'][] = [
            'account_code' => $akun_pembelian,
            'type' => 'dana_from',
            'amount' => $data['total'] - $susut
        ];
        if ($susut > 0) {
            $journal['detail'][] = [
                'account_code' => $akun_susut_pembelian,
                'type' => 'dana_from',
                'amount' => $susut
            ];
        }
        if ($data['total_bayar'] > 0) {
            $journal['detail'][] = [
                'account_code' => $data['account'],
                'type' => 'dana_to',
                'amount' => $data['total_bayar']
            ];
        }
        if ($data['diskon'] > 0) {
            $journal['detail'][] = [
                'account_code' => $akun_diskon,
                'type' => 'dana_to',
                'amount' => $data['diskon']
            ];
        }
        if ($utang > 0) {
            $journal['detail'][] = [
                'account_code' => $suplier->account_code,
                'type' => 'dana_to',
                'amount' => $utang
            ];
        }
        $data['status'] = $utang > 0 ? 0 : 1;

        unset($data['account']);
        if (!$this->accountancy->adjustingJournalSave($journal)) {
            return back()->with(['warning' => $this->accountancy->error]);
        }
        $this->accountancy->journalSave($journal);

        $data['journal_id'] = $this->accountancy->last_journal_id;
        $this->store->purchaseSave($data);

        if ($utang > 0) {
            $save_utang = [
                'purchase_id' => $this->store->last_purchase_id,
                'suplier_id' => $data['suplier_id'],
                'total' => $utang,
                'transaction_date' => $data['tanggal_beli'],
                'due_date' => date('Y-m-d', strtotime('+1 month', strtotime($data['tanggal_beli'])))
            ];
            $this->store->purchaseDebtSave($save_utang);
            $hutang_histori = [
                'trxdate' => $data['tanggal_beli'],
                'suplier_id' => $data['suplier_id'],
                'no_ref' => $data['ref_number'],
                'tipe' => 0,
                'total' => $utang,
                'note' => 'Pembelian Kredit dari ' . $suplier->name
            ];
            $this->store->purchaseDebtHistorySave($hutang_histori);
        }
        return redirect()->route('purchaseList')->with(['success' => 'Data pembelian baru berhasil ditambahkan.']);
    }
    public function purchaseDetail($id)
    {
        $data['data'] = $this->store->purchaseGet($id);
        if (!$data['data']) {
            return redirect()->route('purchaseList')->with(['warning' => 'Data pembelian tidak ditemukan.']);
        }
        $data['active_menu'] = 'purchase';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Data Pembelian' => route('purchaseList'),
            $data['data']->no_faktur => url()->current(),
        ];
        return view('store.purchase-detail', compact('data'));
    }
    public function purchasePrint($id)
    {
        $data['data'] = $this->store->purchaseGet($id);
        if (!$data['data']) {
            return redirect()->route('purchaseList')->with(['warning' => 'Data pembelian tidak ditemukan.']);
        }
        return view('store.purchase-print', compact('data'));
    }
    /*
    * ========================================================================================== END DATA PEMBELIAN ==========================================================================================
    */



    /*
    * ========================================================================================== START DATA RETUR PEMBELIAN ==========================================================================================
    */
    public function purchaseReturList()
    {
        $data['limit']  = $_GET['limit'] ?? 25;
        $data['q']  = $_GET['q'] ?? '';
        $data['data'] = $this->store->purchaseReturList($data, $data['limit']);
        $data['active_menu'] = 'purchase';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Data Pembelian' => route('purchaseList'),
            'Retur Pembelian' => url()->current(),
        ];
        return view('store.purchase-retur-list', compact('data'));
    }
    public function purchaseReturAdd($id)
    {
        $data['data'] = $this->store->itemDetailGet($id);
        if (!$data['data']) {
            return redirect()->route('itemList')->with(['warning' => 'Data tidak ditemukan']);
        }
        if ($data['data']->qty <= 0) {
            return redirect()->route('itemDetail', ['id' => $data['data']->item_id])->with(['warning' => 'Qty sudah kosong, tidak dapat diretur.']);
        }
        $data['cash'] = $this->accountancy->accountList(['group_id' => 1]);
        $data['mode'] = 'add';
        $data['active_menu'] = 'purchase';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Data Pembelian' => route('purchaseList'),
            'Retur Pembelian' => route('purchaseReturList'),
            'Tambah Retur' => url()->current(),
        ];
        return view('store.purchase-retur-form', compact('data'));
    }
    public function purchaseReturConfirm(PurchaseReturRequest $request)
    {
        $data = $request->validated();
        $data['data'] = $this->store->itemDetailGet($data['id']);
        if (!$data['data']) {
            return redirect()->route('itemList')->with(['warning' => 'Data tidak ditemukan']);
        }
        if ($data['data']->qty <= 0) {
            return redirect()->route('itemDetail', ['id' => $data['data']->item_id])->with(['warning' => 'Qty sudah kosong, tidak dapat diretur.']);
        }
        if ($data['qty'] > $data['data']->qty) {
            return redirect()->route('purchaseReturAdd', ['id' => $data['data']->id])->with(['warning' => 'Qty sudah kosong, tidak dapat diretur.']);
        }
        $data['akun'] = $this->accountancy->accountGet(['code', $data['akun']]);
        $data['mode'] = 'confirm';
        $data['active_menu'] = 'purchase';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Data Pembelian' => route('purchaseList'),
            'Retur Pembelian' => route('purchaseReturList'),
            'Tambah Retur' => route('purchaseReturAdd', ['id' => $data['id']]),
        ];
        return view('store.purchase-retur-form', compact('data'));
    }
    public function purchaseReturSave(PurchaseReturRequest $request)
    {
        $data = $request->validated();
        $config = config('config_apps');
        $barang = $this->store->itemDetailGet($data['id']);
        $data['item_id'] = $barang->item_id;
        $data['total'] = $barang->harga_beli * $data['qty'];
        $data['harga_beli'] = $barang->harga_beli;
        $data['suplier_id'] = $barang->suplier_id;
        $data['tanggal_retur'] = $data['tanggal_retur'] . date(' H:i:s');
        $journal = [
            'transaction_date' => $data['tanggal_retur'],
            'reference_number' => $data['no_retur'],
            'name' => 'Retur stok ' . $barang->item->name . '(' . $data['note'] . ')',
            'type' => 0,
            'unit' => 2,
            'warehouse_id' => $barang->warehouse_id
        ];
        $journal['detail'][] = [
            'account_code' => $data['akun'],
            'type' => 'dana_from',
            'amount' => $barang->harga_beli * $data['qty']
        ];
        $journal['detail'][] = [
            'account_code' => $config['akun_retur_pembelian'],
            'type' => 'dana_to',
            'amount' => $barang->harga_beli * $data['qty']
        ];

        if (!$this->accountancy->adjustingJournalSave($journal)) {
            return redirect()->route('purchaseReturAdd', ['id' => $data['id']])->with(['warning' => $this->acocuntancy->error]);
        }
        $this->accountancy->journalSave($journal);
        unset($data['akun']);
        $this->store->purchaseReturSave($data);
        return redirect()->route('purchaseReturList')->with(['success' => 'Data retur berhasil ditambahkan.']);
    }
    /*
    * ========================================================================================== END DATA RETUR PEMBELIAN ==========================================================================================
    */



    /*
    * ========================================================================================== START DATA PEMBELIAN UTANG ==========================================================================================
    */
    public function purchaseDebtList()
    {
        $data['limit'] = $_GET['limit'] ?? 25;
        $data['q'] = $_GET['q'] ?? '';
        $data['status'] = $_GET['status'] ?? 'all';
        $data['data'] = $this->store->purchaseDebtList($data, $data['limit']);
        $data['active_menu'] = 'purchase';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Data Pembelian' => route('purchaseList'),
            'Data Utang' => url()->current(),
        ];
        return view('store.purchase-debt-list', compact('data'));
    }
    public function purchaseDebtPay($id)
    {
        $data['data'] = $this->store->purchaseDebtGet($id);
        if (!$data['data']) {
            return redirect()->route('purchaseDebtList')->with(['warning' => 'Data utang tidak ditemukan.']);
        }
        if ($data['data']->status == 1) {
            return redirect()->route('purchaseDebtList')->with(['warning' => 'Data utang sudah lunas.']);
        }
        $data['cash'] = $this->accountancy->accountList(['group_id' => 1]);
        // if(isset($_GET['k'])){
            $data['cash'][] = $this->accountancy->accountGet(['code', '01.01.45']);
            // dd($data['cash']);
        // }
        $data['active_menu'] = 'purchase';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Data Pembelian' => route('purchaseList'),
            'Data Utang' => route('purchaseDebtList'),
            'Bayar' => url()->current(),
        ];
        return view('store.purchase-debt-form', compact('data'));
    }
    public function purchaseDebtConfirm(PurchaseDebtRequest $request)
    {
        $data = $request->validated();
        $data['cash'] = $this->accountancy->accountGet(['code', $data['account']]);
        $data['debt'] = $this->store->purchaseDebtGet($data['id']);
        $data['active_menu'] = 'purchase';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Data Pembelian' => route('purchaseList'),
            'Data Utang' => route('purchaseDebtList'),
            'Bayar' => route('purchaseDebtPay', ['id' => $data['id']]),
        ];
        return view('store.purchase-debt-confirm', compact('data'));
    }
    public function purchaseDebtSave(PurchaseDebtRequest $request)
    {
        $data = $request->validated();
        $debt = $this->store->purchaseDebtGet($data['id']);
        $data['total'] = str_replace(',', '', $data['pay']);
        $data['purchase_id'] = $debt->purchase_id;
        $data['suplier_id'] = $debt->suplier_id;
        $data['note'] = 'Pembayaran Utang (' . $data['note'] . ')';
        $data['transaction_date'] = $data['transaction_date'] . date(' H:i:s');
        $suplier = $this->store->suplierGet($data['suplier_id']);

        $journal = [
            'transaction_date' => $data['transaction_date'],
            'reference_number' => $data['ref_number'],
            'name' => $data['note'],
            'type' => 1,
            'unit' => 2
        ];
        $journal['detail'][] = [
            'account_code' => $suplier->account_code,
            'type' => 'dana_from',
            'amount' => $data['total']
        ];
        $journal['detail'][] = [
            'account_code' => $data['account'],
            'type' => 'dana_to',
            'amount' => $data['total']
        ];
        unset($data['id'], $data['pay'], $data['account']);
        if (!$this->accountancy->adjustingJournalSave($journal)) {
            return back()->with(['warning' => $this->accountancy->error]);
        }
        $this->accountancy->journalSave($journal);
        $this->store->purchaseTransactionSave($data);

        $update_debt['updated_at'] = $update_purchase['updated_at'] = date('Y-m-d h:i:s');
        $update_debt['updated_by'] = $update_purchase['updated_by'] = auth()->user()->id;

        $update_debt['pay'] = $debt->pay + $data['total'];
        if ($update_debt['pay'] == $debt->total) {
            $update_debt['status'] = 1;
        }

        $purchase = $this->store->purchaseGet($debt->purchase_id);
        $update_purchase['total_bayar'] = $purchase->total_bayar + $data['total'];
        if ($update_purchase['total_bayar'] >= ($purchase->total - $purchase->diskon)) {
            $update_purchase['status'] = 1;
        }

        $debt->update($update_debt);
        $purchase->update($update_purchase);


        $hutang_histori = [
            'trxdate' => $data['transaction_date'],
            'suplier_id' => $data['suplier_id'],
            'no_ref' => $data['ref_number'],
            'tipe' => 1,
            'total' => $data['total'],
            'note' => 'Pembayaran utang kepada ' . $suplier->name
        ];
        $this->store->purchaseDebtHistorySave($hutang_histori);

        return redirect()->route('purchaseDebtList')->with(['success' => 'Transaksi pembayaran utang berhasil.']);
    }
    /*
    * ========================================================================================== END DATA PEMBELIAN UTANG ==========================================================================================
    */



    /*
    * ========================================================================================== START DATA PENJUALAN ==========================================================================================
    */
    public function saleList()
    {
        $data['limit'] = $_GET['limit'] ?? 25;
        $data['q'] = $_GET['q'] ?? '';
        $data['status'] = $_GET['status'] ?? 'all';
        $data['warehouse_id'] = $_GET['warehouse_id'] ?? 'all';
        if (auth()->user()->isGudang()) {
            $data['warehouse_id'] = auth()->user()->getWarehouseId();
        }
        if (auth()->user()->isMember()) {
            $data['member_id'] = auth()->user()->member->id;
        }
        $data['data'] = $this->store->saleList($data, $data['limit']);
        $data['active_menu'] = 'sale';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Data Penjualan' => url()->current(),
        ];
        return view('store.sale-list', compact('data'));
    }
    public function saleAdd()
    {
        if (auth()->user()->isGudang()) {
            $warehouse_id = auth()->user()->getWarehouseId();
            $warehouse = $this->store->warehouseGet($warehouse_id);
            $data['no_faktur'] = $warehouse->code . '-';
            $penj = $this->store->saleList(['warehouse_id' => $warehouse->id])->count() + 1;
            $data['no_faktur'] .= str_pad($penj, 9, 0, STR_PAD_LEFT);
        } else {
            $data['no_faktur'] = 'P-';
            $penj = $this->store->saleList(['warehouse_id' => 0])->count() + 1;
            $data['no_faktur'] .= str_pad($penj, 9, 0, STR_PAD_LEFT);
        }
        $data['cash'] = $this->accountancy->accountList(['group_id' => 1]);
        $data['active_menu'] = 'sale';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Data Penjualan' => route('saleList'),
            'Tambah Penjualan' => url()->current(),
        ];
        return view('store.sale-form', compact('data'));
    }
    public function saleConfirm(SaleRequest $request)
    {
        $data['data'] = $request->validated();
        $data['data']['total_bayar'] = str_replace(',', '', $data['data']['total_bayar']);
        $data['data']['potongan_simpati1'] = str_replace(',', '', $data['data']['potongan_simpati1']);
        $data['data']['potongan_simpati2'] = str_replace(',', '', $data['data']['potongan_simpati2']);
        $data['data']['potongan_simpati3'] = str_replace(',', '', $data['data']['potongan_simpati3']);
        $data['member'] = $this->master->memberGet($data['data']['member_id']);
        $data['account'] = $this->accountancy->accountGet(['code', $data['data']['account']]);
        $data['active_menu'] = 'sale';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Data Penjualan' => route('saleList'),
            'Tambah Penjualan' => route('saleAdd'),
        ];
        return view('store.sale-confirm', compact('data'));
    }
    public function saleSave(SaleRequest $request)
    {
        $data = $request->validated();
        // dd($data);
        $data['warehouse_id'] = auth()->user()->isGudang() ? auth()->user()->getWarehouseId() : 0;
        $data['tanggal_jual'] = $data['tanggal_jual'] . date(' H:i:s');
        $member = $this->master->memberGet($data['member_id']);
        $data['region_id'] = $member->region_id;
        $utang = $data['utang'] =  $data['total_belanja'] - $data['total_bayar'];
        $config = config('config_apps');
        $data['status'] = $data['status_pembayaran'] = 0;
        $data['note'] = 'Penjualan Kedelai kepada ' . $member->name . ($data['note'] != null ? ' (' . $data['note'] . ')' : '');
        if ($data['total_bayar'] >= $data['total_belanja']) {
            $data['status'] = $data['status_pembayaran'] = 1;
            $nilai_kas_jurnal = $data['total_belanja'];
        } else {
            $nilai_kas_jurnal = $data['total_bayar'];
        }

        // Potongan 1
        $potongan1 = $data['potongan_simpati1'];
        if ($potongan1 <= $data['total_bayar']) {
            $akun_simpanan = $data['account'];
            $nilai_kas_jurnal = $nilai_kas_jurnal - $potongan1;
        } else {
            $utang = $utang - $potongan1;
            $akun_simpanan = $member->status == 0 ? $config['piutang_penjualan_non_anggota'] : $config['piutang_penjualan_anggota'];
        }
        if ($potongan1 > 0) {
            $simpanan = $this->deposit->depositGet($config['rek_simpati_kopti1']);
            if (!$simpanan) {
                return redirect()->route('saleAdd')->with(['warning' => 'Data Simpati organisasi tidak ditemukan.']);
            }
            $transaksi_simpanan = [
                'deposit_id' => $simpanan->id,
                'transaction_date' => $data['tanggal_jual'],
                'type' => 1,
                'debit' => 0,
                'kredit' => $potongan1,
                'reference_number' => 'TRXS-' . date('YmdHis'),
                'account' => $akun_simpanan,
                'note' => 'Penambahan dari penjualan (' . $data['no_faktur'] . ')',
                'warehouse_id' => $data['warehouse_id']
            ];
            $this->deposit->depositTransactionSave($transaksi_simpanan);
        }
        // Potongan 2
        $potongan2 = $data['potongan_simpati2'];
        if ($potongan2 <= $data['total_bayar']) {
            $akun_simpanan = $data['account'];
            $nilai_kas_jurnal = $nilai_kas_jurnal - $potongan2;
        } else {
            $utang = $utang - $potongan2;
            $akun_simpanan = $member->status == 0 ? $config['piutang_penjualan_non_anggota'] : $config['piutang_penjualan_anggota'];
        }
        if ($potongan2 > 0) {
            $simpanan = $this->deposit->depositGet($config['rek_simpati_kopti2']);
            if (!$simpanan) {
                return redirect()->route('saleAdd')->with(['warning' => 'Data Simpati organisasi tidak ditemukan.']);
            }
            $transaksi_simpanan = [
                'deposit_id' => $simpanan->id,
                'transaction_date' => $data['tanggal_jual'],
                'type' => 1,
                'debit' => 0,
                'kredit' => $potongan2,
                'reference_number' => 'TRXS-' . date('YmdHis'),
                'account' => $akun_simpanan,
                'note' => 'Penambahan dari penjualan (' . $data['no_faktur'] . ')',
                'warehouse_id' => $data['warehouse_id']
            ];
            $this->deposit->depositTransactionSave($transaksi_simpanan);
        }
        // Potongan 3
        $potongan3 = $data['potongan_simpati3'];
        if ($potongan3 <= $data['total_bayar']) {
            $akun_simpanan = $data['account'];
            $nilai_kas_jurnal = $nilai_kas_jurnal - $potongan3;
        } else {
            $utang = $utang - $potongan3;
            $akun_simpanan = $member->status == 0 ? $config['piutang_penjualan_non_anggota'] : $config['piutang_penjualan_anggota'];
        }
        if ($potongan3 > 0) {
            $simpanan = $this->deposit->depositGet($config['rek_simpati_kopti2']);
            if (!$simpanan) {
                return redirect()->route('saleAdd')->with(['warning' => 'Data Simpati organisasi tidak ditemukan.']);
            }
            $transaksi_simpanan = [
                'deposit_id' => $simpanan->id,
                'transaction_date' => $data['tanggal_jual'],
                'type' => 1,
                'debit' => 0,
                'kredit' => $potongan3,
                'reference_number' => 'TRXS-' . date('YmdHis'),
                'account' => $akun_simpanan,
                'note' => 'Penambahan dari penjualan (' . $data['no_faktur'] . ')',
                'warehouse_id' => $data['warehouse_id']
            ];
            $this->deposit->depositTransactionSave($transaksi_simpanan);
        }
        // Jurnal
        $journal = [
            'transaction_date' => $data['tanggal_jual'],
            'reference_number' => $data['ref_number'],
            'name' => $data['note'],
            'type' => 0,
            'unit' => 2,
            'member_id' => $member->id
        ];
        if ($nilai_kas_jurnal > 0) {
            $journal['detail'][] = [
                'account_code' => $data['account'],
                'type' => 'dana_from',
                'amount' => $nilai_kas_jurnal
            ];
        }
        if ($utang > 0) {
            $journal['detail'][] = [
                'account_code' => $member->status == 0 ? $config['piutang_penjualan_non_anggota'] : $config['piutang_penjualan_anggota'],
                'type' => 'dana_from',
                'amount' => $utang
            ];
        }
        $journal['detail'][] = [
            'account_code' => $member->status == 0 ? $config['akun_penjualan_non_anggota'] : $config['akun_penjualan_anggota'],
            'type' => 'dana_to',
            'amount' => $data['total_belanja'] - $potongan1 - $potongan2 - $potongan3
        ];
        if (!$this->accountancy->adjustingJournalSave($journal)) {
            return redirect()->route('saleAdd')->with(['warning' => $this->acocuntancy->error]);
        }
        $this->accountancy->journalSave($journal);
        $data['journal_id'] = $this->accountancy->last_journal_id;
        unset($data['account']);
        $this->store->saleSave($data);
        // jika ada hutang
        if ($utang > 0) {
            $utang_save = [
                'sale_id' => $this->store->last_sale_id,
                'member_id' => $member->id,
                'warehouse_id' => $data['warehouse_id'],
                'total' => $utang,
                'tanggal_transaksi' => $data['tanggal_jual'],
                'jatuh_tempo' => date('Y-m-d', strtotime('+1 month', strtotime($data['tanggal_jual']))),
            ];
            $this->store->saleDebtSave($utang_save);

            $histori_utang = [
                'member_id' => $member->id,
                'region_id' => $member->region_id,
                'member_stat' => $member->status,
                'trxdate' => $data['tanggal_jual'],
                'no_ref' => $data['ref_number'],
                'tipe' => 0,
                'total' => $utang,
                'note' => $data['note'],
                'sale_id' => $this->store->last_sale_id,
                'debt_id' => $this->store->last_sale_debt_id

            ];
            $this->store->saleDebtHistorySave($histori_utang);
        }
        return redirect()->route('saleList')->with(['success' => 'Data penjualan berhasil ditambahkan']);
    }
    public function saleDetail($id)
    {
        $data['data'] = $this->store->saleGet($id);
        if (!$data['data']) {
            return redirect()->route('saleList')->with(['warning' => 'Data penjualan tidak ditemukan.']);
        }
        $data['active_menu'] = 'sale';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Data Penjualan' => route('saleList'),
            $data['data']->no_faktur => url()->current(),
        ];
        return view('store.sale-detail', compact('data'));
    }
    public function salePrint($id)
    {
        $data['data'] = $this->store->saleGet($id);
        if (!$data['data']) {
            return redirect()->route('saleList')->with(['warning' => 'Data penjualan tidak ditemukan.']);
        }
        $data['assignment'] = $this->master->pengurusAssignment();
        return view('store.sale-print', compact('data'));
    }
    /*
    * ========================================================================================== END DATA PENJUALAN ==========================================================================================
    */



    /*
    * ========================================================================================== START DATA PENJUALAN PIUTANG ==========================================================================================
    */
    public function saleDebtList()
    {
        $data['limit'] = $_GET['limit'] ?? 25;
        $data['q'] = $_GET['q'] ?? '';
        $data['status'] = $_GET['status'] ?? 'all';
        $data['warehouse_id'] = $_GET['warehouse_id'] ?? 'all';
        if (auth()->user()->isGudang()) {
            $data['warehouse_id'] = auth()->user()->getWarehouseId();
        }
        if (auth()->user()->isMember()) {
            $data['member_id'] = auth()->user()->member->id;
        }
        $data['data'] = $this->store->saleDebtList($data, $data['limit']);
        $data['active_menu'] = 'sale';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Data Penjualan' => route('saleList'),
            'Data Piutang' => url()->current(),
        ];
        return view('store.sale-debt-list', compact('data'));
    }
    public function saleDebtDetail($id)
    {
        $data['data'] = $this->store->saleDebtGet($id);
        $data['active_menu'] = 'sale';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Data Penjualan' => route('saleList'),
            'Data Piutang' => route('saleDebtList'),
            $data['data']->id => url()->current(),
        ];
        return view('store.sale-debt-detail', compact('data'));
    }
    public function saleDebtDetailPrint($debt_id, $id)
    {
        $data['debt'] = $this->store->saleDebtGet($debt_id);
        $data['data'] = $this->store->saleDebtHistoryGet($id);
        $numberToWords = new NumberToWords();
        $num = $numberToWords->getNumberTransformer('id');
        $data['bilangan'] = $num->toWords($data['data']->total + 0);
        $data['assignment'] = $this->master->pengurusAssignment();
        return view('store.sale-debt-print', compact('data'));
    }
    public function saleDebtPay($id)
    {
        $data['data'] = $this->store->saleDebtGet($id);
        if ($data['data']->status != 0) {
            return redirect()->route('saleDebtList')->with(['warning' => 'Data piutang sudah lunas.']);
        }
        $data['cash'] = $this->accountancy->accountList(['group_id' => 1]);
        $data['active_menu'] = 'sale';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Data Penjualan' => route('saleList'),
            'Data Piutang' => route('saleDebtList'),
            $data['data']->id => route('saleDebtDetail', ['id' => $data['data']->id]),
            'Bayar' => url()->current(),
        ];
        return view('store.sale-debt-form', compact('data'));
    }
    public function saleDebtConfirm(SaleDebtRequest $request)
    {
        $data = $request->validated();
        $data['cash'] = $this->accountancy->accountGet(['code', $data['account']]);
        $data['debt'] = $this->store->saleDebtGet($data['id']);
        $data['active_menu'] = 'sale';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Data Penjualan' => route('saleList'),
            'Data Piutang' => route('saleDebtList'),
            'Bayar' => route('saleDebtPay', ['id' => $data['id']]),
        ];
        return view('store.sale-debt-confirm', compact('data'));
    }
    public function saleDebtSave(SaleDebtRequest $request)
    {
        $data = $request->validated();
        $debt = $this->store->saleDebtGet($data['id']);
        $data['total'] = str_replace(',', '', $data['pay']);
        $data['sale_id'] = $debt->sale_id;
        $data['warehouse_id'] = $debt->warehouse_id;
        $data['note'] = 'Pembayaran Piutang (' . $data['note'] . ')';
        $data['transaction_date'] = $data['transaction_date'] . date(' H:i:s');
        $data['member_id'] = $debt->member_id;
        $warehouse = $this->store->warehouseGet($data['warehouse_id']);
        $config = config('config_apps');

        $journal = [
            'transaction_date' => $data['transaction_date'],
            'reference_number' => $data['ref_number'],
            'name' => $data['note'],
            'type' => 0,
            'unit' => 2
        ];
        $journal['detail'][] = [
            'account_code' => $data['account'],
            'type' => 'dana_from',
            'amount' => $data['total']
        ];
        $journal['detail'][] = [
            'account_code' => ($debt->member->status == 1 ? $config['piutang_penjualan_anggota'] : $config['piutang_penjualan_non_anggota']),
            'type' => 'dana_to',
            'amount' => $data['total']
        ];
        unset($data['id'], $data['pay'], $data['account']);
        if (!$this->accountancy->adjustingJournalSave($journal)) {
            return back()->with(['warning' => $this->accountancy->error]);
        }
        $this->accountancy->journalSave($journal);

        $this->store->saleTransactionSave($data);

        $update_debt['updated_at'] = $update_sale['updated_at'] = date('Y-m-d h:i:s');
        $update_debt['updated_by'] = $update_sale['updated_by'] = auth()->user()->id;

        $update_debt['pay'] = $debt->pay + $data['total'];
        if ($update_debt['pay'] == $debt->total) {
            $update_debt['status'] = 1;
        }

        $sale = $this->store->saleGet($debt->sale_id);
        $update_sale['total_bayar'] = $sale->total_bayar + $data['total'];
        if ($update_sale['total_bayar'] >= $sale->total) {
            $update_sale['status_pembayaran'] = 1;
        }

        $debt->update($update_debt);
        $sale->update($update_sale);

        $histori_utang = [
            'member_id' => $debt->member->id,
            'region_id' => $debt->member->region_id,
            'member_stat' => $debt->member->status,
            'trxdate' => $data['transaction_date'],
            'no_ref' => $data['ref_number'],
            'tipe' => 1,
            'total' => $data['total'],
            'note' => 'Pembayaran piutang dari ' . $debt->member->name,
            'sale_id' => $debt->sale_id,
            'debt_id' => $debt->id
        ];
        $this->store->saleDebtHistorySave($histori_utang);

        return redirect()->route('saleDebtDetail', ['id' => $debt->id])->with(['success' => 'Transaksi pembayaran piutang berhasil.']);
    }
    /*
    * ========================================================================================== END DATA PENJUALAN PIUTANG ==========================================================================================
    */



    /*
    * ========================================================================================== START DATA RETUR PENJUALAN ==========================================================================================
    */
    public function saleReturList()
    {
        $data['limit'] = $_GET['limit'] ?? 25;
        $data['q'] = $_GET['q'] ?? '';
        $data['warehouse_id'] = $_GET['warehouse_id'] ?? '0';
        if (auth()->user()->isGudang()) {
            $data['warehouse_id'] = auth()->user()->getWarehouseId();
        }
        $data['data'] = $this->store->saleReturList($data, $data['limit']);
        $data['warehouse'] = $this->store->warehouseList();
        $data['active_menu'] = 'sale';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Data Penjualan' => route('saleList'),
            'Retur Penjualan' => url()->current(),
        ];
        return view('store.sale-retur-list', compact('data'));
    }
    public function saleReturAdd($id)
    {
        $data['data'] = $this->store->saleDetailget($id);
        $data['cash'] = $this->accountancy->accountList(['group_id' => 1]);
        $data['mode'] = 'add';
        $data['active_menu'] = 'sale';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Data Penjualan' => route('saleList'),
            'Retur Penjualan' => route('saleReturList'),
            'Tambah' => url()->current(),
        ];
        return view('store.sale-retur-form', compact('data'));
    }
    public function saleReturConfirm(SaleReturRequest $request)
    {
        $data = $request->validated();
        $data['mode'] = 'confirm';
        $data['detail'] = $this->store->saleDetailget($data['id']);
        $data['akun'] = $this->accountancy->accountGet(['code', $data['akun']]);
        $data['active_menu'] = 'sale';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Data Penjualan' => route('saleList'),
            'Retur Penjualan' => route('saleReturList'),
            'Tambah' => route('saleReturAdd', ['id' => $data['id']]),
        ];
        return view('store.sale-retur-form', compact('data'));
    }
    public function saleReturSave(SaleReturRequest $request)
    {
        $data = $request->validated();
        $data['tanggal_transaksi'] = $data['tanggal_transaksi'] . date(' H:i:s');
        $detail = $this->store->saleDetailGet($data['id']);
        if (($detail->qty - $detail->qty_retur) < $data['qty']) {
            redirect()->route('saleReturAdd', ['id' => $data['id']])->with(['warning' => 'Qty tidak boleh melebihi penjualan']);
        }
        $config = config('config_apps');
        $journal = [
            'transaction_date' => $data['tanggal_transaksi'],
            'reference_number' => $data['no_ref'],
            'name' => 'Retur penjualan ' . $detail->sale->no_faktur . '(' . $data['note'] . ')',
            'type' => 1,
            'unit' => 2,
            'member_id' => $detail->sale->member_id
        ];
        $journal['detail'][] = [
            'account_code' => $detail->sale->member->status == 0 ? $config['akun_retur_penjualan_non_anggota'] : $config['akun_retur_penjualan_anggota'],
            'type' => 'dana_from',
            'amount' => $detail->harga_jual * $data['qty']
        ];
        $journal['detail'][] = [
            'account_code' => $data['akun'],
            'type' => 'dana_to',
            'amount' => $detail->harga_jual * $data['qty']
        ];
        if (!$this->accountancy->adjustingJournalSave($journal)) {
            return redirect()->route('saleReturAdd', ['id' => $data['id']])->with(['warning' => $this->acocuntancy->error]);
        }
        $this->accountancy->journalSave($journal);
        unset($data['akun']);
        $this->store->saleReturSave($data);
        return redirect()->route('saleReturList')->with(['success' => 'Data retur berhasil ditambahkan.']);
    }
    /*
    * ========================================================================================== END DATA RETUR PENJUALAN ==========================================================================================
    */



    /*
    * ========================================================================================== START STOCK OPNAME ==========================================================================================
    */
    public function stockOpname()
    {
        $data['limit'] = $_GET['limit'] ?? 25;
        $data['q'] = $_GET['q'] ?? '';
        $data['data'] = $this->store->stockOpname([], $data['limit']);
        $data['total_susut'] = $this->store->stockOpname()->sum('total_susut');
        $data['active_menu'] = 'stockopname';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Stock Opname' => url()->current(),
        ];
        if (isset($_GET['confirm'])) {
            if (!$this->store->stockOpnameConfirm($_GET['confirm'])) {
                return back()->with(['warning' => $this->store->accountancy]);
            } else {
                if ($_GET['confirm'] == 0) {
                    return redirect()->route('stockOpname')->with(['info' => 'Stock opname penyusutan dibatalkan.']);
                } else {
                    return redirect()->route('stockOpname')->with(['info' => 'Stock opname sukses.']);
                }
            }
        }
        return view('store.stock-opname', compact('data'));
    }
    public function stockOpnameFormat()
    {
        $filter['so'] = '0';
        $filter['warehouse_id'] = '0';
        if (auth()->user()->isGudang()) {
            $filter['warehouse_id'] = auth()->user()->getWarehouseId();
        }
        $data = $this->store->itemDetailList($filter);
        $export['data'] = [];
        $i = 0;
        $row = 2;
        foreach ($data as $key => $value) {
            $i++;
            $export['data'][] = [
                'no' => $i,
                'id' => $value->id,
                'kode_barang' => $value->item->code ?? '',
                'nama_barang' => $value->item->name ?? '',
                'tanggal_masuk' => $value->tanggal_masuk,
                'gudang' => $value->warehouse->name ?? 'Pusat',
                'harga_beli' => $value->harga_beli,
                'qty' => $value->qty,
                'total_persediaan' => '=G' . $row . '*H' . $row,
                'qty_susut' => '0',
                'total_penyusutan' => '=G' . $row . '*J' . $row,
            ];
            $row++;
        }
        return Excel::download(new StockOpnameExport($export), 'Format Stock Opname.xlsx');
    }
    public function stockOpnameSave(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);
        $file = $request->file('file')->storeAs('import/store/so', date('YmdHis-') . $_FILES['file']['name']);
        Excel::import(new StockOpnameImport($request->tanggal), $file);
        return redirect()->route('stockOpname')->with(['info' => 'Pastikan data telah sesuai, lalu klik tombol konfirmasi.']);
    }
    /*
    * ========================================================================================== END STOCK OPNAME ==========================================================================================
    */



    /*
    * ========================================================================================== START LAPORAN ==========================================================================================
    */
    public function report()
    {
        $data['active_menu'] = 'store-report';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Laporan' => url()->current(),
        ];
        return view('store.report', compact('data'));
    }
    public function reportSaleCash()
    {
        $data['start_date'] = $filter['end_date'] = $_GET['start_date'] ?? date('Y-m-01');
        $data['end_date'] = $_GET['end_date'] ?? date('Y-m-t');
        if ($data['end_date'] < $data['start_date']) {
            return redirect()->route('storeReportSaleCash')->with(['warning' => 'Tanggal tidak valid']);
        }
        $tgl1 = new DateTime($data['start_date']);
        $tgl2 = new DateTime($data['end_date']);
        $selisih = $tgl2->diff($tgl1)->days;
        if ($selisih > 30) {
            return redirect()->route('storeReportSaleCash')->with(['warning' => 'Selisih tanggal tidak boleh lebih dari 30 hari.']);
        }
        $data['item_id'] = $filter['item_id'] = $_GET['item_id'] ?? '';
        $data['member'] = $filter['member'] = $_GET['member'] ?? 'all';
        $data['status'] = $filter['status'] = 1;
        // get list data
        $data['data'] = $this->store->saleDetailList($data);
        // get data barang
        $data['item'] = $this->store->itemList();
        $data['barang'] = $this->store->itemGet($data['item_id']);
        // saldo lalu
        $lastdata = $this->store->saleDetaillist($filter);
        $data['qty'] = $lastdata->sum('qty');
        $data['saldo'] = $lastdata->sum('harga_total_satuan');
        // title
        switch ($data['member']) {
            case '1':
                $data['title'] = 'Laporan Penjualan Tunai Anggota';
                break;

            case '0':
                $data['title'] = 'Laporan Penjualan Tunai Non Anggota';
                break;

            default:
                $data['title'] = 'Laporan Penjualan Tunai';
                break;
        }
        $data['param'] = 'item_id=' . $data['item_id'] . '&start_date=' . $data['start_date'] . '&end_date=' . $data['end_date'] . '&member=' . $data['member'];
        $data['active_menu'] = 'store-report';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Laporan' => route('storeReport'),
            'Penjualan Tunai' => url()->current(),
        ];
        return view('store.report-sale-cash', compact('data'));
    }
    public function reportSaleCashPrint()
    {
        $data['start_date'] = $filter['end_date'] = $_GET['start_date'];
        $data['end_date'] = $_GET['end_date'];
        $data['item_id'] = $filter['item_id'] = $_GET['item_id'];
        $data['member'] = $filter['member'] = $_GET['member'];
        $data['status'] = $filter['status'] = 1;
        // get list data
        $data['data'] = $this->store->saleDetailList($data);
        // get data barang
        $data['item'] = $this->store->itemList();
        $data['barang'] = $this->store->itemGet($data['item_id']);
        // saldo lalu
        $lastdata = $this->store->saleDetaillist($filter);
        $data['qty'] = $lastdata->sum('qty');
        $data['saldo'] = $lastdata->sum('harga_total_satuan');
        // title
        switch ($data['member']) {
            case '1':
                $data['title'] = 'Laporan Penjualan Tunai Anggota';
                break;

            case '0':
                $data['title'] = 'Laporan Penjualan Tunai Non Anggota';
                break;

            default:
                $data['title'] = 'Laporan Penjualan Tunai';
                break;
        }
        $data['assignment'] = $this->master->pengurusAssignment();
        return view('store.report-sale-cash-print', compact('data'));
    }
    public function reportSaleCashDownload()
    {
        $data['start_date'] = $filter['end_date'] = $_GET['start_date'];
        $data['end_date'] = $_GET['end_date'];
        $data['item_id'] = $filter['item_id'] = $_GET['item_id'];
        $data['member'] = $filter['member'] = $_GET['member'];
        $data['status'] = $filter['status'] = 1;
        // get list data
        $data['data'] = $this->store->saleDetailList($data);
        // get data barang
        $data['item'] = $this->store->itemList();
        $data['barang'] = $this->store->itemGet($data['item_id']);
        // saldo lalu
        $lastdata = $this->store->saleDetaillist($filter);
        $data['qty'] = $lastdata->sum('qty');
        $data['saldo'] = $lastdata->sum('harga_total_satuan');
        // title
        switch ($data['member']) {
            case '1':
                $data['title'] = 'Laporan Penjualan Tunai Anggota';
                break;

            case '0':
                $data['title'] = 'Laporan Penjualan Tunai Non Anggota';
                break;

            default:
                $data['title'] = 'Laporan Penjualan Tunai';
                break;
        }
        $export = [
            'title' => $data['title'],
            'periode' => 'Periode ' . date('d-m-Y', strtotime($data['start_date'])) . ' s/d ' . date('d-m-Y', strtotime($data['end_date'])),
            'barang' => $data['barang'],
            'qty_awal' => $data['qty'],
            'saldo_awal' => $data['saldo'],
            'data' => [],
            'date' => $data['start_date']
        ];
        $i = 0;
        foreach ($data['data'] as $key => $value) {
            $data['qty'] += $value->qty;
            $data['saldo'] += $value->harga_total_satuan;
            $export['data'][$i]['no'] = $i + 1;
            $export['data'][$i]['code'] = $value->sale->member->code;
            $export['data'][$i]['name'] = $value->sale->member->name;
            $export['data'][$i]['date'] = date('d-m-Y', strtotime($value->sale->tanggal_jual));
            $export['data'][$i]['no_fak'] = $value->sale->no_faktur;
            $export['data'][$i]['qty'] = number_format($value->qty, 2, ',', '.');
            $export['data'][$i]['harga'] = number_format($value->harga_jual, 2, ',', '.');
            $export['data'][$i]['total'] = number_format($value->harga_total_satuan, 2, ',', '.');
            $i++;
        }
        $export['saldo_akhir'] = $data['saldo'];
        $export['qty_akhir'] = $data['qty'];
        return Excel::download(new StoreSaleCashExport($export), $data['title'] . '.xlsx');
    }
    public function reportSaleDebt()
    {
        $data['start_date'] = $filter['end_date'] = $_GET['start_date'] ?? date('Y-m-01');
        $data['end_date'] = $_GET['end_date'] ?? date('Y-m-t');
        if ($data['end_date'] < $data['start_date']) {
            return redirect()->route('storeReportSaleDebt')->with(['warning' => 'Tanggal tidak valid']);
        }
        $tgl1 = new DateTime($data['start_date']);
        $tgl2 = new DateTime($data['end_date']);
        $selisih = $tgl2->diff($tgl1)->days;
        if ($selisih > 30) {
            return redirect()->route('storeReportSaleDebt')->with(['warning' => 'Selisih tanggal tidak boleh lebih dari 30 hari.']);
        }
        $data['item_id'] = $filter['item_id'] = $_GET['item_id'] ?? '';
        $data['member'] = $filter['member'] = $_GET['member'] ?? 'all';
        $data['status'] = $filter['status'] = 0;
        // get list data
        $data['data'] = $this->store->saleDetailList($data);
        // get data barang
        $data['item'] = $this->store->itemList();
        $data['barang'] = $this->store->itemGet($data['item_id']);
        // saldo lalu
        $lastdata = $this->store->saleDetaillist($filter);
        $data['qty'] = $lastdata->sum('qty');
        $data['saldo'] = $lastdata->sum('harga_total_satuan');
        // title
        switch ($data['member']) {
            case '1':
                $data['title'] = 'Laporan Penjualan Kredit Anggota';
                break;

            case '0':
                $data['title'] = 'Laporan Penjualan Kredit Non Anggota';
                break;

            default:
                $data['title'] = 'Laporan Penjualan Kredit';
                break;
        }
        $data['param'] = 'item_id=' . $data['item_id'] . '&start_date=' . $data['start_date'] . '&end_date=' . $data['end_date'] . '&member=' . $data['member'];
        $data['active_menu'] = 'store-report';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Laporan' => route('storeReport'),
            'Penjualan Kredit' => url()->current(),
        ];
        return view('store.report-sale-debt', compact('data'));
    }
    public function reportSaleDebtPrint()
    {
        $data['start_date'] = $filter['end_date'] = $_GET['start_date'];
        $data['end_date'] = $_GET['end_date'];
        $data['item_id'] = $filter['item_id'] = $_GET['item_id'];
        $data['member'] = $filter['member'] = $_GET['member'];
        $data['status'] = $filter['status'] = 0;
        // get list data
        $data['data'] = $this->store->saleDetailList($data);
        // get data barang
        $data['item'] = $this->store->itemList();
        $data['barang'] = $this->store->itemGet($data['item_id']);
        // saldo lalu
        $lastdata = $this->store->saleDetaillist($filter);
        $data['qty'] = $lastdata->sum('qty');
        $data['saldo'] = $lastdata->sum('harga_total_satuan');
        // title
        switch ($data['member']) {
            case '1':
                $data['title'] = 'Laporan Penjualan Kredit Anggota';
                break;

            case '0':
                $data['title'] = 'Laporan Penjualan Kredit Non Anggota';
                break;

            default:
                $data['title'] = 'Laporan Penjualan Kredit';
                break;
        }
        $data['assignment'] = $this->master->pengurusAssignment();
        return view('store.report-sale-debt-print', compact('data'));
    }
    public function reportSaleDebtDownload()
    {
        $data['start_date'] = $filter['end_date'] = $_GET['start_date'];
        $data['end_date'] = $_GET['end_date'];
        $data['item_id'] = $filter['item_id'] = $_GET['item_id'];
        $data['member'] = $filter['member'] = $_GET['member'];
        $data['status'] = $filter['status'] = 0;
        // get list data
        $data['data'] = $this->store->saleDetailList($data);
        // get data barang
        $data['item'] = $this->store->itemList();
        $data['barang'] = $this->store->itemGet($data['item_id']);
        // saldo lalu
        $lastdata = $this->store->saleDetaillist($filter);
        $data['qty'] = $lastdata->sum('qty');
        $data['saldo'] = $lastdata->sum('harga_total_satuan');
        // title
        switch ($data['member']) {
            case '1':
                $data['title'] = 'Laporan Penjualan Kredit Anggota';
                break;

            case '0':
                $data['title'] = 'Laporan Penjualan Kredit Non Anggota';
                break;

            default:
                $data['title'] = 'Laporan Penjualan Kredit';
                break;
        }
        $export = [
            'title' => $data['title'],
            'periode' => 'Periode ' . date('d-m-Y', strtotime($data['start_date'])) . ' s/d ' . date('d-m-Y', strtotime($data['end_date'])),
            'barang' => $data['barang'],
            'qty_awal' => $data['qty'],
            'saldo_awal' => $data['saldo'],
            'data' => [],
            'date' => $data['start_date']
        ];
        $i = 0;
        foreach ($data['data'] as $key => $value) {
            $data['qty'] += $value->qty;
            $data['saldo'] += $value->harga_total_satuan;
            $export['data'][$i]['no'] = $i + 1;
            $export['data'][$i]['code'] = $value->sale->member->code;
            $export['data'][$i]['name'] = $value->sale->member->name;
            $export['data'][$i]['date'] = date('d-m-Y', strtotime($value->sale->tanggal_jual));
            $export['data'][$i]['no_fak'] = $value->sale->no_faktur;
            $export['data'][$i]['stt_pembayaran'] = $value->sale->status_pembayaran == 1 ? 'Lunas' : 'Belum Lunas';
            $export['data'][$i]['qty'] = number_format($value->qty, 2, ',', '.');
            $export['data'][$i]['harga'] = number_format($value->harga_jual, 2, ',', '.');
            $export['data'][$i]['total'] = number_format($value->harga_total_satuan, 2, ',', '.');
            $i++;
        }
        $export['saldo_akhir'] = $data['saldo'];
        $export['qty_akhir'] = $data['qty'];
        return Excel::download(new StoreSaleDebtExport($export), $data['title'] . '.xlsx');
    }
    public function reportItemStock()
    {
        $data['limit'] = $_GET['limit'] ?? 25;
        $data['q'] = $_GET['q'] ?? '';
        $data['start_date'] = $filter['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $filter['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data['warehouse_id'] = $filter['warehouse_id'] = $_GET['warehouse_id'] ?? '0';
        if (auth()->user()->isGudang()) {
            $data['warehouse_id'] = $filter['warehouse_id'] = auth()->user()->getWarehouseId();
        }
        $dataawal = $this->store->itemCardList($filter);
        $data['data'] = $this->store->itemList($data, $data['limit']);
        foreach ($data['data'] as $key => $value) {
            $filter['item_id'] = $value->id;
            $persediaan_awal = $this->store->itemCardlist(['end_date' => date('Y-m-d', strtotime('-1 day', strtotime($data['start_date']))), 'warehouse_id' => $data['warehouse_id'], 'item_id' => $value->id]);
            $data['data'][$key]['saldo_awal'] = $persediaan_awal->where('tipe', 0)->sum('qty') - $persediaan_awal->where('tipe', 1)->sum('qty');
            $card = $this->store->itemCardList($filter);
            $data['data'][$key]['masuk'] = $card->where('tipe', 0)->sum('qty');
            $data['data'][$key]['keluar'] = $card->where('tipe', 1)->sum('qty');
        }
        $saldoawal = $this->store->itemCardlist(['end_date' => date('Y-m-d', strtotime('-1 day', strtotime($data['start_date']))), 'warehouse_id' => $data['warehouse_id']]);
        $data['total_saldo_awal'] = $saldoawal->where('tipe', 0)->sum('qty') - $saldoawal->where('tipe', 1)->sum('qty');
        $data['total_masuk'] = $dataawal->where('tipe', 0)->sum('qty');
        $data['total_keluar'] = $dataawal->where('tipe', 1)->sum('qty');
        $data['total_persediaan'] = $data['total_saldo_awal'] + $data['total_masuk'] - $data['total_keluar'];
        $data['gudang'] = $this->store->warehouseGet($data['warehouse_id']);
        $data['warehouse'] = $this->store->warehouseList();
        $data['param'] = 'q=' . $data['q'] . '&start_date=' . $data['start_date'] . '&end_date=' . $data['end_date'] . '&warehouse_id=' . $data['warehouse_id'];
        $data['active_menu'] = 'store-report';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Laporan' => route('storeReport'),
            'Persediaan Barang' => url()->current(),
        ];
        return view('store.report-item-stock', compact('data'));
    }
    public function reportItemStockPrint()
    {
        $data['q'] = $_GET['q'] ?? '';
        $data['start_date'] = $filter['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $filter['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data['warehouse_id'] = $filter['warehouse_id'] = $_GET['warehouse_id'] ?? '0';
        if (auth()->user()->isGudang()) {
            $data['warehouse_id'] = $filter['warehouse_id'] = auth()->user()->getWarehouseId();
        }
        $dataawal = $this->store->itemCardList($filter);
        $data['data'] = $this->store->itemList($data);
        foreach ($data['data'] as $key => $value) {
            $filter['item_id'] = $value->id;
            $persediaan_awal = $this->store->itemCardlist(['end_date' => date('Y-m-d', strtotime('-1 day', strtotime($data['start_date']))), 'warehouse_id' => $data['warehouse_id'], 'item_id' => $value->id]);
            $data['data'][$key]['saldo_awal'] = $persediaan_awal->where('tipe', 0)->sum('qty') - $persediaan_awal->where('tipe', 1)->sum('qty');
            $card = $this->store->itemCardList($filter);
            $data['data'][$key]['masuk'] = $card->where('tipe', 0)->sum('qty');
            $data['data'][$key]['keluar'] = $card->where('tipe', 1)->sum('qty');
        }
        $saldoawal = $this->store->itemCardlist(['end_date' => date('Y-m-d', strtotime('-1 day', strtotime($data['start_date']))), 'warehouse_id' => $data['warehouse_id']]);
        $data['total_saldo_awal'] = $saldoawal->where('tipe', 0)->sum('qty') - $saldoawal->where('tipe', 1)->sum('qty');
        $data['total_masuk'] = $dataawal->where('tipe', 0)->sum('qty');
        $data['total_keluar'] = $dataawal->where('tipe', 1)->sum('qty');
        $data['total_persediaan'] = $data['total_saldo_awal'] + $data['total_masuk'] - $data['total_keluar'];
        $data['gudang'] = $this->store->warehouseGet($data['warehouse_id']);
        $data['warehouse'] = $this->store->warehouseList();
        $data['assignment'] = $this->master->pengurusAssignment();
        return view('store.report-item-stock-print', compact('data'));
    }
    public function reportItemStockDownload()
    {
        $data['q'] = $_GET['q'] ?? '';
        $data['start_date'] = $filter['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $filter['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data['warehouse_id'] = $filter['warehouse_id'] = $_GET['warehouse_id'] ?? '0';
        if (auth()->user()->isGudang()) {
            $data['warehouse_id'] = $filter['warehouse_id'] = auth()->user()->getWarehouseId();
        }
        $dataawal = $this->store->itemCardList($filter);
        $data['data'] = $this->store->itemList($data);
        $export['header'] = [
            'No',
            'Kode Barang',
            'Nama Barang',
            'Stok s/d ' . date('d-M-Y', strtotime('-1 day', strtotime($data['start_date']))) . ' (Kg)',
            'Masuk (Kg)',
            'Keluar (Kg)',
            'Stok Akhir (Kg)',
        ];
        $export['data'] = [];
        $i = 0;
        foreach ($data['data'] as $key => $value) {
            $i++;
            $filter['item_id'] = $value->id;
            $persediaan_awal = $this->store->itemCardlist(['end_date' => date('Y-m-d', strtotime('-1 day', strtotime($data['start_date']))), 'warehouse_id' => $data['warehouse_id'], 'item_id' => $value->id]);
            $saldo_awal = $persediaan_awal->where('tipe', 0)->sum('qty') - $persediaan_awal->where('tipe', 1)->sum('qty');
            $card = $this->store->itemCardList($filter);
            $masuk = $card->where('tipe', 0)->sum('qty');
            $keluar = $card->where('tipe', 1)->sum('qty');
            $total = $saldo_awal + $masuk - $keluar;

            $export['data'][$key]['no'] = $i;
            $export['data'][$key]['kode_brg'] = $value->code;
            $export['data'][$key]['nama_brg'] = $value->name;
            $export['data'][$key]['saldo_awal'] = number_format($saldo_awal, 2, ',', '.');
            $export['data'][$key]['masuk'] = number_format($masuk, 2, ',', '.');
            $export['data'][$key]['keluar'] = number_format($keluar, 2, ',', '.');
            $export['data'][$key]['total'] = number_format($total, 2, ',', '.');
        }
        $saldoawal = $this->store->itemCardlist(['end_date' => date('Y-m-d', strtotime('-1 day', strtotime($data['start_date']))), 'warehouse_id' => $data['warehouse_id']]);
        $export['total_saldo_awal'] = $saldoawal->where('tipe', 0)->sum('qty') - $saldoawal->where('tipe', 1)->sum('qty');
        $export['total_masuk'] = $dataawal->where('tipe', 0)->sum('qty');
        $export['total_keluar'] = $dataawal->where('tipe', 1)->sum('qty');
        $export['total_persediaan'] = $export['total_saldo_awal'] + $export['total_masuk'] - $export['total_keluar'];
        $gudang = $this->store->warehouseGet($data['warehouse_id']);
        $data['warehouse'] = $this->store->warehouseList();
        $export['title'] = 'Laporan Persediaan ' . ($gudang->name ?? 'Pusat');
        $export['periode'] = 'Periode ' . date('d-m-Y', strtotime($data['start_date'])) . ' s/d ' . date('d-m-Y', strtotime($data['end_date']));
        return Excel::download(new StoreSaleItemStockExport($export), $export['title'] . '.xlsx');
    }
    public function reportRegion()
    {
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data['data'] = $this->master->regionList();
        $data['param'] = 'start_date=' . $data['start_date'] . '&end_date=' . $data['end_date'];
        $data['active_menu'] = 'store-report';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Laporan' => route('storeReport'),
            'Penjualan Wilayah' => url()->current(),
        ];
        return view('store.report-region', compact('data'));
    }
    public function reportRegionPrint()
    {
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data['data'] = $this->master->regionList();
        $data['assignment'] = $this->master->pengurusAssignment();
        return view('store.report-region-print', compact('data'));
    }
    public function reportRegionDownload()
    {
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data['data'] = $this->master->regionList();
        $export['periode'] = 'Periode ' . date('d-m-Y', strtotime($data['start_date'])) . ' s/d ' . date('d-m-Y', strtotime($data['end_date']));
        $export['header'] = ['No', 'Wilayah', 'Kebutuhan/Bulan (Kg)', 'Total Penjualan (Rp)'];
        $export['data'] = [];
        $i = $total_kebutuhan = $total_penjualan = 0;
        foreach ($data['data'] as $key => $value) {
            $i++;
            $kebutuhan = $value->member->sum('soybean_ration');
            $total_kebutuhan += $kebutuhan;
            $penjualan = $value->penjualan->where('tanggal_jual', '>=', $data['start_date'] . ' 00:00:00')->where('tanggal_jual', '<=', $data['end_date'] . ' 23:59:59')->sum('total_belanja');
            $total_penjualan += $penjualan;
            $export['data'][$key]['no'] = $i;
            $export['data'][$key]['name'] = $value->name;
            $export['data'][$key]['kebutuhan'] = number_format($kebutuhan, 2, ',', '.');
            $export['data'][$key]['penjualan'] = number_format($penjualan, 2, ',', '.');
        }
        $export['total_kebutuhan'] = $total_kebutuhan;
        $export['total_penjualan'] = $total_penjualan;
        return Excel::download(new StoreSaleRegionExport($export), 'Rekapitulasi Penjualan Wilayah.xlsx');
    }
    public function reportMember()
    {
        $data['limit'] = $_GET['limit'] ?? 25;
        $data['status'] = $_GET['status'] ?? '1';
        $data['region_id'] = $_GET['region_id'] ?? 'all';
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data['data'] = $this->master->memberList($data, $data['limit']);
        foreach ($data['data'] as $key => $value) {
            $data['data'][$key]['total_qty'] = 0;
            $penjualan = $value->penjualan->where('tanggal_jual', '>=', $data['start_date'] . ' 00:00:00')->where('tanggal_jual', '<=', $data['end_date'] . ' 23:59:59');
            foreach ($penjualan as $hsl => $hasil) {
                $data['data'][$key]['total_qty'] += $hasil->detail->sum('qty');
            }
        }
        $data['param'] = 'start_date=' . $data['start_date'] . '&end_date=' . $data['end_date'] . '&region_id=' . $data['region_id'] . '&status=' . $data['status'];
        $data['region'] = $this->master->regionList();
        if ($data['status'] == '1') {
            $data['title'] = 'Rekapitulasi Penjualan Anggota';
        } else if ($data['status'] == '0') {
            $data['title'] = 'Rekapitulasi Penjualan Non Anggota';
        } else {
            $data['title'] = 'Rekapitulasi Penjualan Anggota dan Non Anggota';
        }
        $data['active_menu'] = 'store-report';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Laporan' => route('storeReport'),
            'Penjualan Anggota & Non Anggota' => url()->current(),
        ];
        return view('store.report-sale-member', compact('data'));
    }
    public function reportMemberPrint()
    {
        $data['status'] = $_GET['status'] ?? '1';
        $data['region_id'] = $_GET['region_id'] ?? 'all';
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data['data'] = $this->master->memberList($data);
        foreach ($data['data'] as $key => $value) {
            $data['data'][$key]['total_qty'] = 0;
            $penjualan = $value->penjualan->where('tanggal_jual', '>=', $data['start_date'] . ' 00:00:00')->where('tanggal_jual', '<=', $data['end_date'] . ' 23:59:59');
            foreach ($penjualan as $hsl => $hasil) {
                $data['data'][$key]['total_qty'] += $hasil->detail->sum('qty');
            }
        }
        if ($data['status'] == '1') {
            $data['title'] = 'Rekapitulasi Penjualan Anggota';
        } else {
            $data['title'] = 'Rekapitulasi Penjualan Non Anggota';
        }
        $data['assignment'] = $this->master->pengurusAssignment();
        return view('store.report-sale-member-print', compact('data'));
    }
    public function reportMemberDownload()
    {
        $data['status'] = $_GET['status'] ?? '1';
        $data['region_id'] = $_GET['region_id'] ?? 'all';
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data['data'] = $this->master->memberList($data);
        if ($data['status'] == '1') {
            $export['title'] = 'Rekapitulasi Penjualan Anggota';
        } else {
            $export['title'] = 'Rekapitulasi Penjualan Non Anggota';
        }
        $export['header'] = ['No', 'Kode Anggota', 'Nama Anggota', 'Wilayah', 'Jatah/Bulan (Kg)', 'Total Penjualan (Kg)'];
        $export['periode'] = 'Periode ' . date('d-m-Y', strtotime($data['start_date'])) . ' s/d ' . date('d-m-Y', strtotime($data['end_date']));
        $i = $total_penjualan = $total_kebutuhan = 0;
        foreach ($data['data'] as $key => $value) {
            $i++;
            $total_kebutuhan += $value->soybean_ration;
            $penjualan = 0;
            $listpenjualan = $value->penjualan->where('tanggal_jual', '>=', $data['start_date'] . ' 00:00:00')->where('tanggal_jual', '<=', $data['end_date'] . ' 23:59:59');
            foreach ($listpenjualan as $hsl => $hasil) {
                $penjualan += $hasil->detail->sum('qty');
            }
            $total_penjualan += $penjualan;
            $export['data'][$key]['no'] = $i;
            $export['data'][$key]['code'] = $value->code;
            $export['data'][$key]['name'] = $value->name;
            $export['data'][$key]['wilayah'] = $value->region->name;
            $export['data'][$key]['jatah'] = number_format($value->soybean_ration, 2, ',', '.');
            $export['data'][$key]['penjualan'] = number_format($penjualan, 2, ',', '.');
        }
        $export['total_kebutuhan'] = $total_kebutuhan;
        $export['total_penjualan'] = $total_penjualan;
        return Excel::download(new StoreSaleMemberExport($export), $export['title'] . '.xlsx');
    }
    public function reportUtang()
    {
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data['data'] = $this->store->suplierList();
        foreach ($data['data'] as $key => $value) {
            $data['data'][$key]['saldo_awal'] = $value->utangHistori->where('tipe', 0)->where('trxdate', '<', $data['start_date'] . ' 00:00:00')->sum('total') - $value->utangHistori->where('tipe', 1)->where('trxdate', '<=', $data['start_date'] . ' 00:00:00')->sum('total');
            $data['data'][$key]['penambahan'] = $value->utangHistori->where('tipe', 0)->where('trxdate', '>=', $data['start_date'] . ' 00:00:00')->where('trxdate', '<=', $data['end_date'] . ' 23:59:59')->sum('total');
            $data['data'][$key]['pengurangan'] = $value->utangHistori->where('tipe', 1)->where('trxdate', '>=', $data['start_date'] . ' 00:00:00')->where('trxdate', '<=', $data['end_date'] . ' 23:59:59')->sum('total');
            $data['data'][$key]['saldo_akhir'] = $data['data'][$key]['saldo_awal'] + $data['data'][$key]['penambahan'] - $data['data'][$key]['pengurangan'];
        }
        $data['active_menu'] = 'store-report';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Laporan' => route('storeReport'),
            'Rekapitulasi Utang' => url()->current(),
        ];
        return view('store.report-utang', compact('data'));
    }
    public function reportUtangPrint()
    {
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data['data'] = $this->store->suplierList();
        foreach ($data['data'] as $key => $value) {
            $data['data'][$key]['saldo_awal'] = $value->utangHistori->where('tipe', 0)->where('trxdate', '<', $data['start_date'] . ' 00:00:00')->sum('total') - $value->utangHistori->where('tipe', 1)->where('trxdate', '<=', $data['start_date'] . ' 00:00:00')->sum('total');
            $data['data'][$key]['penambahan'] = $value->utangHistori->where('tipe', 0)->where('trxdate', '>=', $data['start_date'] . ' 00:00:00')->where('trxdate', '<=', $data['end_date'] . ' 23:59:59')->sum('total');
            $data['data'][$key]['pengurangan'] = $value->utangHistori->where('tipe', 1)->where('trxdate', '>=', $data['start_date'] . ' 00:00:00')->where('trxdate', '<=', $data['end_date'] . ' 23:59:59')->sum('total');
            $data['data'][$key]['saldo_akhir'] = $data['data'][$key]['saldo_awal'] + $data['data'][$key]['penambahan'] - $data['data'][$key]['pengurangan'];
        }
        $data['assignment'] = $this->master->pengurusAssignment();
        return view('store.report-utang-print', compact('data'));
    }
    public function reportUtangDownload()
    {
        $start_date = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $end_date = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data = $this->store->suplierList();
        $export['data'] = [];
        $export['saldo_awal'] = $export['penambahan'] = $export['pengurangan'] = $export['saldo_akhir'] = 0;
        $export['header'] = ['#', 'Kode Suplier', 'Nama Suplier', 'Utang s/d ' . date('Y-m-d', strtotime('-1 day', strtotime($start_date))) . ' (Rp)', 'Penambahan (Rp)', 'Pengurangan (Rp)', 'Total Utang (Rp)'];
        $i = 1;
        foreach ($data as $key => $value) {
            $saldo_awal = $value->utangHistori->where('tipe', 0)->where('trxdate', '<', $start_date . ' 00:00:00')->sum('total') - $value->utangHistori->where('tipe', 1)->where('trxdate', '<=', $start_date . ' 00:00:00')->sum('total');
            $penambahan = $value->utangHistori->where('tipe', 0)->where('trxdate', '>=', $start_date . ' 00:00:00')->where('trxdate', '<=', $end_date . ' 23:59:59')->sum('total');
            $pengurangan = $value->utangHistori->where('tipe', 1)->where('trxdate', '>=', $start_date . ' 00:00:00')->where('trxdate', '<=', $end_date . ' 23:59:59')->sum('total');
            $saldo_akhir = $saldo_awal + $penambahan - $pengurangan;
            $export['data'][$i] = [
                $i++,
                $value->code,
                $value->name,
                number_format($saldo_awal, 2, ',', '.'),
                number_format($penambahan, 2, ',', '.'),
                number_format($pengurangan, 2, ',', '.'),
                number_format($saldo_akhir, 2, ',', '.'),
            ];
            $export['saldo_awal'] += $saldo_awal;
            $export['penambahan'] += $penambahan;
            $export['pengurangan'] += $pengurangan;
            $export['saldo_akhir'] += $saldo_akhir;
        }
        $export['periode'] = 'Periode ' . $start_date . ' s/d ' . $end_date;
        return Excel::download(new StoreReportUtangExport($export), 'Rekapitulasi Utang.xlsx');
    }
    public function reportUtangAdd()
    {
        $data['suplier'] = $this->store->suplierList();
        $data['active_menu'] = 'store-report';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Laporan' => route('storeReport'),
            'Rekapitulasi Utang' => route('storeReportUtang'),
            'Tambah Transaksi' => url()->current(),
        ];
        return view('store.report-utang-form', compact('data'));
    }
    public function reportUtangSave(RekapitulasiUtangRequest $request)
    {
        $data = $request->validated();
        $data['total'] = str_replace(',', '', $data['total']);
        $data['trxdate'] = $data['trxdate'] . date(' H:i:s');
        $this->store->purchaseDebtHistorySave($data);
        return redirect()->route('storeReportUtang')->with(['success' => 'Data berhasil diinput.']);
    }
    public function reportUtangDetail($id)
    {
        $data['limit'] = $_GET['limit'] ?? 25;
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data['data'] = $this->store->suplierGet($id);
        $data['saldo_awal'] = $data['data']->utangHistori->where('tipe', 0)->where('trxdate', '<', $data['start_date'] . ' 00:00:00')->sum('total') - $data['data']->utangHistori->where('tipe', 1)->where('trxdate', '<=', $data['start_date'] . ' 00:00:00')->sum('total');
        $filter = [
            'suplier_id' => $data['data']->id,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
        ];
        $data['list'] = $this->store->purchaseDebtHistoryList($filter, $data['limit']);

        if ($data['list']->currentPage() != 1) {
            $limit = ($data['list']->currentPage() - 1) * $data['limit'];
            $list = $this->store->purchaseDebtHistoryList($filter, $limit, ['trxdate', 'asc'], false);
            $data['saldo_awal'] += $list->where('tipe', 0)->sum('total') - $list->where('tipe', 1)->sum('total');
        }

        $data['active_menu'] = 'store-report';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Laporan' => route('storeReport'),
            'Rekapitulasi Utang' => route('storeReportUtang'),
            $data['data']->name => url()->current()
        ];
        return view('store.report-utang-detail', compact('data'));
    }
    public function reportUtangDetailPrint($id)
    {
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data['data'] = $this->store->suplierGet($id);
        $data['saldo_awal'] = $data['data']->utangHistori->where('tipe', 0)->where('trxdate', '<', $data['start_date'] . ' 00:00:00')->sum('total') - $data['data']->utangHistori->where('tipe', 1)->where('trxdate', '<=', $data['start_date'] . ' 00:00:00')->sum('total');
        $filter = [
            'suplier_id' => $data['data']->id,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
        ];
        $data['assignment'] = $this->master->pengurusAssignment();
        $data['list'] = $this->store->purchaseDebtHistoryList($filter);

        return view('store.report-utang-detail-print', compact('data'));
    }
    public function reportUtangDetailDownload($id)
    {
        $start_date = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $end_date = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $suplier = $this->store->suplierGet($id);
        $saldo = $suplier->utangHistori->where('tipe', 0)->where('trxdate', '<', $start_date . ' 00:00:00')->sum('total') - $suplier->utangHistori->where('tipe', 1)->where('trxdate', '<=', $start_date . ' 00:00:00')->sum('total');
        $filter = [
            'suplier_id' => $suplier->id,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ];
        $data = $this->store->purchaseDebtHistoryList($filter);

        $export['periode'] = 'Periode ' . $start_date . ' s/d ' . $end_date;
        $export['suplier'] = $suplier->name;
        $export['header'] = ['#', 'Tanggal Transaksi', 'No Ref', 'Keterangan', 'Debit (Rp)', 'Kredit (Rp)', 'Saldo (Rp)'];
        $export['saldo_awal'] = $saldo;
        $export['data'] = [];
        $i = 0;
        foreach ($data as $key => $value) {
            $i++;
            if ($value->tipe) {
                $debit = $value->total;
                $kredit = 0;
            } else {
                $debit = 0;
                $kredit = $value->total;
            }
            $saldo += $kredit - $debit;
            $export['data'][$key] = [
                $i,
                $value->trxdate,
                $value->no_ref,
                $value->note,
                number_format($debit, 2, ',', '.'),
                number_format($kredit, 2, ',', '.'),
                number_format($saldo, 2, ',', '.')
            ];
        }
        $export['saldo_akhir'] = $saldo;

        return Excel::download(new StoreReportUtangDetailExport($export), 'Buku Besar Pembantu Utang.xlsx');
    }
    public function reportPiutang()
    {
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data['data'] = $this->master->regionList();
        foreach ($data['data'] as $key => $value) {
            $data['data'][$key]['saldo_awal'] = $value->utangHistori->where('tipe', 0)->where('trxdate', '<', $data['start_date'] . ' 00:00:00')->sum('total') - $value->utangHistori->where('tipe', 1)->where('trxdate', '<=', $data['start_date'] . ' 00:00:00')->sum('total');
            $data['data'][$key]['penambahan'] = $value->utangHistori->where('tipe', 0)->where('trxdate', '>=', $data['start_date'] . ' 00:00:00')->where('trxdate', '<=', $data['end_date'] . ' 23:59:59')->sum('total');
            $data['data'][$key]['pengurangan'] = $value->utangHistori->where('tipe', 1)->where('trxdate', '>=', $data['start_date'] . ' 00:00:00')->where('trxdate', '<=', $data['end_date'] . ' 23:59:59')->sum('total');
            $data['data'][$key]['saldo_akhir'] = $data['data'][$key]['saldo_awal'] + $data['data'][$key]['penambahan'] - $data['data'][$key]['pengurangan'];
        }
        $data['active_menu'] = 'store-report';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Laporan' => route('storeReport'),
            'Rekapitulasi Piutang' => url()->current(),
        ];
        return view('store.report-piutang', compact('data'));
    }
    public function reportPiutangPrint()
    {
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data['data'] = $this->master->regionList();
        foreach ($data['data'] as $key => $value) {
            $data['data'][$key]['saldo_awal'] = $value->utangHistori->where('tipe', 0)->where('trxdate', '<', $data['start_date'] . ' 00:00:00')->sum('total') - $value->utangHistori->where('tipe', 1)->where('trxdate', '<=', $data['start_date'] . ' 00:00:00')->sum('total');
            $data['data'][$key]['penambahan'] = $value->utangHistori->where('tipe', 0)->where('trxdate', '>=', $data['start_date'] . ' 00:00:00')->where('trxdate', '<=', $data['end_date'] . ' 23:59:59')->sum('total');
            $data['data'][$key]['pengurangan'] = $value->utangHistori->where('tipe', 1)->where('trxdate', '>=', $data['start_date'] . ' 00:00:00')->where('trxdate', '<=', $data['end_date'] . ' 23:59:59')->sum('total');
            $data['data'][$key]['saldo_akhir'] = $data['data'][$key]['saldo_awal'] + $data['data'][$key]['penambahan'] - $data['data'][$key]['pengurangan'];
        }
        $data['assignment'] = $this->master->pengurusAssignment();
        return view('store.report-piutang-print', compact('data'));
    }
    public function reportPiutangDownload()
    {
        $start_date = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $end_date = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data['data'] = $this->master->regionList();
        $export['header'] = ['No', 'Kode Wilayah', 'Nama Wilayah', 'Piutang s/d ' . $start_date . ' (Rp)', 'Penambahan (Rp)', 'Pengurangan (Rp)', 'Total Piutang (Rp)'];
        $export['data'] = [];
        $i = $export['saldo_awal'] = $export['penambahan'] = $export['pengurangan'] = $export['saldo_akhir'] = 0;
        foreach ($data['data'] as $key => $value) {
            $i++;
            $saldo_awal = $value->utangHistori->where('tipe', 0)->where('trxdate', '<', $start_date . ' 00:00:00')->sum('total') - $value->utangHistori->where('tipe', 1)->where('trxdate', '<=', $start_date . ' 00:00:00')->sum('total');
            $penambahan = $value->utangHistori->where('tipe', 0)->where('trxdate', '>=', $start_date . ' 00:00:00')->where('trxdate', '<=', $end_date . ' 23:59:59')->sum('total');
            $pengurangan = $value->utangHistori->where('tipe', 1)->where('trxdate', '>=', $start_date . ' 00:00:00')->where('trxdate', '<=', $end_date . ' 23:59:59')->sum('total');
            $saldo_akhir = $saldo_awal + $penambahan - $pengurangan;
            $export['data'][$key] = [
                $i,
                $value->code,
                $value->name,
                number_format($saldo_awal, 2, ',', '.'),
                number_format($penambahan, 2, ',', '.'),
                number_format($pengurangan, 2, ',', '.'),
                number_format($saldo_akhir, 2, ',', '.')
            ];
            $export['saldo_awal'] += $saldo_awal;
            $export['penambahan'] += $penambahan;
            $export['pengurangan'] += $pengurangan;
            $export['saldo_akhir'] += $saldo_akhir;
        }
        $export['periode'] = 'Periode ' . $start_date . ' s/d ' . $end_date;
        return Excel::download(new StoreReportPiutangExport($export), 'Rekapitulasi Piutang.xlsx');
    }
    public function reportPiutangDetail()
    {
        $data['region_id'] = $_GET['region_id'];
        $data['status'] = $_GET['status'] ?? '1';
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data['limit'] = $_GET['limit'] ?? 25;
        $data['data'] = $this->master->memberList($data, $data['limit']);
        foreach ($data['data'] as $key => $value) {
            $data['data'][$key]['saldo_awal'] = $value->utangHistori->where('tipe', 0)->where('trxdate', '<', $data['start_date'] . ' 00:00:00')->sum('total') - $value->utangHistori->where('tipe', 1)->where('trxdate', '<=', $data['start_date'] . ' 00:00:00')->sum('total');
            $data['data'][$key]['penambahan'] = $value->utangHistori->where('tipe', 0)->where('trxdate', '>=', $data['start_date'] . ' 00:00:00')->where('trxdate', '<=', $data['end_date'] . ' 23:59:59')->sum('total');
            $data['data'][$key]['pengurangan'] = $value->utangHistori->where('tipe', 1)->where('trxdate', '>=', $data['start_date'] . ' 00:00:00')->where('trxdate', '<=', $data['end_date'] . ' 23:59:59')->sum('total');
            $data['data'][$key]['saldo_akhir'] = $data['data'][$key]['saldo_awal'] + $data['data'][$key]['penambahan'] - $data['data'][$key]['pengurangan'];
        }
        $saldo_awal = $this->store->saleDebtHistoryList([
            'region_id' => $data['region_id'],
            'end_date' => $data['start_date'],
            'member_stat' => $data['status']
        ]);
        $saldo = $this->store->saleDebtHistoryList([
            'region_id' => $data['region_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'member_stat' => $data['status']
        ]);
        $data['saldo_awal'] = $saldo_awal->where('tipe', 0)->where('trxdate', '<', $data['start_date'] . ' 00:00:00')->sum('total') - $saldo_awal->where('tipe', 1)->where('trxdate', '<=', $data['start_date'] . ' 00:00:00')->sum('total');
        $data['penambahan'] = $saldo->where('tipe', 0)->where('trxdate', '>=', $data['start_date'] . ' 00:00:00')->where('trxdate', '<=', $data['end_date'] . ' 23:59:59')->sum('total');
        $data['pengurangan'] = $saldo->where('tipe', 1)->where('trxdate', '>=', $data['start_date'] . ' 00:00:00')->where('trxdate', '<=', $data['end_date'] . ' 23:59:59')->sum('total');
        $data['saldo_akhir'] = $data['saldo_awal'] + $data['penambahan'] - $data['pengurangan'];
        $data['region'] = $this->master->regionGet($data['region_id']);
        $data['regionlist'] = $this->master->regionList();
        $data['active_menu'] = 'store-report';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Laporan' => route('storeReport'),
            'Rekapitulasi Piutang' => route('storeReportPiutang'),
            $data['region']->name => url()->current(),
        ];
        return view('store.report-piutang-detail', compact('data'));
    }
    public function reportPiutangDetailPrint()
    {
        $data['region_id'] = $_GET['region_id'];
        $data['status'] = $_GET['status'] ?? '1';
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data['data'] = $this->master->memberList($data);
        foreach ($data['data'] as $key => $value) {
            $data['data'][$key]['saldo_awal'] = $value->utangHistori->where('tipe', 0)->where('trxdate', '<', $data['start_date'] . ' 00:00:00')->sum('total') - $value->utangHistori->where('tipe', 1)->where('trxdate', '<=', $data['start_date'] . ' 00:00:00')->sum('total');
            $data['data'][$key]['penambahan'] = $value->utangHistori->where('tipe', 0)->where('trxdate', '>=', $data['start_date'] . ' 00:00:00')->where('trxdate', '<=', $data['end_date'] . ' 23:59:59')->sum('total');
            $data['data'][$key]['pengurangan'] = $value->utangHistori->where('tipe', 1)->where('trxdate', '>=', $data['start_date'] . ' 00:00:00')->where('trxdate', '<=', $data['end_date'] . ' 23:59:59')->sum('total');
            $data['data'][$key]['saldo_akhir'] = $data['data'][$key]['saldo_awal'] + $data['data'][$key]['penambahan'] - $data['data'][$key]['pengurangan'];
        }
        $data['region'] = $this->master->regionGet($data['region_id']);
        $data['assignment'] = $this->master->pengurusAssignment();
        return view('store.report-piutang-detail-print', compact('data'));
    }
    public function reportPiutangDetailDownload()
    {
        $data['region_id'] = $_GET['region_id'];
        $data['status'] = $_GET['status'] ?? '1';
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data['data'] = $this->master->memberList($data);
        $export['header'] = ['No', 'Kode Anggota', 'Nama Anggota', 'Piutang s/d ' . $data['start_date'] . ' (Rp)', 'Penambahan (Rp)', 'Pengurangan (Rp)', 'Total Piutang (Rp)'];
        $export['data'] = [];
        $i = $export['saldo_awal'] = $export['penambahan'] = $export['pengurangan'] = $export['saldo_akhir'] = 0;
        foreach ($data['data'] as $key => $value) {
            $i++;
            $saldo_awal = $value->utangHistori->where('tipe', 0)->where('trxdate', '<', $data['start_date'] . ' 00:00:00')->sum('total') - $value->utangHistori->where('tipe', 1)->where('trxdate', '<=', $data['start_date'] . ' 00:00:00')->sum('total');
            $penambahan = $value->utangHistori->where('tipe', 0)->where('trxdate', '>=', $data['start_date'] . ' 00:00:00')->where('trxdate', '<=', $data['end_date'] . ' 23:59:59')->sum('total');
            $pengurangan = $value->utangHistori->where('tipe', 1)->where('trxdate', '>=', $data['start_date'] . ' 00:00:00')->where('trxdate', '<=', $data['end_date'] . ' 23:59:59')->sum('total');
            $saldo_akhir = $saldo_awal + $penambahan - $pengurangan;
            $export['data'][$key] = [
                $i,
                $value->code,
                $value->name,
                number_format($saldo_awal, 2, ',', '.'),
                number_format($penambahan, 2, ',', '.'),
                number_format($pengurangan, 2, ',', '.'),
                number_format($saldo_akhir, 2, ',', '.'),
            ];
            $export['saldo_awal'] += $saldo_awal;
            $export['penambahan'] += $penambahan;
            $export['pengurangan'] += $pengurangan;
            $export['saldo_akhir'] += $saldo_akhir;
        }
        $region = $this->master->regionGet($data['region_id']);
        $export['periode'] = 'Periode ' . $data['start_date'] . ' s/d ' . $data['end_date'];
        $export['wilayah'] = $region->name;
        $export['anggota'] = $data['status'] ? 'Anggota' : 'Non Anggota';
        return Excel::download(new StoreReportPiutangDetailExport($export), 'Rekapitulasi Piutang ' . $region->name . '.xlsx');
    }
    public function reportPiutangDetailAnggota($id)
    {
        $data['limit'] = $_GET['limit'] ?? 25;
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data['data'] = $this->master->memberGet($id);
        $filter = [
            'member_id' => $data['data']->id,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
        ];
        $data['list'] = $this->store->saleDebtHistoryList($filter, $data['limit'], ['trxdate', 'asc']);
        $data['saldo_awal'] = $data['data']->utangHistori->where('tipe', 0)->where('trxdate', '<', $data['start_date'] . ' 00:00:00')->sum('total') - $data['data']->utangHistori->where('tipe', 1)->where('trxdate', '<=', $data['start_date'] . ' 00:00:00')->sum('total');
        if ($data['list']->currentPage() != 1) {
            $limit = ($data['list']->currentPage() - 1) * $data['limit'];
            $list = $this->store->saleDebtHistoryList($filter, $limit, ['trxdate', 'asc'], false);
            $data['saldo_awal'] += $list->where('tipe', 0)->sum('total') - $list->where('tipe', 1)->sum('total');
        }
        $data['active_menu'] = 'store-report';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Laporan' => route('storeReport'),
            'Rekapitulasi Piutang' => route('storeReportPiutang'),
            $data['data']->region->name => route('storeReportPiutangDetail', ['region_id' => $data['data']->region_id]),
            $data['data']->name => url()->current(),
        ];
        return view('store.report-piutang-detail-anggota', compact('data'));
    }
    public function reportPiutangDetailAnggotaPrint($id)
    {
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data['data'] = $this->master->memberGet($id);
        $filter = [
            'member_id' => $data['data']->id,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
        ];
        $data['list'] = $this->store->saleDebtHistoryList($filter, false, ['trxdate', 'asc']);
        $data['saldo_awal'] = $data['data']->utangHistori->where('tipe', 0)->where('trxdate', '<', $data['start_date'] . ' 00:00:00')->sum('total') - $data['data']->utangHistori->where('tipe', 1)->where('trxdate', '<=', $data['start_date'] . ' 00:00:00')->sum('total');

        $data['assignment'] = $this->master->pengurusAssignment();
        return view('store.report-piutang-detail-anggota-print', compact('data'));
    }
    public function reportPiutangDetailAnggotaDownload($id)
    {
        $data['start_date'] = $_GET['start_date'] ?? config('config_apps.journal_periode_start');
        $data['end_date'] = $_GET['end_date'] ?? config('config_apps.journal_periode_end');
        $data['data'] = $this->master->memberGet($id);
        $filter = [
            'member_id' => $data['data']->id,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
        ];
        $data['list'] = $this->store->saleDebtHistoryList($filter, false, ['trxdate', 'asc']);
        $export['saldo_awal'] = $saldo = $data['data']->utangHistori->where('tipe', 0)->where('trxdate', '<', $data['start_date'] . ' 00:00:00')->sum('total') - $data['data']->utangHistori->where('tipe', 1)->where('trxdate', '<=', $data['start_date'] . ' 00:00:00')->sum('total');
        $export['title'] = 'Buku Besar Pembantu Piutang ' . ($data['data']->status == 1 ? 'Anggota' : 'Non Anggota');
        $export['periode'] = 'Periode ' . $data['start_date'] . ' s/d ' . $data['end_date'];
        $export['header'] = ['#', 'Tanggal Transaksi', 'No Ref', 'Keterangan', 'Debit (Rp)', 'Kredit (Rp)', 'Saldo (Rp)'];
        $export['anggota'] = $data['data']->name;
        $export['wilayah'] = $data['data']->region->name;
        $export['data'] = [];
        $i = 0;
        foreach ($data['list'] as $key => $value) {
            $i++;
            if ($value->tipe) {
                $debit = $value->total;
                $kredit = 0;
            } else {
                $debit = 0;
                $kredit = $value->total;
            }
            $saldo += $kredit - $debit;
            $export['data'][$key] = [
                $i,
                $value->trxdate,
                $value->no_ref,
                $value->note,
                number_format($kredit, 2, ',', '.'),
                number_format($debit, 2, ',', '.'),
                number_format($saldo, 2, ',', '.')
            ];
        }
        $export['saldo_akhir'] = $saldo;
        // dd($data, $export);
        return Excel::download(new StoreReportPiutangDetailAnggotaExport($export), $export['title'] . '.xlsx');
    }
    public function reportPiutangAdd()
    {
        $data['member'] = $this->master->memberList();
        $data['active_menu'] = 'store-report';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Laporan' => route('storeReport'),
            'Rekapitulasi Piutang' => route('storeReportPiutang'),
            'Tambah Transaksi' => url()->current(),
        ];
        return view('store.report-piutang-form', compact('data'));
    }
    public function reportPiutangSave(RekapitulasiPiutangRequest $request)
    {
        $data = $request->validated();
        $data['total'] = str_replace(',', '', $data['total']);
        $data['trxdate'] = $data['trxdate'] . date(' H:i:s');
        $member = $this->master->memberGet($data['member_id']);
        $data['region_id'] = $member->region_id;
        $data['member_stat'] = $member->status;
        // dd($data);
        $this->store->saleDebtHistorySave($data);
        return redirect()->route('storeReportPiutang')->with(['success' => 'Data berhasil diinput.']);
    }
    public function reportPiutangUpload()
    {
        $data['limit'] = $_GET['limit'] ?? 25;
        $data['q'] = $_GET['q'] ?? '';
        $data['data'] = $this->store->reportPiutangUploadList($data, $data['limit']);
        $data['active_menu'] = 'store-report';
        $data['breadcrumb'] = [
            'Toko' => route('store'),
            'Laporan' => route('storeReport'),
            'Rekapitulasi Piutang' => route('storeReportPiutang'),
            'Upload Transaksi' => url()->current(),
        ];
        if (isset($_GET['confirm'])) {
            $this->store->reportPiutangUploadConfirm($_GET['confirm']);
            if ($_GET['confirm'] == 0) {
                return redirect()->route('storeReportPiutangUpload')->with(['info' => 'Upload data rekapitulasi piutang dibatalkan.']);
            } else {
                return redirect()->route('storeReportPiutang')->with(['success' => 'Upload data rekapitulasi piutang berhasil.']);
            }
        }
        return view('store.report-piutang-upload', compact('data'));
    }
    public function reportPiutangUploadSave(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);
        $file = $request->file('file')->storeAs('import/member', date('YmdHis-') . $_FILES['file']['name']);
        Excel::import(new StoreRekapitulasiPiutangImport, $file);
        return redirect()->route('storeReportPiutangUpload')->with(['info' => 'Pastikan data telah sesuai, lalu klik tombol konfirmasi.']);
    }
    /*
    * ========================================================================================== END LAPORAN ==========================================================================================
    */
}