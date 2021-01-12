<?php

namespace App\Http\Controllers;

use App\Classes\AreaClass;
use App\Classes\MasterClass;
use App\Classes\UserClass;
use App\Http\Requests\MemberRequest;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role');
        Config::set('title', 'Kelola User');
        $this->user = new UserClass();
        $this->area = new AreaClass();
        $this->master = new MasterClass();
    }

    /*
    * =============================================================================================== START PROFILE ===============================================================================================
    */
    public function profile()
    {
        $data['data'] = Auth::user();
        $data['active_menu'] = 'profile';
        $data['breadcrumb'] = [
            'Profile' => url()->current()
        ];
        if (auth()->user()->isMember()) {
            return view('user.profile-member', compact('data'));
        }
        return view('user.profile', compact('data'));
    }
    public function profileUpdate(UserRequest $request)
    {
        $data = $request->validated();
        if ($this->user->userUpdate($request->id, $data)) {
            //if input has image profile or image
            if ($request->has('image')) {
                //delete old image
                Storage::delete($request->image_old);
                //upload new image to storage/app/public/user
                $image = $request->file('image')->store('user');
                // update user image
                DB::table('users')->where('id', $request->id)->update(['image' => $image]);
            }
            return back()->with(['success' => 'Data user aplikasi berhasil diperbaharui']);
        } else {
            return back()->with(['warning' => $this->user->error]);
        }
    }
    public function profileMemberUpdate(MemberRequest $request)
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
        } else {
            $data['image'] = $request->image_old;
        }
        $data['username'] = $request->username;
        $data['password'] = $request->password;
        $member = $this->master->memberGet($request->id);

        if (!$this->master->memberUpdate($request->id, $data)) {
            if (isset($data['image']) && !empty($data['image'])) {
                Storage::delete($data['image']);
            }
            return back()->with(['warning' => $this->master->error])->withInput();
        }
        return back()->with(['success' => 'Profile berhasil di perbaharui']);
    }
    /*
    * =============================================================================================== END PROFILE ===============================================================================================
    */

    /*
    * =============================================================================================== START DATA USER ===============================================================================================
    */
    public function userList()
    {
        $data['limit'] = $_GET['limit'] ?? 20;
        $data['q'] = $_GET['q'] ?? '';
        $data['level_id'] = $_GET['level_id'] ?? 'all';
        $data['sort_by'] = $_GET['sort_by'] ?? 'created_at';
        $data['order'] = $_GET['order'] ?? 'asc';
        $order = [$data['sort_by'], $data['order']];
        $data['data'] = $this->user->userList($data, $data['limit'], $order);
        $data['level'] = $this->user->levelList();
        $data['active_menu'] = 'data-user';
        $data['breadcrumb'] = [
            'User' => url()->current()
        ];
        return view('user.user-list', compact('data'));
    }
    public function userAdd()
    {
        $data['level'] = $this->user->levelList();
        $data['mode'] = 'add';
        $data['active_menu'] = 'data-user';
        $data['breadcrumb'] = [
            'User' => route('userList'),
            'Tambah' => url()->current()
        ];
        return view('user.user-form', compact('data'));
    }
    protected function userEdit($id)
    {
        //get this user
        $data['data'] = $this->user->userGet($id);
        if (!$data['data']) {
            return redirect()->route('userList')->with(['warning' => 'Data user tidak ditemukan.']);
        }
        //this mode
        $data['mode'] = 'edit';
        //active menu
        $data['active_menu'] = 'data-user';
        $data['breadcrumb'] = [
            'User' => route('userList'),
            'Edit: ' . $data['data']->name => url()->current()
        ];
        return view('user.user-form', compact('data'));
    }
    protected function userSave(UserRequest $request)
    {
        //validate request data
        $data = $request->validated();
        //if mode input is add
        if ($request->mode == 'add') {
            //save user
            if ($this->user->userSave($data)) {
                //if request has avatar or image profile
                if ($request->has('image')) {
                    $user_id = $this->user->last_user_id;
                    //save the image to storage/app/public/user
                    $image = $request->file('image')->store('user');
                    //update user image
                    DB::table('users')->where('id', $user_id)->update(['image' => $image]);
                }

                $message = 'Data user aplikasi berhasil di tambahkan';
            } else {
                return back()->with(['warning' => $this->user->error]);
            }
        } else {
            //update user
            if ($this->user->userUpdate($request->id, $data)) {
                //if request has image or image profile
                if ($request->has('image')) {
                    //delete old image
                    Storage::delete($request->image_old);
                    //save new image to storage/app/public/user
                    $image = $request->file('image')->store('user');
                    // update user image
                    DB::table('users')->where('id', $request->id)->update(['image' => $image]);
                }
                $message = 'Data user aplikasi berhasil diperbaharui';
            } else {
                return back()->with(['warning' => $this->user->error]);
            }
        }
        return redirect()->route('userList')->with(['success' => $message]);
    }
    public function userDelete($id)
    {
        $user = $this->user->userGet($id);
        //if user not found
        if ($user == false) {
            return redirect()->route('userList')->with(['warning' => 'Data user tidak ditemukan.']);
        }
        //delete image in the storage
        Storage::delete($user->image);
        //delete user
        $user->deleted_by = auth()->user()->id ?? 1;
        $user->deleted_at = date('Y-m-d H:i:s');
        $user->username = base64_encode($user->username);
        $user->email = base64_encode($user->email);
        $user->update();
        return redirect()->route('userList')->with(['success' => 'Data user berhasil dihapus.']);
    }
    /*
    * =============================================================================================== END DATA USER ===============================================================================================
    */

    /*
    * =============================================================================================== START LEVEL USER ===============================================================================================
    */
    public function levelList()
    {
        $data['q'] = $_GET['q'] ?? '';
        $data['data'] = $this->user->levelList($data);
        $data['active_menu'] = 'level-user';
        $data['breadcrumb'] = [
            'Level User' => url()->current()
        ];
        return view('user.level-list', compact('data'));
    }
    public function levelAdd()
    {
        $data['mode'] = 'add';
        $data['active_menu'] = 'level-user';
        $data['breadcrumb'] = [
            'Level User' => route('levelList'),
            'Tambah' => url()->current(),
        ];
        return view('user.level-form', compact('data'));
    }
    protected function levelEdit($id)
    {

        $data['data'] = $this->user->levelGet($id);
        $data['data']->rule = json_decode($data['data']->rule);
        $data['mode'] = 'edit';
        $data['active_menu'] = 'level-user';
        $data['breadcrumb'] = [
            'Level User' => route('levelList'),
            'Edit : ' . $data['data']->name => url()->current()
        ];
        return view('user.level-form', compact('data'));
    }
    protected function levelSave(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|numeric',
            'name' => 'required',
            'rule' => 'nullable'
        ]);
        if (!isset($data['rule'])) {
            $data['rule'] = '[]';
        }
        $data['rule'] = json_encode($data['rule']);
        if ($request->mode == 'add') {
            if ($this->user->levelSave($data)) {
                $message = 'Data level user aplikasi berhasil ditambahkan.';
            } else {
                return back()->with(['warning' => $this->user->error]);
            }
        } else {
            if ($this->user->levelUpdate($data)) {
                $message = 'Data level user aplikasi berhasil diperbaharui.';
            } else {
                return back()->with(['warning' => $this->user->error]);
            }
        }
        return redirect()->route('levelList')->with(['success' => $message]);
    }
    public function levelDelete($id)
    {
        // get data level
        $level = $this->user->levelGet($id);
        //if level not found
        if ($level == false) {
            return redirect()->route('levelList')->with(['warning' => 'Data level user tidak ditemukan.']);
        }
        // if level is used
        if ($this->user->userList(['level_id' => $id])->count() > 0) {
            return redirect()->route('levelList')->with(['warning' => 'Data level user tidak dapat dihapus karena sudah digunakan.']);
        }
        //delete level
        $level->delete();

        return redirect()->route('levelList')->with(['success' => 'Data level user berhasil dihapus.']);
    }
    /*
    * =============================================================================================== END LEVEL USER ===============================================================================================
    */
}