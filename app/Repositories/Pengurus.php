<?php
namespace App\Repositories;

use App\Model\Management;
use App\Model\Region;

class Pengurus extends BaseRepo
{
    private $karyawan;
    public function __construct(Management $model, Karyawan $karyawan) {
        $this->model = $model;
        $this->karyawan = $karyawan;
        // $this->search_field = ['code', 'name', 'description'];
    }

    public function assignment()
    {
        $ketua = $this->get(['position_id', 1]);
        $wakil_ketua = $this->get(['position_id', 2]);
        $sekretaris = $this->get(['position_id', 3]);
        $bendahara = $this->get(['position_id', 5]);
        $manager = $this->karyawan->get(['position_id', 10]);
        $kasir = $this->karyawan->get(['position_id', 13]);

        $data = [
            'ketua' => $ketua->member->name ?? '',
            'wakil_ketua' => $wakil_ketua->member->name ?? '',
            'sekretaris' => $sekretaris->member->name ?? '',
            'bendahara' => $bendahara->member->name ?? '',
            'manager' => $manager->name ?? '',
            'kasir' => $kasir->name ?? '',
        ];

        return $data;
    }
}
