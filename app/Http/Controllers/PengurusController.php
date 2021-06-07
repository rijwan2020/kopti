<?php

namespace App\Http\Controllers;

use App\Repositories\Anggota;
use App\Repositories\Pengurus;
use App\Repositories\Position;
use App\Repositories\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class PengurusController extends Controller
{
    private $posisi, $pengurus, $anggota, $user;
    public function __construct(
        Pengurus $pengurus, 
        Position $position, 
        Anggota $anggota, 
        User $user
    ){
        $this->middleware('auth');
        $this->middleware('role');
        Config::set('title', 'Data Master');
        $this->pengurus = $pengurus;
        $this->anggota = $anggota;
        $this->posisi = $position;
        $this->user = $user;
    }

    public function index()
    {
        $data['q'] = $_GET['q'] ?? '';
        $data['limit'] = $_GET['limit'] ?? 25;
        $data['position_id'] = $_GET['position_id'] ?? 'all';

        $filter = [
            'q' => $data['q'],
            'limit' => $data['limit'],
        ];
        if($data['position_id'] != 'all'){
            $filter['position_id'] = $data['position_id'];
        }
        $data['data'] = $this->pengurus->list($filter);
        $data['position'] = $this->posisi->list(['type' => 0]);
        $data['active_menu'] = 'management';
        $data['breadcrumb'] = [
            'Data Pengurus' => url()->current()
        ];
        return view('master.management-list', compact('data'));
    }

    public function create()
    {
        $data['mode'] = 'add';
        $data['member'] = $this->anggota->list(['status' => 1]);
        $data['position'] = $this->posisi->list(['type' => 0]);
        $data['active_menu'] = 'management';
        $data['breadcrumb'] = [
            'Data Pengurus' => route('managementList'),
            'Tambah' => url()->current(),
        ];
        return view('master.management-form', compact('data'));
    }
    
    public function edit($id)
    {
        $data['data'] = $this->pengurus->get($id);
        if ($data['data'] == null) {
            return redirect()->route('managementList')->with(['warning' => 'Data pengurus tidak ditemukan.']);
        }
        $data['mode'] = 'edit';
        $data['position'] = $this->posisi->list(['type' => 0]);
        $data['active_menu'] = 'management';
        $data['breadcrumb'] = [
            'Data Pengurus' => route('managementList'),
            'Edit: ' . $data['data']->member->name => url()->current(),
        ];
        return view('master.management-form', compact('data'));
    }
    
    public function delete($id)
    {
        $management = $this->pengurus->get($id);
        //if management not exist
        if ($management == null) {
            return redirect()->route('managementList')->with(['warning' => 'Data pengurus tidak ditemukan.']);
        }
        $management->delete();
        // get user management
        $user = $this->user->get($management->user_id);
        // delete user
        $user->deleted_by = auth()->user()->id ?? 1;
        $user->deleted_at = date('Y-m-d H:i:s');
        $user->username = base64_encode($user->username);
        $user->email = base64_encode($user->email);
        $user->update();
        return redirect()->route('managementList')->with(['success' => 'Data pengurus berhasil dihapus.']);
    }
}
