<?php
namespace App\Repositories;

use App\Model\Account;

class Akun extends BaseRepo
{
    public function __construct(Account $model) {
        $this->model = $model;
        $this->search_field = ['name', 'code'];
    }
}
