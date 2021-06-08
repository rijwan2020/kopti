<?php
namespace App\Repositories;

use App\Model\Deposit;

class Simpanan extends BaseRepo
{
    public function __construct(Deposit $model) {
        $this->model = $model;
        $this->search_field = ['account_number'];
    }
}
