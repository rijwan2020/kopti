<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class StoreSaleDebtHistori extends Model
{
    protected $guarded = ['id'];
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}