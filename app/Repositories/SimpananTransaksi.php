<?php
namespace App\Repositories;

use App\Model\DepositTransaction;

class SimpananTransaksi extends BaseRepo
{
    public function __construct(DepositTransaction $model) {
        $this->model = $model;
        // $this->search_field = ['name', 'code', 'description'];
    }
}
