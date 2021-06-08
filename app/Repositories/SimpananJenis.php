<?php
namespace App\Repositories;

use App\Model\DepositType;

class SimpananJenis extends BaseRepo
{
    public function __construct(DepositType $model) {
        $this->model = $model;
        $this->search_field = ['name', 'code', 'description'];
    }
}
