<?php
namespace App\Repositories;

use App\Model\DepositUpload;

class SimpananUpload extends BaseRepo
{
    public function __construct(DepositUpload $model) {
        $this->model = $model;
        // $this->search_field = ['name', 'code', 'description'];
    }
}
