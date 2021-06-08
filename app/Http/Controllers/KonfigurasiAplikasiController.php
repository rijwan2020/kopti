<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConfigAppsRequest;
use App\Repositories\Akun;
use App\Repositories\Simpanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class KonfigurasiAplikasiController extends Controller
{
    private $akun, $simpanan;
    public function __construct(
        Akun $akun, 
        Simpanan $simpanan
    ){
        $this->middleware('auth');
        $this->middleware('role');
        Config::set('title', 'Data Master');
        $this->akun = $akun;
        $this->simpanan = $simpanan;
    }

    public function index()
    {
        $data['data'] = config('config_apps');
        $data['account'] = $this->akun->list(['level' => 3]);
        $data['deposit'] = $this->simpanan->list(['deposit_type_id' => 11]);
        $data['active_menu'] = 'config-apps';
        $data['breadcrumb'] = [
            'Konfigurasi Aplikasi' => url()->current()
        ];
        return view('master.config-apps', compact('data'));
    }
    public function update(ConfigAppsRequest $request)
    {
        $data = $request->validated();
        $data['besar_sp'] = str_replace(',', '', $data['besar_sp']);
        $data['besar_sw'] = str_replace(',', '', $data['besar_sw']);
        $data['set_account'] = config('config_apps.set_account');
        $data['shu_account'] = config('config_apps.shu_account');

        $content = "<?php \nreturn [\n";
        foreach ($data as $key => $value) {
            $content .= "\t'{$key}' => '{$value}',\n";
        }
        $content .= '];';

        $file = config_path() . '/config_apps.php';
        file_put_contents($file, $content);
        return back()->with(['success' => 'Konfigurasi aplikasi berhasil diperbaharui.']);
    }
}
