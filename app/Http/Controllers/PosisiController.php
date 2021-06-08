<?php

namespace App\Http\Controllers;

use App\Http\Requests\PositionRequest;
use App\Repositories\Karyawan;
use App\Repositories\Level;
use App\Repositories\Pengurus;
use App\Repositories\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class PosisiController extends Controller
{
    private $posisi, $pengurus, $level, $karyawan;
    public function __construct(
        Pengurus $pengurus, 
        Position $position,
        Level $level,
        Karyawan $karyawan
    ){
        $this->middleware('auth');
        $this->middleware('role');
        Config::set('title', 'Data Master');
        $this->pengurus = $pengurus;
        $this->posisi = $position;
        $this->level = $level;
        $this->karyawan = $karyawan;
    }

    public function jabatan()
    {
        $data['q'] = $_GET['q'] ?? '';
        $data['limit'] = $_GET['limit'] ?? 10;
        $data['type'] = 0;
        $data['data'] = $this->posisi->list($data);
        $data['active_menu'] = 'management';
        $data['breadcrumb'] = [
            'Data Pengurus' => route('managementList'),
            'Manage Jabatan' => url()->current()
        ];
        return view('master.management-position-list', compact('data'));
    }

    public function jabatanAdd()
    {
        $data['mode'] = 'add';
        $data['level'] = $this->level->list();
        $data['active_menu'] = 'management';
        $data['breadcrumb'] = [
            'Data Pengurus' => route('managementList'),
            'Manage Jabatan' => route('managementPositionList'),
            'Tambah' => url()->current(),
        ];
        return view('master.management-position-form', compact('data'));
    }
    
    protected function jabatanEdit($id)
    {
        //get this position
        $data['data'] = $this->posisi->get($id);
        if ($data['data'] == null) {
            return redirect()->route('managementPositionList')->with(['warning' => 'Data jabatan pengurus tidak ditemukan.']);
        }
        //this mode
        $data['mode'] = 'edit';
        $data['active_menu'] = 'management';
        $data['breadcrumb'] = [
            'Data Pengurus' => route('managementList'),
            'Manage Jabatan' => route('managementPositionList'),
            'Edit: ' . $data['data']->name => url()->current(),
        ];
        return view('master.management-position-form', compact('data'));
    }
    
    public function jabatanDelete($id)
    {
        $position = $this->posisi->get($id);
        if ($position == null) {
            return redirect()->route('managementPositionList')->with(['warning' => 'Data jabatan pengurus tidak ditemukan.']);
        }
        // check list management
        if ($this->pengurus->list(['position_id' => $id])->count() > 0) {
            return redirect()->route('managementPositionList')->with(['warning' => 'Data jabatan pengurus tidak dapat dihapus.']);
        }

        $position->delete();
        return redirect()->route('managementPositionList')->with(['success' => 'Data jabatan pengurus berhasil dihapus.']);
    }

    public function jabatanSave(PositionRequest $request)
    {
        $data = $request->validated();
        $data['type'] = 0;
        if ($request->mode == 'add') {
            //save position
            if (!$this->posisi->create($data)) {
                return back()->with(['warning' => $this->posisi->getError()]);
            }
            $message = 'Data jabatan pengurus berhasil ditambahkan.';
        } else {
            //update refion
            if (!$this->posisi->edit($request->id, $data)) {
                return back()->with(['warning' => $this->posisi->getError()]);
            }
            $message = 'Data jabatan pengurus berhasil diperbaharui.';
        }
        return redirect()->route('managementPositionList')->with(['success' => $message]);
    }

    public function index()
    {
        $data['q'] = $_GET['q'] ?? '';
        $data['limit'] = $_GET['limit'] ?? 10;
        $data['type'] = 1;
        $data['data'] = $this->posisi->list($data);
        $data['active_menu'] = 'employee';
        $data['breadcrumb'] = [
            'Data Karyawan' => route('employeeList'),
            'Manage Posisi' => url()->current()
        ];
        return view('master.employee-position-list', compact('data'));
    }
    public function add()
    {
        $data['mode'] = 'add';
        $data['level'] = $this->level->list();
        $data['active_menu'] = 'employee';
        $data['breadcrumb'] = [
            'Data Pengurus' => route('employeeList'),
            'Manage Posisi' => route('employeePositionList'),
            'Tambah' => url()->current(),
        ];
        return view('master.employee-position-form', compact('data'));
    }
    protected function edit($id)
    {
        //get this position
        $data['data'] = $this->posisi->get($id);
        if ($data['data'] == null) {
            return redirect()->route('employeePositionList')->with(['warning' => 'Data posisi karyawan tidak ditemukan.']);
        }
        //this mode
        $data['mode'] = 'edit';
        $data['active_menu'] = 'employee';
        $data['breadcrumb'] = [
            'Data Pengurus' => route('employeeList'),
            'Manage Posisi' => route('employeePositionList'),
            'Edit: ' . $data['data']->name => url()->current(),
        ];
        return view('master.employee-position-form', compact('data'));
    }
    public function save(PositionRequest $request)
    {
        $data = $request->validated();
        $data['type'] = 1;
        if ($request->mode == 'add') {
            //save position
            if (!$this->posisi->create($data)) {
                return back()->with(['warning' => $this->posisi->getError()]);
            }
            $message = 'Data posisi karyawan berhasil ditambahkan.';
        } else {
            //update refion
            if (!$this->posisi->edit($request->id, $data)) {
                return back()->with(['warning' => $this->posisi->getError()]);
            }
            $message = 'Data posisi karyawan berhasil diperbaharui.';
        }
        return redirect()->route('employeePositionList')->with(['success' => $message]);
    }
    public function delete($id)
    {
        $position = $this->posisi->get($id);
        if (!$position) {
            return redirect()->route('employeePositionList')->with(['warning' => 'Data posisi karyawan tidak ditemukan.']);
        }
        // check list employee
        if ($this->karyawan->list(['position_id' => $id])->count() > 0) {
            return redirect()->route('employeePositionList')->with(['warning' => 'Data posisi karyawan tidak dapat dihapus.']);
        }

        $position->delete();
        return redirect()->route('employeePositionList')->with(['success' => 'Data posisi karyawan berhasil dihapus.']);
    }
}
