<?php
namespace App\Repositories;

use App\Model\Management;
use App\Model\Region;

class Pengurus extends BaseRepo
{
    public function __construct(Management $model) {
        $this->model = $model;
        // $this->search_field = ['code', 'name', 'description'];
    }
}
