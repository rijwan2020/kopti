<?php
namespace App\Repositories;

use App\Model\Management;
use App\Level as MLevel;
use App\Model\Region;

class Level extends BaseRepo
{
    public function __construct(MLevel $model) {
        $this->model = $model;
        $this->search_field = ['name'];
    }
}
