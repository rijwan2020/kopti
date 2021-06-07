<?php
namespace App\Repositories;

use App\Model\Region;

class Wilayah extends BaseRepo
{
    public function __construct(Region $model) {
        $this->model = $model;
        $this->search_field = ['code', 'name', 'description'];
    }
}
