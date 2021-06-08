<?php

namespace App\Http\Controllers;

use App\Repositories\Akun;
use App\Repositories\AkunGrup;
use App\Repositories\Simpanan;
use App\Repositories\SimpananJenis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class SimpananJenisController extends Controller
{
    private $jenis, $akun, $akungrup, $simpanan;
    public function __construct(
        SimpananJenis $jenis, 
        Akun $akun, 
        AkunGrup $akungrup,
        Simpanan $simpanan
    ){
        $this->middleware('auth');
        $this->middleware('role');
        Config::set('title', 'Data Master');
        $this->jenis = $jenis;
        $this->akun = $akun;
        $this->akungrup = $akungrup;
        $this->simpanan = $simpanan;
    }

    public function index()
    {
        $data['limit'] = $_GET['list'] ?? 10;
        $data['q'] = $_GET['q'] ?? '';
        $data['relations'] = ['deposit'];
        $data['data'] = $this->jenis->list($data);
        $data['active_menu'] = 'deposit-type';
        $data['breadcrumb'] = [
            'Simpanan' => route('depositList'),
            'Jenis Simpanan' => url()->current()
        ];
        return view('deposit.deposit-type-list', compact('data'));
    }

    public function create()
    {
        $data['mode'] = 'add';
        $data['account'] = $this->akun->list(['level' => 2]);
        $data['group'] = $this->akungrup->list();
        $data['active_menu'] = 'deposit-type';
        $data['breadcrumb'] = [
            'Simpanan' => route('depositList'),
            'Jenis Simpanan' => route('depositTypeList'),
            'Tambah' => url()->current()
        ];
        return view('deposit.deposit-type-form', compact('data'));
    }

    public function edit($id)
    {
        $data['data'] = $this->jenis->get($id);
        if ($data['data'] == false) {
            return redirect()->route('depositTypeList')->with(['warning' => 'Data jenis simpanan tidak ditemukan.']);
        }
        $data['mode'] = 'edit';
        $data['active_menu'] = 'deposit-type';
        $data['breadcrumb'] = [
            'Simpanan' => route('depositList'),
            'Jenis Simpanan' => route('depositTypeList'),
            'Edit: ' . $data['data']->name => url()->current()
        ];
        return view('deposit.deposit-type-form', compact('data'));
    }
    
    public function delete($id)
    {
        $depositType = $this->jenis->get($id);
        if ($depositType == false) {
            return redirect()->route('depositTypeList')->with(['warning' => 'Data jenis simpanan tidak ditemukan.']);
        }
        // cek simpanan
        if($this->simpanan->list(['deposit_type_id' => $id])->count() > 0){
            return redirect()->route('depositTypeList')->with(['warning' => 'Data jenis simpanan tidak dapat dihapus.']);
        }
        $account = $this->akun->get(['code', $depositType->account_code]);
        if ($account) {
            if ($account->ending_balance > 0 || $account->beginning_balance > 0) {
                return redirect()->route('depositTypeList')->with(['warning' => 'Data jenis simpanan tidak dapat dihapus.']);
            }
            $account->delete();
        }
        $depositType->delete();
        return redirect()->route('depositTypeList')->with(['success' => 'Data jenis simpanan berhasil dihapus.']);
    }
}
