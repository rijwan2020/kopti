<?php
namespace App\Repositories;

use App\Model\Member;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Anggota extends BaseRepo
{
    private $user;
    public function __construct(Member $model, User $user) {
        $this->model = $model;
        $this->user = $user;
        $this->search_field = ['code', 'name', 'phone', 'place_of_birth', 'address'];
    }

    public function create($data)
    {
        if(!$data){
            return false;
        }
        $data['user_id'] = 0;
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $data['updated_by'] = auth()->id();
        DB::beginTransaction();
        try {
            if ($data['status'] == 1) {
                $user = [
                    'name' => $data['name'],
                    'username' => $data['code'],
                    'password' => $data['code'],
                    'level_id' => 99,
                    'image' => $data['image'] ?? ''
                ];
    
                $this->user->create($user);
                $data['user_id'] = $this->user->last_id;
            }
            $input = $this->save($data);
            $this->last_id = $input->last_id;
            DB::commit();
            return $input;
        } catch (\Illuminate\Database\QueryException $e) {
            Log::info("Anggota::create()");
            Log::info($e->getMessage());
            $this->error = $e->getMessage();
            DB::rollBack();
            return false;
        }
    }

    public function edit($id, $data = [])
    {
        if(!$data){
            return false;
        }
        $member = $this->get($id);
        if (isset($data['code']) && $data['code'] != $member->code) {
            if ($this->get(['code', $data['code']])) {
                $this->error = 'Kode anggota sudah digunakan.';
                return false;
            }
        }
        $data['updated_by'] = auth()->id();
        DB::beginTransaction();
        try {
            $user = [
                'username' => $data['username'],
                'name' => $data['name'],
                'password' => $data['password'],
                'image' => $data['image'] ?? ''
            ];
            unset($data['username'], $data['password']);
            $req = $this->update($id, $data);
            if ($member->user_id != 0) {
                if(!$this->user->edit($member->user_id, $user)){
                    throw new Exception($this->user->error);
                }
            }
            DB::commit();
            return $req;
        } catch (\Illuminate\Database\QueryException | Exception $e) {
            Log::info("Anggota::edit()");
            Log::info($e->getMessage());
            $this->error = $e->getMessage();
            DB::rollBack();
            return false;
        }
    }
}
