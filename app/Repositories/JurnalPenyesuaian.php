<?php
namespace App\Repositories;

use App\Model\AdjustingJournal;

class JurnalPenyesuaian extends BaseRepo
{
    public function __construct(AdjustingJournal $model) {
        $this->model = $model;
    }
}
