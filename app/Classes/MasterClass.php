<?php

namespace App\Classes;

use App\Model\Asset;
use App\Model\AssetCategory;
use App\Model\Employee;
use App\Model\Management;
use App\Model\Member;
use App\Model\MemberUpload;
use App\Model\Position;
use App\Model\Region;
use Illuminate\Support\Facades\DB;

class MasterClass
{
    public $error = '', $last_member_id = 0;
    public function __construct()
    {
        DB::enableQueryLog();
        $this->user = new UserClass();
    }



    /*
    * =============================================================================================== START MEMBER ===============================================================================================
    */
    public function memberList($data = [], $limit = false, $order = false, $paginate = true)
    {
        if (!$order) $order = ['created_at', 'asc'];
        //start query
        $query = Member::query()->with(['village', 'district', 'regency', 'province', 'region', 'user', 'penjualan', 'utangHistori']);
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("code", "like", "%{$data['q']}%")
                    ->orWhere("name", "like", "%{$data['q']}%")
                    ->orWhere("phone", "like", "%{$data['q']}%")
                    ->orWhere("place_of_birth", "like", "%{$data['q']}%")
                    ->orWhere("address", "like", "%{$data['q']}%");
            });
        }
        if (isset($data['status']) && $data['status'] != 'all') {
            $query->where('status', $data['status']);
        }
        if (isset($data['join_date']) && !empty($data['join_date'])) {
            $query->where('join_date', $data['join_date']);
        }
        if (isset($data['region_id']) && $data['region_id'] != 'all') {
            $query->where('region_id', $data['region_id']);
        }
        //order query
        $query->orderBy($order[0], $order[1]);
        //limit
        if ($limit != false) {
            if ($paginate == true) {
                $result = $query->paginate($limit);
            } else {
                $result = $query->limit($limit)->get();
            }
        } else {
            $result = $query->get();
        }

        return $result;
    }
    public function memberGet($data)
    {
        //if $data not array
        if (!is_array($data)) {
            $query = Member::find($data);
        } else {
            $query = Member::where($data[0], $data[1])->get()->first();
        }
        $result = $query;
        return $result;
    }
    public function memberSave($data = [])
    {
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $data['updated_by'] = auth()->user()->id;
        // cek member type code
        if ($this->memberGet(['code', $data['code']])) {
            $this->error = 'Kode anggota sudah digunakan.';
            return false;
        }
        $data['user_id'] = 0;
        if ($data['status'] == 1) {
            $user = [
                'name' => $data['name'],
                'username' => $data['code'],
                'password' => $data['code'],
                'level_id' => 99,
                'image' => $data['image'] ?? ''
            ];

            $this->user->userSave($user);
            $data['user_id'] = $this->user->last_user_id;
        }

        $member = Member::create($data);
        $this->last_member_id = $member->id;

        return true;
    }
    public function memberUpdate($id, $data = [])
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['updated_by'] = auth()->user()->id;
        $member = $this->memberGet($id);
        if (isset($data['code']) && $data['code'] != $member->code) {
            if ($this->memberGet(['code', $data['code']])) {
                $this->error = 'Kode anggota sudah digunakan.';
                return false;
            }
        }
        $user = [
            'username' => $data['username'],
            'name' => $data['name'],
            'password' => $data['password'],
            'image' => $data['image'] ?? ''
        ];
        if ($member->user_id != 0) {
            $this->user->userUpdate($member->user_id, $user);
        }

        unset($data['username'], $data['password']);
        $member->update($data);
        return true;
    }
    /*
    * =============================================================================================== END MEMBER ===============================================================================================
    */



    /*
    * ============================================================================================== START MEMBER UPLOAD ==============================================================================================
    */
    public function memberUploadList($data = [], $limit = false, $order = false, $paginate = true)
    {
        if (!$order) $order = ['created_at', 'asc'];
        //start query
        $query = MemberUpload::query();
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("code", "like", "%{$data['q']}%")
                    ->orWhere("name", "like", "%{$data['q']}%")
                    ->orWhere("nik", "like", "%{$data['q']}%")
                    ->orWhere("email", "like", "%{$data['q']}%")
                    ->orWhere("phone", "like", "%{$data['q']}%")
                    ->orWhere("profession", "like", "%{$data['q']}%")
                    ->orWhere("place_of_birth", "like", "%{$data['q']}%")
                    ->orWhere("address", "like", "%{$data['q']}%");
            });
        }
        if (isset($data['status']) && $data['status'] != 'all') {
            $query->where('status', $data['status']);
        }
        if (isset($data['join_date']) && !empty($data['join_date'])) {
            $query->where('join_date', $data['join_date']);
        }
        if (isset($data['region_id']) && $data['region_id'] != 'all') {
            $query->where('region_id', $data['region_id']);
        }
        //order query
        $query->orderBy($order[0], $order[1]);
        //limit
        if ($limit != false) {
            if ($paginate == true) {
                $result = $query->paginate($limit);
            } else {
                $result = $query->limit($limit)->get();
            }
        } else {
            $result = $query->get();
        }

        return $result;
    }
    public function memberUploadConfirm($confirm)
    {
        if ($confirm == 0) {
            MemberUpload::query()->truncate();
        } else {
            $member = $this->memberUploadList();
            $next_code_anggota = 0;
            $next_code_non_anggota = 0;
            foreach ($member as $key => $value) {
                unset($value->id);
                $data = $value->toArray();
                if ($this->memberSave($data)) {
                    if ($data['status'] == 1) {
                        $next_code_anggota++;
                    } elseif ($data['status'] == 0) {
                        $next_code_non_anggota++;
                    }
                }
            }
            $config = "<?php \n return [\n";
            foreach (config('config_apps') as $hsl => $hasil) {
                if ($hsl == 'next_code_anggota') {
                    $hasil += $next_code_anggota;
                }
                if ($hsl == 'next_code_non_anggota') {
                    $hasil += $next_code_non_anggota;
                }

                $config .= "\t'{$hsl}' => '{$hasil}',\n";
            }
            $config .= " ]; ";
            $file = config_path() . '/config_apps.php';
            file_put_contents($file, $config);
            MemberUpload::query()->truncate();
        }
        return true;
    }
    /*
    * ============================================================================================== END MEMBER UPLOAD ==============================================================================================
    */



    /*
    * =============================================================================================== START REGION ===============================================================================================
    */
    public function regionList($data = [], $limit = false, $order = false, $paginate = true)
    {
        if (!$order) $order = ['created_at', 'desc'];
        //start query
        $query = Region::query()->with(['penjualan', 'utangHistori', 'member']);
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("name", "like", "%{$data['q']}%")
                    ->orWhere("description", "like", "%{$data['q']}%");
            });
        }
        //order query
        $query->orderBy($order[0], $order[1]);
        //limit
        if ($limit != false) {
            if ($paginate == true) {
                $result = $query->paginate($limit);
            } else {
                $result = $query->limit($limit)->get();
            }
        } else {
            $result = $query->get();
        }

        return $result;
    }
    public function regionGet($data)
    {
        //if $data not array
        if (!is_array($data)) {
            $query = Region::find($data);
        } else {
            $query = Region::where($data[0], $data[1])->get()->first();
        }
        $result = $query;
        return $result;
    }
    public function regionSave($data = [])
    {
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $data['updated_by'] = auth()->user()->id;
        Region::create($data);
        return true;
    }
    public function regionUpdate($id, $data = [])
    {
        $region = $this->regionGet($id);
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['updated_by'] = auth()->user()->id;
        $region->update($data);
        return true;
    }
    /*
    * =============================================================================================== END REGION ===============================================================================================
    */



    /*
    * =============================================================================================== START POSITION ===============================================================================================
    */
    public function positionList($data = [], $limit = false, $order = false, $paginate = true)
    {
        if (!$order) $order = ['created_at', 'desc'];
        //start query
        $query = Position::query()->with(['level']);
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("name", "like", "%{$data['q']}%")
                    ->orWhere("description", "like", "%{$data['q']}%");
            });
        }
        if (isset($data['type'])) {
            $query->where('type', $data['type']);
        }
        //limit
        if ($limit != false) {
            if ($paginate == true) {
                $result = $query->paginate($limit);
            } else {
                $result = $query->limit($limit)->get();
            }
        } else {
            $result = $query->get();
        }

        return $result;
    }
    public function positionGet($data)
    {
        //if $data not array
        if (!is_array($data)) {
            $query = Position::find($data);
        } else {
            $query = Position::where($data[0], $data[1])->get()->first();
        }
        $result = $query;
        return $result;
    }
    public function positionSave($data = [])
    {
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $data['updated_by'] = auth()->user()->id;
        if ($this->positionGet(['name', $data['name']])) {
            $this->error = 'Jabatan sudah ada.';
            return false;
        }
        Position::create($data);
        return true;
    }
    public function positionUpdate($id, $data = [])
    {
        $position = $this->positionGet($id);
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['updated_by'] = auth()->user()->id;
        if ($data['name'] != $position->name) {
            if ($this->positionGet(['name', $data['name']])) {
                $this->error = 'Jabatan sudah ada.';
                return false;
            }
        }
        $position->update($data);
        return true;
    }
    /*
    * =============================================================================================== END POSITION ===============================================================================================
    */



    /*
    * =============================================================================================== START MANAGEMENT ===============================================================================================
    */
    public function managementList($data = [], $limit = false, $order = false, $paginate = true)
    {
        if (!$order) $order = ['created_at', 'desc'];
        //start query
        $query = Management::query()->with(['user', 'member', 'position']);
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("name", "like", "%{$data['q']}%")
                    ->orWhere("description", "like", "%{$data['q']}%");
            });
        }
        if (isset($data['position_id']) && $data['position_id'] != 'all') {
            $query->where('position_id', $data['position_id']);
        }
        //limit
        if ($limit != false) {
            if ($paginate == true) {
                $result = $query->paginate($limit);
            } else {
                $result = $query->limit($limit)->get();
            }
        } else {
            $result = $query->get();
        }

        return $result;
    }
    public function managementGet($data)
    {
        //if $data not array
        if (!is_array($data)) {
            $query = Management::find($data);
        } else {
            $query = Management::where($data[0], $data[1])->get()->first();
        }
        $result = $query;
        return $result;
    }
    public function managementSave($data = [])
    {
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $data['updated_by'] = auth()->user()->id;
        $position = $this->positionGet($data['position_id']);
        $member = $this->memberGet($data['member_id']);

        if ($this->managementGet(['member_id', $data['member_id']])) {
            $this->error = 'Anggota sudah menjadi pengurus.';
            return false;
        }

        $user = [
            'name' => $member['name'],
            'username' => $data['username'],
            'password' => $data['password'],
            'level_id' => $position->level_id,
        ];
        if (!$this->user->userSave($user)) {
            $this->error = $this->user->error;
            return false;
        }
        $data['user_id'] = $this->user->last_user_id;

        unset($data['username'], $data['password']);
        Management::create($data);

        return true;
    }
    public function managementUpdate($id, $data = [])
    {
        $management = $this->managementGet($id);
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['updated_by'] = auth()->user()->id;
        $position = $this->positionGet($data['position_id']);
        $user = [
            'username' => $data['username'],
            'password' => $data['password'],
            'name' => $management->member->name,
            'level_id' => $position->level_id
        ];
        if (!$this->user->userUpdate($management->user_id, $user)) {
            $this->error = $this->user->error;
            return false;
        }
        unset($data['username'], $data['password']);
        $management->update($data);
        return true;
    }
    /*
    * =============================================================================================== END MANAGEMENT ===============================================================================================
    */



    /*
    * =============================================================================================== START MANAGEMENT ===============================================================================================
    */
    public function employeeList($data = [], $limit = false, $order = false, $paginate = true)
    {
        if (!$order) $order = ['created_at', 'desc'];
        //start query
        $query = Employee::query()->with(['user', 'position']);
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("name", "like", "%{$data['q']}%")
                    ->orWhere("description", "like", "%{$data['q']}%");
            });
        }
        if (isset($data['position_id']) && $data['position_id'] != 'all') {
            $query->where('position_id', $data['position_id']);
        }
        //limit
        if ($limit != false) {
            if ($paginate == true) {
                $result = $query->paginate($limit);
            } else {
                $result = $query->limit($limit)->get();
            }
        } else {
            $result = $query->get();
        }

        return $result;
    }
    public function employeeGet($data)
    {
        //if $data not array
        if (!is_array($data)) {
            $query = Employee::find($data);
        } else {
            $query = Employee::where($data[0], $data[1])->get()->first();
        }
        $result = $query;
        return $result;
    }
    public function employeeSave($data = [])
    {
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $data['updated_by'] = auth()->user()->id;
        $position = $this->positionGet($data['position_id']);

        $user = [
            'name' => $data['name'],
            'email' => $data['email'],
            'username' => $data['username'],
            'password' => $data['password'],
            'level_id' => $position->level_id,
        ];
        if (!$this->user->userSave($user)) {
            $this->error = $this->user->error;
            return false;
        }
        $data['user_id'] = $this->user->last_user_id;

        unset($data['username'], $data['password']);
        Employee::create($data);

        return true;
    }
    public function employeeUpdate($id, $data = [])
    {
        $employee = $this->employeeGet($id);
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['updated_by'] = auth()->user()->id;
        $position = $this->positionGet($data['position_id']);
        $user = [
            'username' => $data['username'],
            'password' => $data['password'],
            'email' => $data['email'],
            'name' => $data['name'],
            'level_id' => $position->level_id
        ];
        if (!$this->user->userUpdate($employee->user_id, $user)) {
            $this->error = $this->user->error;
            return false;
        }
        unset($data['username'], $data['password']);
        $employee->update($data);
        return true;
    }
    /*
    * =============================================================================================== END MANAGEMENT ===============================================================================================
    */



    /*
    * =============================================================================================== START ASSET ===============================================================================================
    */
    public function assetList($data = [], $limit = false, $order = false, $paginate = true)
    {
        if (!$order) $order = ['created_at', 'desc'];
        //start query
        $query = Asset::query();
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("name", "like", "%{$data['q']}%")
                    ->orWhere("note", "like", "%{$data['q']}%");
            });
        }
        if (isset($data['cat_id']) && $data['cat_id'] != 'all') {
            $query->where('asset_category_id', $data['cat_id']);
        }
        //limit
        if ($limit != false) {
            if ($paginate == true) {
                $result = $query->paginate($limit);
            } else {
                $result = $query->limit($limit)->get();
            }
        } else {
            $result = $query->get();
        }

        return $result;
    }
    public function assetGet($data)
    {
        //if $data not array
        if (!is_array($data)) {
            $query = Asset::find($data);
        } else {
            $query = Asset::where($data[0], $data[1])->get()->first();
        }
        $result = $query;
        return $result;
    }
    public function assetSave($data = [])
    {
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $data['updated_by'] = auth()->user()->id;
        Asset::create($data);
        return true;
    }
    public function assetUpdate($id, $data = [])
    {
        $asset = $this->assetGet($id);
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['updated_by'] = auth()->user()->id;
        $asset->update($data);
        return true;
    }
    /*
    * =============================================================================================== END ASSET ===============================================================================================
    */



    /*
    * =============================================================================================== START ASSET CATEGORY ===============================================================================================
    */
    public function assetCategoryList($data = [], $limit = false, $order = false, $paginate = true)
    {
        if (!$order) $order = ['created_at', 'desc'];
        //start query
        $query = AssetCategory::query();
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("name", "like", "%{$data['q']}%")
                    ->orWhere("account_code", "like", "%{$data['q']}%")
                    ->orWhere("description", "like", "%{$data['q']}%");
            });
        }
        //limit
        if ($limit != false) {
            if ($paginate == true) {
                $result = $query->paginate($limit);
            } else {
                $result = $query->limit($limit)->get();
            }
        } else {
            $result = $query->get();
        }

        return $result;
    }
    public function assetCategoryGet($data)
    {
        //if $data not array
        if (!is_array($data)) {
            $query = AssetCategory::find($data);
        } else {
            $query = AssetCategory::where($data[0], $data[1])->get()->first();
        }
        $result = $query;
        return $result;
    }
    public function assetCategorySave($data = [])
    {
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $data['updated_by'] = auth()->user()->id;
        AssetCategory::create($data);
        return true;
    }
    public function assetCategoryUpdate($id, $data = [])
    {
        $assetCategory = $this->assetCategoryGet($id);
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['updated_by'] = auth()->user()->id;
        $assetCategory->update($data);
        return true;
    }
    /*
    * =============================================================================================== END ASSET CATEGORY ===============================================================================================
    */

    public function pengurusAssignment()
    {
        $ketua = $this->managementGet(['position_id', 1]);
        $wakil_ketua = $this->managementGet(['position_id', 2]);
        $sekretaris = $this->managementGet(['position_id', 3]);
        $bendahara = $this->managementGet(['position_id', 5]);
        $manager = $this->employeeGet(['position_id', 10]);
        $kasir = $this->employeeGet(['position_id', 13]);

        $data = [
            'ketua' => $ketua->member->name ?? '',
            'wakil_ketua' => $wakil_ketua->member->name ?? '',
            'sekretaris' => $sekretaris->member->name ?? '',
            'bendahara' => $bendahara->member->name ?? '',
            'manager' => $manager->name ?? '',
            'kasir' => $kasir->name ?? '',
        ];

        return $data;
    }
}