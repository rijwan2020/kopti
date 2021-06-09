<?php

namespace App\Http\Controllers;

use App\Classes\AccountancyClass;
use App\Classes\AreaClass;
use App\Classes\DepositClass;
use App\Classes\MasterClass;
use App\Classes\StoreClass;
use App\Services\Saldo;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $store, $deposit, $master, $area, $pembukuan, $saldo;
    public function __construct(
        Saldo $saldo
    ){
        $this->middleware('auth');
        $this->area = new AreaClass();
        $this->master = new MasterClass();
        $this->deposit = new DepositClass();
        $this->store = new StoreClass();
        $this->pembukuan = new AccountancyClass();
        $this->saldo = $saldo;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $data['config'] = config('config_apps');
        $filter = false;
        if (auth()->user()->isMember()) {
            $filter['member_id'] = auth()->user()->member->id;
        }
        $current_month = date('m');
        for ($i = 1; $i <= $current_month; $i++) {
            $fulldate = date('Y-' . (str_pad($i, 2, 0, STR_PAD_LEFT)) . '-01');
            $filter['end_date'] = date('Y-m-t', strtotime($fulldate));

            $data['simpanan_tahunan'][$i]['bulan'] = $data['grafik_stok'][$i]['bulan'] = date('M', strtotime($fulldate));
            $simpanan = $this->deposit->depositTransactionSum($filter);
            $data['simpanan_tahunan'][$i]['total'] = $simpanan->kredit - $simpanan->debit;

            $data['grafik_stok'][$i]['penambahan'] = $this->store->itemPenambahan(['start_date' => $fulldate, 'end_date' => $filter['end_date']]);
            $data['grafik_stok'][$i]['pengurangan'] = $this->store->itemPengurangan(['start_date' => $fulldate, 'end_date' => $filter['end_date']]);
        }
        unset($filter['end_date']);
        $data['total_simpanan'] = $this->deposit->depositSum($filter);
        $data['total_rekening'] = $this->deposit->depositCount($filter);
        $filter['start_date'] = date('Y-m-d');
        $data['simpanan_hari_ini'] = $this->deposit->depositTransactionSum($filter);

        $data['member'] = $this->master->memberList();
        $data['region'] = $this->master->regionList();

        $data['stok'] = $this->store->itemList()->sum('qty');
        $data['penambahan'] = $this->store->itemPenambahan(['start_date' => $data['config']['journal_periode_start'], 'end_date' => $data['config']['journal_periode_end']]);
        $data['pengurangan'] = $this->store->itemPengurangan(['start_date' => $data['config']['journal_periode_start'], 'end_date' => $data['config']['journal_periode_end']]);

        $akun = $this->saldo->saldo([
            'start_date' => $data['config']['journal_periode_start'],
            'end_date' => $data['config']['journal_periode_end'],
            'view' => 'all'
        ]);
        $pendapatan = $beban = $shu = 0;
        $pendapatan_lalu = $beban_lalu = $shu_lalu = 0;
        foreach ($akun as $key => $value) {
            if ($value['code'][1] == 4) {
                if ($value['type'] == 1) {
                    $pendapatan += $value['adjusting_balance'];
                    $pendapatan_lalu += $value['saldo_tahun_lalu'];
                } else {
                    $pendapatan -= $value['adjusting_balance'];
                    $pendapatan_lalu -= $value['saldo_tahun_lalu'];
                }
            }
            if ($value['code'][1] == 5) {
                if ($value['type'] == 0) {
                    $beban += $value['adjusting_balance'];
                    $beban_lalu += $value['saldo_tahun_lalu'];
                } else {
                    $beban -= $value['adjusting_balance'];
                    $beban_lalu -= $value['saldo_tahun_lalu'];
                }
            }
            if ($value['code'] == $data['config']['shu_account']) {
                $shu = $value['adjusting_balance'];
                $shu_lalu = $value['saldo_tahun_lalu'];
            }
        }
        $total_shu = $shu + $pendapatan - $beban;
        $data['shu'] = $total_shu - (2.5 * $total_shu / 100);
        $total_shu_lalu = $shu_lalu + $pendapatan_lalu - $beban_lalu;
        $data['shu_lalu'] = $total_shu_lalu - (2.5 * $total_shu_lalu / 100);

        $data['active_menu'] = 'home';
        $data['breadcrumb'] = [];
        return view('home', compact('data'));
    }
    public function getVillage(Request $request)
    {
        if ($request->has('q')) {
            $desa = $this->area->villageList(['q' => $request->q]);
            $data = [];
            $i = 0;
            foreach ($desa as $key => $value) {
                $data[$i]['id'] = $value->id;
                $data[$i]['name'] = $value->name;
                $data[$i]['district_name'] = $value->district->name;
                $data[$i]['regency_name'] = $value->regency->name;
                $data[$i]['province_name'] = $value->province->name;
                $i++;
            }
            return response()->json($data);
        }
    }
    public function getMember(Request $request)
    {
        if ($request->has('q')) {
            $member = $this->master->memberList(['q' => $request->q]);
            $data = [];
            $i = 0;
            foreach ($member as $key => $value) {
                if ($value->status != 2) {
                    $data[$i]['id'] = $value->id;
                    $data[$i]['code'] = $value->code;
                    $data[$i]['name'] = $value->name;
                    $data[$i]['status'] = $value->status;
                    $i++;
                }
            }
            return response()->json($data);
        }
    }
    public function getNoRek($id)
    {
        $type = $this->deposit->depositTypeGet($id);
        $data = $type->code . '-' . str_pad($type->next_code, 9, 0, STR_PAD_LEFT);
        return response()->json($data);
    }
    public function getItem(Request $request)
    {
        $data = [];
        if ($request->has('q')) {
            $barang = $this->store->itemList(['q' => $request->q]);
            $i = 0;
            foreach ($barang as $key => $value) {
                $data[$i] = $value->toArray();
                $data[$i]['text'] = '[' . $value->code . '] - ' . $value->name;
                $i++;
            }
        }
        return response()->json($data);
    }
    public function getItemJual(Request $request)
    {
        if (auth()->user()->isGudang()) {
            $warehouse_id = auth()->user()->getWarehouseId();
        } else {
            $warehouse_id = 0;
        }
        $data = [];
        if ($request->has('q')) {
            $barang = $this->store->itemList(['q' => $request->q]);
            $i = 0;
            foreach ($barang as $key => $value) {
                $qty = $this->store->getQty(['warehouse_id' => $warehouse_id, 'item_id' => $value->id]);
                if ($qty > 0) {
                    $data[$i] = $value->toArray();
                    $data[$i]['text'] = '[' . $value->code . '] - ' . $value->name;
                    $data[$i]['qty'] = $qty;
                    // $data[$i]['harga_jual'] = fmod($value->qty, 1) !== 0.00 ? number_format($value->harga_jual, 2, ',', '.') : number_format($value->harga_jual, 0, ',', '.');
                    $i++;
                }
            }
        }
        return response()->json($data);
    }
}