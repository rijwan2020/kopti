<?php

namespace App\Classes;

use App\Level;
use App\User;
use Illuminate\Support\Facades\DB;

class UserClass
{
    //declare public variable
    public $error = '', $last_user_id = 0;
    public function random_pass($long)
    {
        $char = 'QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm';
        $string = '';
        for ($i = 0; $i < $long; $i++) {
            $pos = rand(0, strlen($char) - 1);
            $string .= $char[$pos];
        }
        return $string;
    }
    /*
    * =============================================================================================== START USER ===============================================================================================
    */
    public function userList($data = [], $limit = false, $order = false, $paginate = true)
    {
        if (!$order) $order = ['created_at', 'desc'];
        //start query
        DB::enableQueryLog();
        $query = User::query();
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("name", "like", "%{$data['q']}%")
                    ->orWhere("username", "like", "%{$data['q']}%")
                    ->orWhere("email", "like", "%{$data['q']}%");
            });
        }
        //search by level
        if (isset($data['level_id']) && $data['level_id'] != 'all') {
            $query->where('level_id', $data['level_id']);
        }
        //don't get dev level
        $query->where("level_id", "!=", "0");
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
    public function userGet($data)
    {
        //if $data not array
        if (!is_array($data)) {
            $query = User::find($data);
        } else {
            $query = User::where($data[0], $data[1])->get()->first();
        }
        $result = $query;
        return $result;
    }
    public function userSave($data)
    {
        $data['created_by'] = $data['updated_by'] = auth()->user()->id;
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');
        //cek username
        if ($this->userGet(['username', $data['username']])) {
            $this->error = 'Username sudah digunakan';
            return false;
        }
        //cek email
        if (isset($data['email']) && !empty($data['email'])) {
            if ($this->userGet(['email', $data['email']])) {
                $this->error = 'Email sudah digunakan';
                return false;
            }
        }
        //create user
        $user = User::create($data);
        $this->last_user_id = $user->id;
        return true;
    }
    public function userUpdate($id, $data)
    {
        //get user yg akan di edit
        $user = $this->userGet($id);

        //cek username jika diubah
        if ($user->username != $data['username']) {
            if ($this->userGet(['username', $data['username']])) {
                $this->error = 'Username telah digunakan';
                return false;
            }
        }

        //cek email jika diubah
        if (isset($data['email']) && $user->email != $data['email']) {
            if ($this->userGet(['email', $data['email']])) {
                $this->error = 'Email telah digunakan';
                return false;
            }
        }
        if ($data['password'] == null) {
            unset($data['password']);
        }
        $user->update($data);
        return true;
    }
    /*
    * =============================================================================================== END USER ===============================================================================================
    */

    /*
    * =============================================================================================== START LEVEL ===============================================================================================
    */
    public function levelList($data = [])
    {
        //start query
        $query = Level::query();
        //keyword search
        if (isset($data['q'])) {
            $query->where('name', 'like', "%{$data['q']}%");
        }
        //dont get dev level
        $query->where('id', '!=', 0);

        $result = $query->get();
        return $result;
    }
    public function levelGet($data)
    {
        //if $data not array
        if (!is_array($data)) {
            $query = Level::find($data);
        } else {
            $query = Level::where($data[0], $data[1])->get()->first();
        }
        $result = $query;
        return $result;
    }
    public function levelSave($data = [])
    {
        if ($this->levelGet($data['id'])) {
            $this->error = 'No Level sudah digunakan.';
            return false;
        }
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');
        $data['created_by'] = $data['updated_by'] = auth()->user()->id;

        Level::insert($data);
        return true;
    }
    public function levelUpdate($data = [])
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['updated_by'] = auth()->user()->id;

        $level = $this->levelGet($data['id']);
        unset($data['id']);
        $level->update($data);
        return true;
    }
    /*
    * =============================================================================================== END LEVEL ===============================================================================================
    */
}