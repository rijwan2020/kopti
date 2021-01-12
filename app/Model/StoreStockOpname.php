<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class StoreStockOpname extends Model
{
    protected $guarded = ['id'];
    public function itemdetail()
    {
        return $this->belongsTo(StoreItemDetail::class, 'item_detail_id', 'id');
    }
    public function warehouse()
    {
        return $this->belongsTo(StoreWarehouse::class, 'warehouse_id', 'id');
    }
}