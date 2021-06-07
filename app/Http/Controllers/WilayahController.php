<?php

namespace App\Http\Controllers;

use App\Repositories\Anggota;
use App\Repositories\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class WilayahController extends Controller
{
    private $anggota, $wilayah;
    public function __construct(Wilayah $wilayah, Anggota $anggota)
    {
        $this->middleware('auth');
        $this->middleware('role');
        Config::set('title', 'Data Master');
        $this->wilayah = $wilayah;
        $this->anggota = $anggota;
    }

    public function index()
    {
        $data['q'] = $_GET['q'] ?? '';
        $data['limit'] = $_GET['limit'] ?? 25;

        $data['data'] = $this->wilayah->list($data);
        $data['active_menu'] = 'region';
        $data['breadcrumb'] = [
            'Data Wilayah' => url()->current()
        ];
        return view('master.region-list', compact('data'));
    }

    public function create()
    {
        $data['mode'] = 'add';
        $data['active_menu'] = 'region';
        $data['breadcrumb'] = [
            'Data Wilayah' => route('regionList'),
            'Tambah' => url()->current()
        ];
        return view('master.region-form', compact('data'));
    }

    protected function edit($id)
    {
        //get this user
        $data['data'] = $this->wilayah->get($id);
        if (!$data['data']) {
            return redirect()->route('regionList')->with(['warning' => 'Data wilayah tidak ditemukan.']);
        }
        //this mode
        $data['mode'] = 'edit';
        //active menu
        $data['active_menu'] = 'region';
        $data['breadcrumb'] = [
            'Data Wilayah' => route('regionList'),
            'Edit: ' . $data['data']->name => url()->current()
        ];
        return view('master.region-form', compact('data'));
    }

    protected function save(Request $request)
    {
        //validate request data
        $data = $request->validate([
            'name' => 'required',
            'code' => 'required',
            'description' => 'nullable'
        ]);
        //if mode input is add
        if ($request->mode == 'add') {
            //save region
            if (!$this->wilayah->create($data)) {
                return back()->with(['warning' => $this->wilayah->getError()]);
            }
            $message = 'Data wilayah berhasil di tambahkan';
        } else {
            //update refion
            if (!$this->wilayah->edit($request->id, $data)) {
                return back()->with(['warning' => $this->wilayah->error]);
            }
            $message = 'Data wilayah berhasil diperbaharui.';
        }
        return redirect()->route('regionList')->with(['success' => $message]);
    }

    public function delete($id)
    {
        $region = $this->wilayah->get($id);
        //if region not found
        if ($region == false) {
            return redirect()->route('regionList')->with(['warning' => 'Data wilayah tidak ditemukan.']);
        }
        // check members with this region
        if ($this->anggota->list(['region_id' => $id])->count() > 0) {
            return redirect()->route('regionList')->with(['warning' => 'Data wilayah tidak dapat dihapus karena sedang digunakan.']);
        }
        //delete region
        $region->deleted_by = auth()->user()->id ?? 1;
        $region->update();
        $region->delete();
        return redirect()->route('regionList')->with(['success' => 'Data wilayah berhasil dihapus.']);
    }
}
