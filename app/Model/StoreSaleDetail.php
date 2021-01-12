<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class StoreSaleDetail extends Model
{
    protected $guarded = ['id'];
    public function item()
    {
        return $this->belongsTo(StoreItem::class, 'item_id', 'id');
    }
    public function sale()
    {
        return $this->belongsTo(StoreSale::class, 'sale_id', 'id');
    }
}