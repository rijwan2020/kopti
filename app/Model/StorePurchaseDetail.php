<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class StorePurchaseDetail extends Model
{
    protected $guarded = ['id'];
    public function item()
    {
        return $this->belongsTo(StoreItem::class, 'item_id', 'id');
    }
    public function purchase()
    {
        return $this->belongsTo(StorePurchase::class, 'purchase_id', 'id');
    }
}