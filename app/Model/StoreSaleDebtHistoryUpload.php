<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class StoreSaleDebtHistoryUpload extends Model
{
    protected $guarded = ['id'];
    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}