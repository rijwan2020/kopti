<?php
namespace App\Repositories;

use App\Model\CloseMonthlyBook;

class TutupBukuBulanan extends BaseRepo
{
    public function __construct(CloseMonthlyBook $model) {
        $this->model = $model;
        $this->search_field = ['description'];
    }
}
