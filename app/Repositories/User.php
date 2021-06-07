<?php
namespace App\Repositories;

use App\User as MUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class User extends BaseRepo
{
    public function __construct(MUser $model) {
        $this->model = $model;
        // $this->search_field = ['code', 'name', 'phone', 'place_of_birth', 'address'];
    }

    public function edit($id, $data = [])
    {
        $user = $this->get($id);
        if (isset($data['username']) && $data['username'] != $user->username) {
            if ($this->get(['username', $data['username']])) {
                $this->error = 'Username sudah digunakan.';
                return false;
            }
        }
        if (isset($data['email']) && $data['email'] != $user->email) {
            if ($this->get(['email', $data['email']])) {
                $this->error = 'Email sudah digunakan.';
                return false;
            }
        }
        if(isset($data['password']) && empty($data['password'] || $data['password'] == null)){
            unset($data['password']);
        }
        $data = parent::edit($id, $data);
        return $data;
    }
}
