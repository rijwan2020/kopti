<?php

namespace App\Http\Controllers;

use App\Classes\DepositClass;
use App\Exports\DepositDetailExport;
use App\Http\Requests\DepositTransactionRequest;
use App\Repositories\Akun;
use App\Repositories\Pengurus;
use App\Repositories\Simpanan;
use App\Repositories\SimpananJenis;
use App\Repositories\SimpananTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Maatwebsite\Excel\Facades\Excel;

class SimpananDetailController extends Controller
{
    private $jenis, $akun, $simpanan, $pengurus, $simpanan_upload, $deposit, $transaksi;
    public function __construct(
        Simpanan $simpanan,
        SimpananJenis $jenis,
        Akun $akun,
        Pengurus $pengurus,
        SimpananTransaksi $transaksi,
        DepositClass $deposit
    ){
        $this->middleware('auth');
        $this->middleware('role');
        Config::set('title', 'Unit Simpanan');
        $this->simpanan = $simpanan;
        $this->jenis = $jenis;
        $this->akun = $akun;
        $this->pengurus = $pengurus;
        $this->transaksi = $transaksi;
        $this->deposit = $deposit;
    }
    
    public function index($id)
    {
        $data['deposit'] = $this->simpanan->get($id);
        if (!$data['deposit']) {
            return redirect()->route('depositList')->with(['warning' => 'Data simpanan tidak ditemukan.']);
        }
        $data['limit'] = $_GET['limit'] ?? 25;
        $data['q'] = $_GET['q'] ?? '';
        $data['start_date'] = $_GET['start_date'] ?? '';
        $data['end_date'] = $_GET['end_date'] ?? '';
        $data['deposit_id'] = $data['deposit']->id;
        $data['data'] = $this->deposit->depositTransactionList($data, $data['limit'], ['transaction_date', 'desc']);
        $data['total_kredit'] = $this->deposit->depositTransactionList($data)->sum('kredit');
        $data['total_debit'] = $this->deposit->depositTransactionList($data)->sum('debit');
        $data['type_transaction'] = [
            1 => 'Setoran',
            2 => ' Penarikan',
            3 => 'Jasa',
            4 => 'Administrasi',
            5 => 'Penyesuaian Setoran',
            6 => 'Penyesuaian Penarikan'
        ];
        $data['active_menu'] = 'deposit';
        $data['breadcrumb'] = [
            'Simpanan' => route('depositList'),
            'Detail : ' . $data['deposit']->account_number => url()->current()
        ];
        return view('deposit.deposit-detail-list', compact('data'));
    }

    public function create($id)
    {
        $data['data'] = $this->simpanan->get($id);
        $data['type_transaction'][1] = 'Setoran';
        if ($data['data']->deposit_type_id != 2) {
            $data['type_transaction'][2] = 'Penarikan';
            $data['balance'] = 0;
            if ($data['data']->deposit_type_id == 1) {
                $data['balance'] = config('config_apps.besar_sp') - $data['data']->balance;
            }
        } else {
            $data['balance'] = config('config_apps.besar_sw');
        }
        $data['type_transaction'][3] = 'Jasa';
        $data['type_transaction'][4] = 'Administrasi';
        $data['type_transaction'][5] = 'Penyesuaian Setoran';
        $data['type_transaction'][6] = 'Penyesuaian Penarikan';
        $data['cash'] = $this->akun->list(['group_id' => 1]);
        $data['active_menu'] = 'deposit';
        $data['breadcrumb'] = [
            'Simpanan' => route('depositList'),
            'Detail : ' . $data['data']->account_number => route('depositDetail', ['id' => $data['data']->id]),
            'Tambah Transaksi' => url()->current(),
        ];
        return view('deposit.deposit-detail-form', compact('data'));
    }

