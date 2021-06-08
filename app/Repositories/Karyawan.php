<?php
namespace App\Repositories;

use App\Model\Employee;

class Karyawan extends BaseRepo
{
    public function __construct(Employee $model) {
        $this->model = $model;
    }
}
