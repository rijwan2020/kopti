<?php

namespace App\Http\Controllers;

use App\Repositories\Anggota;
use App\Repositories\Karyawan;
use App\Repositories\Position;
use App\Repositories\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class KaryawanController extends Controller
{
    private $posisi, $karyawan, $anggota, $user;
    public function __construct(
        Karyawan $karyawan, 
        Position $position, 
        Anggota $anggota, 
        User $user
    ){
        $this->middleware('auth');
        $this->middleware('role');
        Config::set('title', 'Data Master');
        $this->karyawan = $karyawan;
        $this->anggota = $anggota;
        $this->posisi = $position;
        $this->user = $user;
    }

    public function index()
    {
        $data['q'] = $_GET['q'] ?? '';
        $data['limit'] = $_GET['limit'] ?? 10;
        $data['position_id'] = $_GET['position_id'] ?? 'all';

        $filter = [
            'q' => $data['q'],
            'limit' => $data['limit'],
        ];
        if($data['position_id'] != 'all'){
            $filter['position_id'] = $data['position_id'];
        }

        $filter['relations'] = ['user', 'position'];
        $data['data'] = $this->karyawan->list($filter);
        $data['position'] = $this->posisi->list(['type' => 1]);
        $data['active_menu'] = 'employee';
        $data['breadcrumb'] = [
            'Data Karyawan' => url()->current()
        ];
        return view('master.employee-list', compact('data'));
    }

    
    public function create()
    {
        $data['mode'] = 'add';
        $data['position'] = $this->posisi->list(['type' => 1]);
        $data['active_menu'] = 'employee';
        $data['breadcrumb'] = [
            'Data Pengurus' => route('employeeList'),
            'Tambah' => url()->current(),
        ];
        return view('master.employee-form', compact('data'));
    }
    
    public function edit($id)
    {
        $data['data'] = $this->karyawan->get($id);
        if ($data['data'] == null) {
            return redirect()->route('employeeList')->with(['warning' => 'Data karyawan tidak ditemukan.']);
        }
        $data['mode'] = 'edit';
        $data['position'] = $this->posisi->list(['type' => 1]);
        $data['active_menu'] = 'employee';
        $data['breadcrumb'] = [
            'Data Karyawan' => route('employeeList'),
            'Edit: ' . $data['data']->name => url()->current(),
        ];
        return view('master.employee-form', compact('data'));
    }

    
    public function delete($id)
    {
        $employee = $this->karyawan->get($id);
        //if employee not exist
        if ($employee == null) {
            return redirect()->route('employeeList')->with(['warning' => 'Data karyawan tidak ditemukan.']);
        }
        $employee->delete();
        // get user employee
        $user = $this->user->get($employee->user_id);
        // delete user
        $user->deleted_by = auth()->user()->id ?? 1;
        $user->deleted_at = date('Y-m-d H:i:s');
        $user->username = base64_encode($user->username);
        $user->email = base64_encode($user->email);
        $user->update();
        return redirect()->route('employeeList')->with(['success' => 'Data karyawan berhasil dihapus.']);
    }
}
