<?php
namespace App\Repositories;

use App\Model\AccountGroup;

class AkunGrup extends BaseRepo
{
    public function __construct(AccountGroup $model) {
        $this->model = $model;
        $this->search_field = ['name', 'description'];
    }
}
