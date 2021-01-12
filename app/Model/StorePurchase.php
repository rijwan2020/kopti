<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class StorePurchase extends Model
{
    protected $guarded = ['id'];
    public function suplier()
    {
        return $this->belongsTo(StoreSuplier::class);
    }
    public function detail()
    {
        return $this->hasMany(StorePurchaseDetail::class, 'purchase_id', 'id');
    }
    public function debt()
    {
        return $this->hasOne(StorePurchaseDebt::class, 'purchase_id', 'id');
    }
}