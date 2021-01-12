<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class StoreItemDetail extends Model
{
    protected $guarded = ['id'];

    public function userInput()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
    public function warehouse()
    {
        return $this->belongsTo(StoreWarehouse::class, 'warehouse_id', 'id');
    }
    public function suplier()
    {
        return $this->belongsTo(StoreSuplier::class);
    }
    public function item()
    {
        return $this->belongsTo(StoreItem::class, 'item_id', 'id');
    }
}