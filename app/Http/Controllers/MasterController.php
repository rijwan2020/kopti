<?php

namespace App\Http\Controllers;

use App\Classes\AccountancyClass;
use App\Classes\AreaClass;
use App\Classes\DepositClass;
use App\Classes\MasterClass;
use App\Classes\UserClass;
use App\Exports\MemberExport;
use App\Http\Requests\AssetRequest;
use App\Http\Requests\ConfigAppsRequest;
use App\Http\Requests\EmployeeRequest;
use App\Http\Requests\MemberRequest;
use App\Http\Requests\PositionRequest;
use App\Imports\MemberImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel as Excel;

class MasterController extends Controller
{
    private $master, $user, $area, $deposit, $accountancy;
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role');
        Config::set('title', 'Data Master');

        $this->master = new MasterClass();
        $this->user = new UserClass();
        $this->area = new AreaClass();
        $this->deposit = new DepositClass();
        $this->accountancy = new AccountancyClass();
    }



    /*
    * ========================================================================================== START PROFILE KOPERASI ==========================================================================================
    */
    public function koperasi()
    {
        $data['data'] = config('koperasi');
        $data['village'] = $this->area->villageGet($data['data']['village_id']);
        $data['active_menu'] = 'koperasi';
        $data['breadcrumb'] = [
            'Profile Koperasi' => url()->current()
        ];
        return view('master.koperasi', compact('data'));
    }
    public function koperasiUpdate(Request $request)
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
    /*
    * ========================================================================================== END PROFILE KOPERASI ==========================================================================================
    */



    /*
    * ========================================================================================== START DATA MEMBER ==========================================================================================
    */
    public function memberDelete($id)
    {
        $member = $this->master->memberGet($id);
        //if member not exist
        if ($member == false) {
            return redirect()->route('memberList')->with(['warning' => 'Data anggota tidak ditemukan.']);
        }
        $update_member = [
            'out_date' => date('Y-m-d'),
            'status' => 2,
            'updated_by' => auth()->user()->id ?? 1,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        // update member
        $member->update($update_member);
        $account = '01.01.01';
        foreach ($member->deposit as $key => $value) {
            // tutup tabungan
            $transaction = [
                'deposit_id' => $value->id,
                'transaction_date' => date('Y-m-d H:i:s'),
                'type' => 2,
                'debit' => $value->balance,
                'kredit' => 0,
                'reference_number' => 'TRXS-' . (date('YmdHis') + $key),
                'account' => $account,
                'note' => 'Penutupan Rekening ' . $value->account_number . '(Anggota Keluar)'
            ];
            if ($value->balance > 0) {
                $this->deposit->depositTransactionSave($transaction, 1);
            }
            $this->deposit->depositDelete($value->id, $transaction['transaction_date']);
        }
        // get user member
        $user = $this->user->userGet($member->user_id);
        // delete user
        $user->deleted_by = auth()->user()->id ?? 1;
        $user->deleted_at = date('Y-m-d H:i:s');
        $user->username = base64_encode($user->username);
        $user->email = base64_encode($user->email);
        $user->update();
        return redirect()->route('memberList')->with(['success' => 'Data anggota berhasil dihapus.']);
    }
    public function memberTransaksi($id)
    {
        $data['member'] = $this->master->memberGet($id);
        $data['limit'] = $_GET['limit'] ?? 25;
        $data['q'] = $_GET['q'] ?? '';
        $data['member_id'] = $id;
        $data['data'] = $this->accountancy->journalList($data, $data['limit']);
        $data['active_menu'] = 'member';
        $data['breadcrumb'] = [
            'Data Anggota' => route('memberList'),
            $data['member']->name => route('memberDetail', ['id' => $data['member']->id]),
            'Transaksi' => url()->current(),
        ];
        return view('master.member-detail-transaksi', compact('data'));
    }
    public function memberUpload()
    {
        $data['limit'] = $_GET['limit'] ?? 25;
        $data['q'] = $_GET['q'] ?? '';
        $data['data'] = $this->master->memberUploadList($data, $data['limit']);
        $data['active_menu'] = 'member';
        $data['breadcrumb'] = [
            'Data Anggota' => route('memberList'),
            'Upload' => url()->current()
        ];
        if (isset($_GET['confirm'])) {
            $this->master->memberUploadConfirm($_GET['confirm']);
            if ($_GET['confirm'] == 0) {
                return redirect()->route('memberUpload')->with(['info' => 'Upload data anggota dibatalkan.']);
            } else {
                return redirect()->route('memberList')->with(['success' => 'Upload data anggota berhasil.']);
            }
        }
        return view('master.member-upload', compact('data'));
    }
    public function memberUploadSave(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);
        $file = $request->file('file')->storeAs('import/member', date('YmdHis-') . $_FILES['file']['name']);
        Excel::import(new MemberImport, $file);
        return redirect()->route('memberUpload')->with(['info' => 'Pastikan data telah sesuai, lalu klik tombol konfirmasi.']);
    }
    public function memberPromotion($id)
    {
        $data['data'] = $this->master->memberGet($id);
        $data['region'] = $this->master->regionList();
        $data['active_menu'] = 'member';
        $data['breadcrumb'] = [
            'Data Anggota' => route('memberList'),
            'Promosikan : ' . $data['data']->name . ' jadi anggota' => url()->current()
        ];
        return view('master.member-promotion-form', compact('data'));
    }
    public function memberPromotionSave(Request $request)
    {
        $data = $request->validate([
            'id' => 'required',
            'code' => 'required'
        ]);
        $member = $this->master->memberGet($data['id']);
        $new_member = $member->toArray();
        $new_member['code'] = $data['code'];
        $new_member['status'] = 1;
        $member->promotion = 1;
        $account = '01.01.01';

        unset($new_member['id']);
        if ($this->master->memberSave($new_member)) {
            $config = "<?php \n return [\n";
            foreach (config('config_apps') as $key => $value) {
                if ($key == 'next_code_anggota') {
                    $value++;
                }
                $config .= "\t'{$key}' => '{$value}',\n";
            }
            $config .= " ]; ";
            $file = config_path() . '/config_apps.php';
            file_put_contents($file, $config);
            // proses penarikan simpanan
            $member_id = $this->master->last_member_id;
            foreach ($member->deposit as $key => $value) {
                // tutup tabungan
                $transaction = [
                    'deposit_id' => $value->id,
                    'transaction_date' => date('Y-m-d H:i:s'),
                    'type' => 2,
                    'debit' => $value->balance,
                    'kredit' => 0,
                    'reference_number' => 'TRXS-' . (date('YmdHis') + $key),
                    'account' => $account,
                    'note' => 'Penutupan Rekening ' . $value->account_number . '(Pemindahan rekening simpanan)'
                ];
                $this->deposit->depositTransactionSave($transaction, 1);
                $this->deposit->depositDelete($value->id, $transaction['transaction_date']);
                // create new deposit
                $tipesimpanan = $this->deposit->depositTypeGet($value->deposit_type_id);
                $new_simpanan = [
                    'member_id' => $member_id,
                    'deposit_type_id' => $value->deposit_type_id,
                    'account_number' => $tipesimpanan->code . '-' . str_pad($tipesimpanan->next_code, 9, 0, STR_PAD_LEFT),
                    'beginning_balance' => $value->balance,
                    'registration_date' => date('Y-m-d'),
                    'account' => $account
                ];
                $this->deposit->depositSave($new_simpanan);
            }
        } else {
            return back()->with(['warning' => $this->master->error])->withInput();
        }
        $member->update();

        return redirect()->route('memberList')->with(['success' => 'Data calon anggota berhasil dipromosikan ke anggota.']);
    }
    public function memberReset()
    {
        DB::table('members')->truncate();
        DB::table('managements')->truncate();
        DB::table('deposits')->truncate();
        DB::table('deposit_bills')->truncate();
        DB::table('deposit_books')->truncate();
        DB::table('deposit_transactions')->truncate();
        DB::table('deposit_transactions')->truncate();
        DB::table('deposit_types')->update(['next_code' => 1]);
        DB::table('users')->where([
            ['level_id', '>', 2],
            ['level_id', '<', 30],
        ])->delete();
        DB::table('users')->where('level_id', '>', 70)->delete();
        $config = "<?php \n return [\n";
        foreach (config('config_apps') as $key => $value) {
            if ($key == 'next_code_anggota') {
                $value = 1;
            }
            if ($key == 'next_code_non_anggota') {
                $value = 1;
            }
            $config .= "\t'{$key}' => '{$value}',\n";
        }
        $config .= " ]; ";
        $file = config_path() . '/config_apps.php';
        file_put_contents($file, $config);
        return back()->with(['success' => 'Data anggota dan data yang berhubungan dengan anggota berhasil di hapus.']);
    }
    public function memberActivity($id)
    {
        $data['date'] = $_GET['date'] ?? date('Y-m-d');
        $data['data'] = $this->master->memberGet($id);
        $data['active_menu'] = 'member';
        $data['breadcrumb'] = [
            'Data Anggota' => route('memberList'),
            'Catatan Aktivitas ' . $data['data']->name => url()->current()
        ];
        return view('master.member-activity', compact('data'));
    }
    public function memberActivityPrint($id)
    {
        $data['date'] = $_GET['date'] ?? date('Y-m-d');
        $data['data'] = $this->master->memberGet($id);
        return view('master.member-activity-print', compact('data'));
    }
    /*
    * ========================================================================================== END DATA MEMBER ==========================================================================================
    */


    /*
    * ========================================================================================== START MANAGEMENT ==========================================================================================
    */
    public function managementSave(Request $request)
    {
        $data = $request->validate([
            'member_id' => 'required',
            'position_id' => 'required',
            'username' => 'required',
            'password' => 'nullable|min:6'
        ]);
        if ($request->mode == 'add') {
            //save management
            if (!$this->master->managementSave($data)) {
                return back()->with(['warning' => $this->master->error]);
            }
            $message = 'Data pengurus berhasil ditambahkan.';
        } else {
            //update refion
            if (!$this->master->managementUpdate($request->id, $data)) {
                return back()->with(['warning' => $this->master->error]);
            }
            $message = 'Data pengurus berhasil diperbaharui.';
        }
        return redirect()->route('managementList')->with(['success' => $message]);
    }
    /*
    * ========================================================================================== END MANAGEMENT ==========================================================================================
    */



    /*
    * ========================================================================================== START MANAGEMENT POSITION ==========================================================================================
    */
    public function managementPositionList()
    {
        $data['q'] = $_GET['q'] ?? '';
        $data['limit'] = $_GET['limit'] ?? 25;
        $data['type'] = 0;
        $data['data'] = $this->master->positionList($data, $data['limit']);
        $data['active_menu'] = 'management';
        $data['breadcrumb'] = [
            'Data Pengurus' => route('managementList'),
            'Manage Jabatan' => url()->current()
        ];
        return view('master.management-position-list', compact('data'));
    }
    public function managementPositionAdd()
    {
        $data['mode'] = 'add';
        $data['level'] = $this->user->levelList();
        $data['active_menu'] = 'management';
        $data['breadcrumb'] = [
            'Data Pengurus' => route('managementList'),
            'Manage Jabatan' => route('managementPositionList'),
            'Tambah' => url()->current(),
        ];
        return view('master.management-position-form', compact('data'));
    }
    protected function managementPositionEdit($id)
    {
        //get this position
        $data['data'] = $this->master->positionGet($id);
        if (!$data['data']) {
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
    public function managementPositionSave(PositionRequest $request)
    {
        $data = $request->validated();
        $data['type'] = 0;
        if ($request->mode == 'add') {
            //save position
            if (!$this->master->positionSave($data)) {
                return back()->with(['warning' => $this->master->error]);
            }
            $message = 'Data jabatan pengurus berhasil ditambahkan.';
        } else {
            //update refion
            if (!$this->master->positionUpdate($request->id, $data)) {
                return back()->with(['warning' => $this->master->error]);
            }
            $message = 'Data jabatan pengurus berhasil diperbaharui.';
        }
        return redirect()->route('managementPositionList')->with(['success' => $message]);
    }
    public function managementPositionDelete($id)
    {
        $position = $this->master->positionGet($id);
        if (!$position) {
            return redirect()->route('managementPositionList')->with(['warning' => 'Data jabatan pengurus tidak ditemukan.']);
        }
        // check list management
        if ($this->master->managementList(['position_id' => $id])->count() > 0) {
            return redirect()->route('managementPositionList')->with(['warning' => 'Data jabatan pengurus tidak dapat dihapus.']);
        }

        $position->delete();
        return redirect()->route('managementPositionList')->with(['success' => 'Data jabatan pengurus berhasil dihapus.']);
    }
    /*
    * ========================================================================================== END MANAGEMENT POSITION ==========================================================================================
    */



    /*
    * ========================================================================================== START EMPLOYEE ==========================================================================================
    */
    public function employeeList()
    {
        $data['q'] = $_GET['q'] ?? '';
        $data['limit'] = $_GET['limit'] ?? 25;
        $data['position_id'] = $_GET['position_id'] ?? 'all';
        $data['data'] = $this->master->employeeList($data, $data['limit']);
        $data['position'] = $this->master->positionList(['type' => 1]);
        $data['active_menu'] = 'employee';
        $data['breadcrumb'] = [
            'Data Karyawan' => url()->current()
        ];
        return view('master.employee-list', compact('data'));
    }
    public function employeeAdd()
    {
        $data['mode'] = 'add';
        $data['position'] = $this->master->positionList(['type' => 1]);
        $data['active_menu'] = 'employee';
        $data['breadcrumb'] = [
            'Data Pengurus' => route('employeeList'),
            'Tambah' => url()->current(),
        ];
        return view('master.employee-form', compact('data'));
    }
    public function employeeEdit($id)
    {
        $data['data'] = $this->master->employeeGet($id);
        if (!$data['data']) {
            return redirect()->route('employeeList')->with(['warning' => 'Data pengurus tidak ditemukan.']);
        }
        $data['mode'] = 'edit';
        $data['position'] = $this->master->positionList(['type' => 1]);
        $data['active_menu'] = 'employee';
        $data['breadcrumb'] = [
            'Data Karyawan' => route('employeeList'),
            'Edit: ' . $data['data']->name => url()->current(),
        ];
        return view('master.employee-form', compact('data'));
    }
    public function employeeSave(EmployeeRequest $request)
    {
        $data = $request->validated();
        if ($request->mode == 'add') {
            //save employee
            if (!$this->master->employeeSave($data)) {
                return back()->with(['warning' => $this->master->error]);
            }
            $message = 'Data karyawan berhasil ditambahkan.';
        } else {
            //update refion
            if (!$this->master->employeeUpdate($request->id, $data)) {
                return back()->with(['warning' => $this->master->error]);
            }
            $message = 'Data karyawan berhasil diperbaharui.';
        }
        return redirect()->route('employeeList')->with(['success' => $message]);
    }
    public function employeeDelete($id)
    {
        $employee = $this->master->employeeGet($id);
        //if employee not exist
        if ($employee == false) {
            return redirect()->route('employeeList')->with(['warning' => 'Data karyawan tidak ditemukan.']);
        }
        $employee->delete();
        // get user employee
        $user = $this->user->userGet($employee->user_id);
        // delete user
        $user->deleted_by = auth()->user()->id ?? 1;
        $user->deleted_at = date('Y-m-d H:i:s');
        $user->username = base64_encode($user->username);
        $user->email = base64_encode($user->email);
        $user->update();
        return redirect()->route('employeeList')->with(['success' => 'Data karyawan berhasil dihapus.']);
    }
    /*
    * ========================================================================================== END EMPLOYEE ==========================================================================================
    */



    /*
    * ========================================================================================== START EMPLOYEE POSITION ==========================================================================================
    */
    public function employeePositionList()
    {
        $data['q'] = $_GET['q'] ?? '';
        $data['limit'] = $_GET['limit'] ?? 25;
        $data['type'] = 1;
        $data['data'] = $this->master->positionList($data, $data['limit']);
        $data['active_menu'] = 'employee';
        $data['breadcrumb'] = [
            'Data Karyawan' => route('employeeList'),
            'Manage Posisi' => url()->current()
        ];
        return view('master.employee-position-list', compact('data'));
    }
    public function employeePositionAdd()
    {
        $data['mode'] = 'add';
        $data['level'] = $this->user->levelList();
        $data['active_menu'] = 'employee';
        $data['breadcrumb'] = [
            'Data Pengurus' => route('employeeList'),
            'Manage Posisi' => route('employeePositionList'),
            'Tambah' => url()->current(),
        ];
        return view('master.employee-position-form', compact('data'));
    }
    protected function employeePositionEdit($id)
    {
        //get this position
        $data['data'] = $this->master->positionGet($id);
        if (!$data['data']) {
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
    public function employeePositionSave(PositionRequest $request)
    {
        $data = $request->validated();
        $data['type'] = 1;
        if ($request->mode == 'add') {
            //save position
            if (!$this->master->positionSave($data)) {
                return back()->with(['warning' => $this->master->error]);
            }
            $message = 'Data posisi karyawan berhasil ditambahkan.';
        } else {
            //update refion
            if (!$this->master->positionUpdate($request->id, $data)) {
                return back()->with(['warning' => $this->master->error]);
            }
            $message = 'Data posisi karyawan berhasil diperbaharui.';
        }
        return redirect()->route('employeePositionList')->with(['success' => $message]);
    }
    public function employeePositionDelete($id)
    {
        $position = $this->master->positionGet($id);
        if (!$position) {
            return redirect()->route('employeePositionList')->with(['warning' => 'Data posisi karyawan tidak ditemukan.']);
        }
        // check list employee

        $position->delete();
        return redirect()->route('employeePositionList')->with(['success' => 'Data posisi karyawan berhasil dihapus.']);
    }
    /*
    * ========================================================================================== END EMPLOYEE POSITION ==========================================================================================
    */



    /*
    * ========================================================================================== START CONFIG APPS ==========================================================================================
    */
    public function configApps()
    {
        $data['data'] = config('config_apps');
        $data['account'] = $this->accountancy->accountList(['level' => 3]);
        $data['deposit'] = $this->deposit->depositList(['type_id' => 11]);
        $data['active_menu'] = 'config-apps';
        $data['breadcrumb'] = [
            'Konfigurasi Aplikasi' => url()->current()
        ];
        return view('master.config-apps', compact('data'));
    }
    public function configAppsUpdate(ConfigAppsRequest $request)
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
    /*
    * ========================================================================================== END CONFIG APPS ==========================================================================================
    */



    /*
    * ========================================================================================== START ASSET ==========================================================================================
    */
    public function assetList()
    {
        $data['limit'] = $_GET['list'] ?? 25;
        $data['q'] = $_GET['q'] ?? '';
        $data['cat_id'] = $_GET['cat_id'] ?? 'all';
        $data['data'] = $this->master->assetList($data, $data['limit']);
        $data['category'] = $this->master->assetCategoryList();
        $data['active_menu'] = 'asset';
        $data['breadcrumb'] = [
            'Aset Barang' => url()->current()
        ];
        return view('master.asset-list', compact('data'));
    }
    public function assetAdd()
    {
        $data['mode'] = 'add';
        $data['active_menu'] = 'asset';
        $data['category'] = $this->master->assetCategoryList();
        $data['breadcrumb'] = [
            'Aset Barang' => route('assetList'),
            'Tambah' => url()->current(),
        ];
        return view('master.asset-form', compact('data'));
    }
    public function assetEdit($id)
    {
        $data['mode'] = 'edit';
        $data['data'] = $this->master->assetGet($id);
        $data['category'] = $this->master->assetCategoryList();
        $data['active_menu'] = 'asset';
        $data['breadcrumb'] = [
            'Aset Barang' => route('assetList'),
            'Edit: ' . $data['data']['name'] => url()->current(),
        ];
        return view('master.asset-form', compact('data'));
    }
    public function assetSave(AssetRequest $request)
    {
        $data = $request->validated();
        $data['price'] = str_replace(',', '', $data['price']);
        $data['item_value'] = str_replace(',', '', $data['item_value']);
        if ($request->mode == 'add') {
            //save asset
            if (!$this->master->assetSave($data)) {
                return back()->with(['warning' => $this->master->error]);
            }
            $message = 'Data aset barang berhasil ditambahkan.';
        } else {
            //update asset
            if (!$this->master->assetUpdate($request->id, $data)) {
                return back()->with(['warning' => $this->master->error]);
            }
            $message = 'Data aset barang berhasil diperbaharui.';
        }
        return redirect()->route('assetList')->with(['success' => $message]);
    }
    public function assetDelete($id)
    {
        $asset = $this->master->assetGet($id);
        if (!$asset) {
            return redirect()->route('assetList')->with(['warning' => 'Data aset tidak ditemukan.']);
        }
        $asset->delete();
        return redirect()->route('assetList')->with(['success' => 'Data aset berhasil dihapus.']);
    }
    /*
    * ========================================================================================== END ASSET ==========================================================================================
    */



    /*
    * ========================================================================================== START ASSET CATEGORY ==========================================================================================
    */
    public function assetCategoryList()
    {
        $data['limit'] = $_GET['list'] ?? 25;
        $data['q'] = $_GET['q'] ?? '';
        $data['data'] = $this->master->assetCategoryList($data, $data['limit']);
        $data['active_menu'] = 'asset';
        $data['breadcrumb'] = [
            'Aset Barang' => route('assetList'),
            'Kategori' => url()->current()
        ];
        return view('master.asset-category-list', compact('data'));
    }
    public function assetCategoryAdd()
    {
        $data['mode'] = 'add';
        $data['active_menu'] = 'asset';
        $data['breadcrumb'] = [
            'Aset Barang' => route('assetList'),
            'Kategori' => route('assetCategoryList'),
            'Tambah' => url()->current(),
        ];
        return view('master.asset-category-form', compact('data'));
    }
    public function assetCategoryEdit($id)
    {
        $data['mode'] = 'edit';
        $data['data'] = $this->master->assetCategoryGet($id);
        $data['active_menu'] = 'asset';
        $data['breadcrumb'] = [
            'Aset Barang' => route('assetList'),
            'Kategori' => route('assetCategoryList'),
            'Edit: ' . $data['data']['name'] => url()->current(),
        ];
        return view('master.asset-category-form', compact('data'));
    }
    public function assetCategorySave(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'description' => 'nullable',
        ]);
        if ($request->mode == 'add') {
            //save asset category
            if (!$this->master->assetCategorySave($data)) {
                return back()->with(['warning' => $this->master->error]);
            }
            $message = 'Data kategori aset barang berhasil ditambahkan.';
        } else {
            //update asset category
            if (!$this->master->assetCategoryUpdate($request->id, $data)) {
                return back()->with(['warning' => $this->master->error]);
            }
            $message = 'Data kategori aset barang berhasil diperbaharui.';
        }
        return redirect()->route('assetCategoryList')->with(['success' => $message]);
    }
    public function assetCategoryDelete($id)
    {
        $assetCategory = $this->master->assetCategoryGet($id);
        if (!$assetCategory) {
            return redirect()->route('assetCategoryList')->with(['warning' => 'Data kategori aset tidak ditemukan.']);
        }
        // check list 
        $asset =  $this->master->assetList(['asset_category_id' => $id]);
        if ($asset->count() > 0) {
            return redirect()->route('assetCategoryList')->with(['warning' => 'Data kategori aset tidak dapat dihapus.']);
        }

        $assetCategory->delete();
        return redirect()->route('assetCategoryList')->with(['success' => 'Data kategori aset berhasil dihapus.']);
    }
    /*
    * ========================================================================================== END ASSET CATEGORY ==========================================================================================
    */
}