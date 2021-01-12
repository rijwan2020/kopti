<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Region extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];

    public function depositType()
    {
        return $this->hasMany(DepositTransaction::class);
    }
    public function member()
    {
        return $this->hasMany(Member::class, 'region_id', 'id');
    }
    public function penjualan()
    {
        return $this->hasMany(StoreSale::class);
    }
    public function utangHistori()
    {
        return $this->hasMany(StoreSaleDebtHistori::class);
    }
}