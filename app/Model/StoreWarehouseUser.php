<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class StoreWarehouseUser extends Model
{
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(StoreWarehouse::class, 'warehouse_id', 'id');
    }
}