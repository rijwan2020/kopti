<?php
namespace App\Repositories;

use App\Model\Deposit;

class Simpanan extends BaseRepo
{
    public function __construct(Deposit $model) {
        $this->model = $model;
        $this->search_field = ['account_number'];
    }

    public function cari($model)
    {
        if (!empty($this->search_field)) {
            $q = $this->q;
            $search_field = $this->search_field;
            $model = $model->where(function($query) use ($q, $search_field){
                foreach ($search_field as $key => $value) {
                    $query = $query->orWhere($value, "Like", "%" . $q ."%");
                }
                $query = $query->orWhereHas("member", function ($res) use($q){
                    $res->where('name', 'like', "%{$q}%")
                        ->orWhere('code', 'like', "%{$q}%");
                });
            });
        }
        return $model;
    }
}
