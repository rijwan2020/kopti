<?php

namespace App\Http\Controllers;

use App\Classes\DepositClass;
use App\Exports\DepositExport;
use App\Repositories\Akun;
use App\Repositories\Pengurus;
use App\Repositories\Simpanan;
use App\Repositories\SimpananJenis;
use App\Repositories\SimpananUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Maatwebsite\Excel\Facades\Excel;

class SimpananController extends Controller
{
    private $jenis, $akun, $simpanan, $pengurus, $simpanan_upload, $deposit;
    public function __construct(
        Simpanan $simpanan,
        SimpananJenis $jenis,
        Akun $akun,
        Pengurus $pengurus,
        SimpananUpload $simpanan_upload,
        DepositClass $deposit
    ){
        $this->middleware('auth');
        $this->middleware('role');
        Config::set('title', 'Unit Simpanan');
        $this->simpanan = $simpanan;
        $this->jenis = $jenis;
        $this->akun = $akun;
        $this->pengurus = $pengurus;
        $this->simpanan_upload = $simpanan_upload;
        $this->deposit = $deposit;
    }

    public function index()
    {
        $data['limit'] = $filter['limit'] = $_GET['limit'] ?? 20;
        $data['q'] = $filter['q'] = $_GET['q'] ?? '';
        $data['type_id'] = $_GET['type_id'] ?? 'all';
        if($data['type_id'] != 'all'){
            $filter['deposit_type_id'] = $data['type_id'];
        }
        $filter['relations'] = ['region', 'type', 'member'];

        if (auth()->user()->isMember()) {
            $data['member_id'] = $filter['member_id'] = auth()->user()->member->id;
        }

        $data['data'] = $this->simpanan->list($filter);
        $data['type'] = $this->jenis->list();
        $data['active_menu'] = 'deposit';
        $data['breadcrumb'] = [
            'Simpanan' => url()->current()
        ];
        return view('deposit.deposit-list', compact('data'));
    }

    public function create()
    {
        $data['type'] = $this->jenis->list();
        $type = $this->jenis->get(3);
        $data['data'] = [
            'account_number' => $type->code . '-' . str_pad($type->next_code, 9, 0, STR_PAD_LEFT),
            'deposit_type_id' => 3
        ];
        $data['account'] = $this->akun->list(['group_id' => 1]);
        $data['mode'] = 'add';
        $data['active_menu'] = 'deposit';
        $data['breadcrumb'] = [
            'Simpanan' => route('depositList'),
            'Tambah Data' => url()->current()
        ];
        return view('deposit.deposit-form', compact('data'));
    }

    public function print()
    {
        $data['q'] = $_GET['q'] ?? '';
        if($_GET['type_id'] != 'all'){
            $data['deposit_type_id'] = $_GET['type_id'];
        }
        $data['relations'] = ['member', 'region', 'type'];
        $data['data'] = $this->simpanan->list($data);
        $data['assignment'] = $this->pengurus->assignment();
        return view('deposit.deposit-print-all', compact('data'));
    }
    
    public function download()
    {
        $data['q'] = $_GET['q'] ?? '';
        if($_GET['type_id'] != 'all'){
            $data['deposit_type_id'] = $_GET['type_id'];
        }
        $data['relations'] = ['member', 'region', 'type'];
        $data['data'] = $this->simpanan->list($data);
        $export['data'] = [];
        $i = 0;
        foreach ($data['data'] as $key => $value) {
            $i++;
            $export['data'][$key]['no'] = $i;
            $export['data'][$key]['kode'] = $value->member->code;
            $export['data'][$key]['nama'] = $value->member->name;
            $export['data'][$key]['no_rek'] = $value->account_number;
            $export['data'][$key]['wilayah'] = $value->region->name;
            $export['data'][$key]['jenis'] = $value->type->name;
            $export['data'][$key]['tgl_reg'] = $value->registration_date;
            $export['data'][$key]['last_trx'] = $value->last_transaction;
            $export['data'][$key]['saldo'] = 'Rp' . number_format($value->balance, 2, ',', '.');
        }
        $export['total_row'] = $data['data']->count();
        $export['saldo'] = $data['data']->sum('balance');
        return Excel::download(new DepositExport($export), 'Data Simpanan.xlsx');
    }
    
    public function delete($id)
    {
        $data['data'] = $this->simpanan->get($id);
        if ($data['data'] == false) {
            return redirect()->route('depositList')->with(['warning' => 'Data simpanan tidak ditemukan.']);
        }
        if ($data['data']->deleted_by != 0) {
            return redirect()->route('depositList')->with(['warning' => 'Data simpanan tidak ditemukan.']);
        }
        $data['cash'] = $this->akun->list(['group_id' => 1]);
        $data['active_menu'] = 'deposit';
        $data['breadcrumb'] = [
            'Simpanan' => route('depositList'),
            'Hapus' => url()->current()
        ];
        return view('deposit.deposit-delete', compact('data'));
    }

    public function upload()
    {
        $data['limit'] = $_GET['limit'] ?? 25;
        $data['q'] = $_GET['q'] ?? '';
        $data['data'] = $this->simpanan_upload->list($data);
        $data['type'] = $this->jenis->list();
        $data['cash'] = $this->akun->list(['group_id' => 1]);
        $data['active_menu'] = 'deposit';
        $data['breadcrumb'] = [
            'Simpanan' => route('depositList'),
            'Upload' => url()->current()
        ];
        if (isset($_GET['confirm'])) {
            $this->deposit->depositUploadConfirm($_GET['confirm']);
            if ($_GET['confirm'] == 0) {
                return redirect()->route('depositUpload')->with(['info' => 'Upload data simpanan dibatalkan.']);
            } else {
                return redirect()->route('depositList')->with(['success' => 'Upload data simpanan berhasil.']);
            }
        }
        return view('deposit.deposit-upload', compact('data'));
    }
}
