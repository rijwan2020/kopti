<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class StoreSaleDebt extends Model
{
    protected $guarded = ['id'];

    public function sale()
    {
        return $this->belongsTo(StoreSale::class, 'sale_id', 'id');
    }
    public function warehouse()
    {
        return $this->belongsTo(StoreWarehouse::class, 'warehouse_id', 'id');
    }
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'id');
    }
    public function histori()
    {
        return $this->hasMany(StoreSaleDebtHistori::class, 'debt_id', 'id');
    }
}