<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class StoreSale extends Model
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

    public function detail()
    {
        return $this->hasMany(StoreSaleDetail::class, 'sale_id', 'id');
    }
}