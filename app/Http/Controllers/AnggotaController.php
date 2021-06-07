<?php

namespace App\Http\Controllers;

use App\Exports\MemberExport;
use App\Http\Requests\MemberRequest;
use App\Repositories\Anggota;
use App\Repositories\Area;
use App\Repositories\Wilayah;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class AnggotaController extends Controller
{
    private $anggota, $wilayah, $area;
    public function __construct(Anggota $anggota, Wilayah $wilayah, Area $area)
    {
        $this->middleware('auth');
        $this->middleware('role');
        Config::set('title', 'Data Master');
        $this->anggota = $anggota;
        $this->wilayah = $wilayah;
        $this->area = $area;
    }

    
    public function index()
    {
        $data['q'] = $_GET['q'] ?? '';
        $data['limit'] = $_GET['limit'] ?? 25;
        $data['region_id'] = $_GET['region_id'] ?? 'all';
        $data['status'] = $_GET['status'] ?? '1';

        $filter = [
            'q' => $data['q'],
            'limit' => $data['limit']
        ];
        if($data['status'] != 'all'){
            $filter['status'] = $data['status'];
        }
        if($data['region_id'] != 'all'){
            $filter['region_id'] = $data['region_id'];
        }
        $filter['relations'] = ['region'];

        $data['data'] = $this->anggota->list($filter);
        $data['region'] = $this->wilayah->list();
        $data['active_menu'] = 'member';
        $data['breadcrumb'] = [
            'Data Anggota' => url()->current()
        ];
        return view('master.member-list', compact('data'));
    }
    
    public function create()
    {
        $data['mode'] = 'add';
        $data['region'] = $this->wilayah->list();
        $data['active_menu'] = 'member';
        $data['breadcrumb'] = [
            'Data Anggota' => route('memberList'),
            'Tambah' => url()->current()
        ];
        return view('master.member-form', compact('data'));
    }
    
    public function edit($id)
    {
        $data['data'] = $this->anggota->get($id);
        $data['mode'] = 'edit';
        $data['region'] = $this->wilayah->list();
        $data['active_menu'] = 'member';
        $data['breadcrumb'] = [
            'Data Anggota' => route('memberList'),
            'Edit : ' . $data['data']->name => url()->current()
        ];
        return view('master.member-form', compact('data'));
    }
    
    public function save(MemberRequest $request)
    {
        $data = $request->validated();
        if (isset($data['village_id'])) {
            //get data desa/kelurahan
            $village = $this->area->villageGet($data['village_id']);
            $data['district_id'] = $village->district_id;
            $data['regency_id'] = $village->regency_id;
            $data['province_id'] = $village->province_id;
        }
        if ($request->has('image')) {
            //upload new image to storage/app/public/anggota/image
            $data['image'] = $request->file('image')->store('anggota/image');
        }
        if (isset($data['income'])) {
            $data['income'] = str_replace(',', '', $data['income']);
        }
        if ($request->mode == 'add') {
            // save member type
            if (!$this->anggota->create($data)) {
                if (isset($data['image']) && !empty($data['image'])) {
                    Storage::delete($data['image']);
                }
                return back()->with(['warning' => $this->master->error])->withInput();
            }
            $config = "<?php \n return [\n";
            foreach (config('config_apps') as $key => $value) {
                if ($data['status'] == 1) {
                    if ($key == 'next_code_anggota') {
                        $value++;
                    }
                } else {
                    if ($key == 'next_code_non_anggota') {
                        $value++;
                    }
                }
                $config .= "\t'{$key}' => '{$value}',\n";
            }
            $config .= " ]; ";
            $file = config_path() . '/config_apps.php';
            file_put_contents($file, $config);
            $message = 'Data anggota berhasil ditambahkan.';
        } else {
            $data['username'] = $request->username;
            $data['password'] = $request->password;
            $member = $this->anggota->get($request->id);
            // update member type
            if (!$this->anggota->edit($request->id, $data)) {
                if (isset($data['image']) && !empty($data['image'])) {
                    Storage::delete($data['image']);
                }
                return back()->with(['warning' => $this->anggota->getError()])->withInput();
            }
            if (isset($data['image']) && !empty($data['image'])) {
                Storage::delete($member->image);
            }
            $message = 'Data anggota berhasil diperbaharui.';
        }
        return redirect()->route('memberList', ['status' => 'all'])->with(['success' => $message]);
    }
    
    public function print()
    {
        $data['q'] = $_GET['q'] ?? '';
        $data['region_id'] = $_GET['region_id'] ?? 'all';
        $data['status'] = $_GET['status'] ?? '1';

        $filter = [
            'q' => $data['q']
        ];
        if($data['status'] != 'all'){
            $filter['status'] = $data['status'];
        }
        if($data['region_id'] != 'all'){
            $filter['region_id'] = $data['region_id'];
        }

        $filter['relations'] = ['region'];

        $data['data'] = $this->anggota->list($filter);
        $data['region'] = $this->wilayah->list();
        return view('master.member-print', compact('data'));
    }

    protected function download()
    {
        ini_set('memory_limit', '-1');
        $data['q'] = $_GET['q'] ?? '';
        $data['status'] = $_GET['status'] ?? 'all';
        $data['region_id'] = $_GET['region_id'] ?? 'all';
        return Excel::download(new MemberExport($data), 'Data Anggota.xlsx');
    }

    public function view($id)
    {
        $data['data'] = $this->anggota->get($id);
        $data['active_menu'] = 'member';
        $data['breadcrumb'] = [
            'Data Anggota' => route('memberList'),
            $data['data']->name => url()->current()
        ];
        return view('master.member-detail', compact('data'));
    }
}
