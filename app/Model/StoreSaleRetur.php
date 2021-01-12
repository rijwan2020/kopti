<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class StoreSaleRetur extends Model
{
    protected $guarded = ['id'];

    public function warehouse()
    {
        return $this->belongsTo(StoreWarehouse::class, 'warehouse_id', 'id');
    }
    public function member()
    {
        return $this->belongsTo(Member::class);
    }
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
    public function sale()
    {
        return $this->belongsTo(StoreSale::class, 'sale_id', 'id');
    }
    public function item()
    {
        return $this->belongsTo(StoreItem::class, 'item_id', 'id');
    }
}