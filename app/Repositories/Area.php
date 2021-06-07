<?php

namespace App\Repositories;

use App\Model\Regency;
use App\Model\Village;

class Area
{
    /*
    * =============================================================================================== START VILLAGE ===============================================================================================
    */
    public function villageList($data = [], $limit = false, $order = false, $paginate = true)
    {
        //start query
        $query = Village::query();
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("name", "like", "%{$data['q']}%");
            });
        }
        //limit
        if ($limit != false) {
            if ($paginate == true) {
                $result = $query->paginate($limit);
            } else {
                $result = $query->limit($limit)->get();
            }
        } else {
            $result = $query->get();
        }

        return $result;
    }
    public function villageGet($id)
    {
        //if $id not array
        if (!is_array($id)) {
            $query = Village::find($id);
        } else {
            $query = Village::where($id[0], $id[1])->get()->first();
        }
        $result = $query;
        return $result;
    }
    /*
    * =============================================================================================== END VILLAGE ===============================================================================================
    */

    /*
    * =============================================================================================== START REGENCY ===============================================================================================
    */
    public function regencyList($data = [], $limit = false, $order = false, $paginate = true)
    {
        //start query
        $query = Regency::query();
        //search by keyword
        if (isset($data['q']) && !empty($data['q'])) {
            $query->where(function ($q) use ($data) {
                $q->where("name", "like", "%{$data['q']}%");
            });
        }
        //limit
        if ($limit != false) {
            if ($paginate == true) {
                $result = $query->paginate($limit);
            } else {
                $result = $query->limit($limit)->get();
            }
        } else {
            $result = $query->get();
        }

        return $result;
    }
    public function regencyGet($id)
    {
        //if $id not array
        if (!is_array($id)) {
            $query = Regency::find($id);
        } else {
            $query = Regency::where($id[0], $id[1])->get()->first();
        }
        $result = $query;
        return $result;
    }
}