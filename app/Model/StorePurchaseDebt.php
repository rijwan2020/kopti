<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class StorePurchaseDebt extends Model
{
    protected $guarded = ['id'];

    public function purchase()
    {
        return $this->belongsTo(StorePurchase::class, 'purchase_id', 'id');
    }
    public function suplier()
    {
        return $this->belongsTo(StoreSuplier::class);
    }
}