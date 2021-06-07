<?php
namespace App\Repositories;

use App\Model\Management;
use App\Model\Position as MPosition;
use App\Model\Region;

class Position extends BaseRepo
{
    public function __construct(MPosition $model) {
        $this->model = $model;
        $this->search_field = ['name', 'description'];
    }
}
