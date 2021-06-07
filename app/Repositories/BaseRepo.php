<?php
namespace App\Repositories;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

abstract class BaseRepo 
{
    protected $model;
    protected $error = '';
    protected $limit = false;
    protected $order = 'id';
    protected $sort = 'ASC';
    protected $q = '';
    protected $where = false;
    protected $search_field = [];
    protected $params = [];
    protected $relations = [];
    protected $validationCreate = [];
    protected $validationUpdate = [];

    public $last_id = 0;

    public function getError()
    {
        return $this->error;
    }

    public function setRelation($data)
    {
        $this->relations = $data;
    }

    /**
     * Get List Data
     */
    public function list($data = [])
    {
        if(isset($data['order_by'])){
            $this->order = $data['order_by'];
            unset($data['order_by']);
        }

        if(isset($data['sort'])){
            $this->sort = $data['sort'];
            unset($data['sort']);
        }

        if(isset($data['q'])){
            $this->q = $data['q'];
            unset($data['q']);
        }

        if(isset($data['limit'])){
            $this->limit = $data['limit'];
            unset($data['limit']);
        }
        
        if(isset($data['relations'])){
            $this->relations = $data['relations'];
            unset($data['relations']);
        }
        
        if(!empty($data)){
            $this->params = $data;
        }

        $model = $this->model;

        $model = $this->withParameter($model);
        
        if ($this->q != "") {
            $model = $this->cari($model);
        }

        if(!empty($this->relations)){
            $model = $model->with($this->relations);
        }

        $model = $model->orderBy($this->order, $this->sort);

        if ($this->limit) {
            $res = $model->paginate($this->limit);
        }else{
            $res = $model->get();
        }
        return $res;
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
            });
        }
        return $model;
    }

    public function withParameter($model)
    {
        if (empty($this->params)) {
            return $model;
        }
        $where = [];
        foreach ($this->params as $key => $value) {
            $where[] = [$key, '=', $value];
        }
        $model = $model->where($where);
        
        return $model;
    }

    /**
     * Get One data
     */
    public function get($id)
    {
        $model = $this->model;
        if(!empty($this->relations)){
            $model = $model->with($this->relations);
        }
        if (is_array($id)) {
            $data = $model->where($id[0], $id[1])->first();
        }else{
            $data = $model->find($id);
        }
        return $data;
    }

    /**
     * Save data
     */
    public function create($data)
    {
        if(!$data){
            return false;
        }
        $data['created_by'] = $data['updated_by'] = auth()->id();
        DB::beginTransaction();
        try {
            $input = $this->save($data);
            $this->last_id = $input->id;
            DB::commit();
            return $input;
        } catch (\Illuminate\Database\QueryException $e) {
            Log::info("BaseRepo::create()");
            Log::info($e->getMessage());
            $this->error = $e->getMessage();
            DB::rollBack();
            return false;
        }
    }

    public function save($data = [])
    {
        return $this->model->create($data);
    }

    /**
     * Update Data
     */
    public function edit($id, $data = [])
    {
        if(!$data){
            return false;
        }
        $data['updated_by'] = auth()->id();
        DB::beginTransaction();
        try {
            $req = $this->update($id, $data);
            DB::commit();
            return $req;
        } catch (\Illuminate\Database\QueryException $e) {
            Log::info("BaseRepo::edit()");
            Log::info($e->getMessage());
            $this->error = $e->getMessage();
            DB::rollBack();
            return false;
        }
    }

    public function update($id, $data = [])
    {
        if(is_array($id)){
            $model = $this->model->where($id[0], $id[1]);
        } else {
            $model = $this->model->where('id', $id);
        }
        return $model->update($data);
    }
}
