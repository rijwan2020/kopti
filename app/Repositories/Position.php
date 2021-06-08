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

    public function create($data)
    {
        if($this->get(['name', $data['name']])){
            $this->error = "Posisi/jabatan sudah ada";
            return false;
        }
        $data = parent::create($data);
        return $data;
    }
    

    public function edit($id, $data = [])
    {
        $posisi = $this->get($id);
        if ($data['name'] != $posisi->name) {
            if($this->get(['name', $data['name']])){
                $this->error = "Pposisi/abatan sudah ada";
                return false;
            }
        }
        $data = parent::edit($id, $data);
        return $data;
    }
}
