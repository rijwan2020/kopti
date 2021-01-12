<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class StoreSuplier extends Model
{
    protected $guarded = ['id'];

    public function item()
    {
        return $this->hasMany(StoreItemDetail::class, 'suplier_id', 'id');
    }
    public function utangHistori()
    {
        return $this->hasMany(StorePurchaseDebtHistory::class, 'suplier_id', 'id');
    }
}