    public function preview(DepositTransactionRequest $request)
    {
        $data['data'] = $request->validated();
        if (in_array($data['data']['type'], [1, 3, 5])) {
            $data['data']['kredit'] = str_replace(',', '', $request->balance);
            $data['data']['debit'] = 0;
        } else {
            $data['data']['debit'] = str_replace(',', '', $request->balance);
            $data['data']['kredit'] = 0;
        }

        $data['deposit'] = $this->simpanan->get($data['data']['deposit_id']);
        if ($data['deposit']->deposit_type_id == 2) {
            $data['data']['month'] = $request->month;
        }
        $data['type_transaction'] = [
            1 => 'Setoran',
            2 => ' Penarikan',
            3 => 'Jasa',
            4 => 'Administrasi',
            5 => 'Penyesuaian Setoran',
            6 => 'Penyesuaian Penarikan'
        ];
        $data['data']['note'] = $data['type_transaction'][$data['data']['type']] . ' ' . $data['deposit']->account_number . ' (' . $data['data']['note'] . ')';
        $data['account'] = $this->akun->get(['code', $data['data']['account']]);
        $data['active_menu'] = 'deposit';
        $data['breadcrumb'] = [
            'Simpanan' => route('depositList'),
            'Detail : ' . $data['deposit']->account_number => route('depositDetail', ['id' => $data['deposit']->id]),
            'Tambah Transaksi' => route('depositDetailAdd', ['id' => $data['deposit']->id]),
        ];
        return view('deposit.deposit-detail-preview', compact('data'));
    }

    public function print($id)
    {
        $data['deposit'] = $this->simpanan->get($id);
        if (!$data['deposit']) {
            return redirect()->route('depositList')->with(['warning' => 'Data simpanan tidak ditemukan.']);
        }
        $data['q'] = $_GET['q'] ?? '';
        $data['start_date'] = $_GET['start_date'] ?? '';
        $data['end_date'] = $_GET['end_date'] ?? '';
        $data['deposit_id'] = $data['deposit']->id;
        $data['data'] = $this->deposit->depositTransactionList($data, false, ['transaction_date', 'asc']);
        $data['type_transaction'] = [
            1 => 'Setoran',
            2 => ' Penarikan',
            3 => 'Jasa',
            4 => 'Administrasi',
            5 => 'Penyesuaian Setoran',
            6 => 'Penyesuaian Penarikan'
        ];
        $data['assignment'] = $this->pengurus->assignment();
        return view('deposit.deposit-detail-print', compact('data'));
    }

    public function download($id)
    {
        $data['deposit'] = $this->simpanan->get($id);
        if (!$data['deposit']) {
            return redirect()->route('depositList')->with(['warning' => 'Data simpanan tidak ditemukan.']);
        }
        $data['q'] = $_GET['q'] ?? '';
        $data['start_date'] = $_GET['start_date'] ?? '';
        $data['end_date'] = $_GET['end_date'] ?? '';
        $data['deposit_id'] = $data['deposit']->id;
        $data['data'] = $this->deposit->depositTransactionList($data, false, ['transaction_date', 'asc']);
        $data['type_transaction'] = [
            1 => 'Setoran',
            2 => ' Penarikan',
            3 => 'Jasa',
            4 => 'Administrasi',
            5 => 'Penyesuaian Setoran',
            6 => 'Penyesuaian Penarikan'
        ];

        $export['header'] = [
            'account_number' => $data['deposit']->account_number,
            'code' => $data['deposit']->member->code,
            'name' => $data['deposit']->member->name,
            'type' => $data['deposit']->type->name,
            'region' => $data['deposit']->region->name,
            'balance' => $data['deposit']->balance,
            'total_debit' => $data['data']->sum('debit'),
            'total_kredit' => $data['data']->sum('kredit'),
        ];
        $export['data'] = [];
        $i = 1;
        foreach ($data['data'] as $key => $value) {
            $export['data'][$key]['no'] = $i++;
            $export['data'][$key]['no_ref'] = $value->reference_number;
            $export['data'][$key]['note'] = $value->note;
            $export['data'][$key]['date'] = $value->transaction_date;
            $export['data'][$key]['type'] = '[' . str_pad($value->type, 2, 0, STR_PAD_LEFT) . '] - ' . $data['type_transaction'][$value['type']];
            $export['data'][$key]['kredit'] = number_format($value->kredit, 2, ',', '.');
            $export['data'][$key]['debit'] = number_format($value->debit, 2, ',', '.');
        }
        $export['total_row'] = $data['data']->count();
        return Excel::download(new DepositDetailExport($export), 'Data Transaksi ' . $data['deposit']->account_number . '.xlsx');
    }
}
