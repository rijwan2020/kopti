<?php

namespace App\Http\Controllers;

use App\Repositories\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class KoperasiController extends Controller
{
    private $area;
    public function __construct(Area $area)
    {
        $this->middleware('auth');
        $this->middleware('role');
        Config::set('title', 'Data Master');
        $this->area = $area;
    }

    public function index()
    {
        $data['data'] = config('koperasi');
        $data['village'] = $this->area->villageGet($data['data']['village_id']);
        $data['active_menu'] = 'koperasi';
        $data['breadcrumb'] = [
            'Profile Koperasi' => url()->current()
        ];
        return view('master.koperasi', compact('data'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required',
            'alamat' => 'nullable',
            'deskripsi' => 'nullable',
            'no_telepon' => 'nullable|int',
            'email' => 'nullable|email',
            'website' => 'nullable',
            'no_badan_hukum' => 'nullable',
            'tanggal_badan_hukum' => 'nullable',
            'bentuk_id' => 'nullable',
            'jenis_id' => 'nullable',
            'village_id' => 'nullable'
        ]);

        if (isset($data['village_id'])) {
            //get data desa/kelurahan
            $village = $this->area->villageGet($data['village_id']);
            $data['district_id'] = $village->district_id;
            $data['regency_id'] = $village->regency_id;
            $data['province_id'] = $village->province_id;
        } else {
            $data["village_id"] = "3273080003";
            $data["district_id"] = "3273080";
            $data["regency_id"] = "3273";
            $data["province_id"] = "32";
        }

        if ($request->has('logo')) {
            if (!empty(config('koperasi.logo'))) {
                Storage::delete(config('koperasi.logo'));
            }
            //upload new logo to storage/app/public/logo
            $logo = $request->file('logo')->store('logo');
        } else {
            $logo = config('koperasi.logo');
        }

        $content = "<?php \nreturn [\n";
        foreach ($data as $key => $value) {
            $content .= "\t'{$key}' => '{$value}',\n";
        }
        $content .= "\t'logo' => '{$logo}',\n";
        $content .= '];';

        $file = config_path() . '/koperasi.php';
        file_put_contents($file, $content);
        return back()->with(['success' => 'Profile koperasi berhasil diperbaharui.']);
    }
}
