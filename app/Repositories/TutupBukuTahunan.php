<?php
namespace App\Repositories;

use App\Model\CloseYearlyBook;

class TutupBukuTahunan extends BaseRepo
{
    public function __construct(CloseYearlyBook $model) {
        $this->model = $model;
        $this->search_field = ['description'];
    }
}
