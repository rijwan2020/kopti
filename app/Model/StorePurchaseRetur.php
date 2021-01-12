<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class StorePurchaseRetur extends Model
{
    protected $guarded = ['id'];
    public function item()
    {
        return $this->belongsTo(StoreItem::class, 'item_id', 'id');
    }
    public function suplier()
    {
        return $this->belongsTo(StoreSuplier::class, 'suplier_id', 'id');
    }
}