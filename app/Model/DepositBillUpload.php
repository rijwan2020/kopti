<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DepositBillUpload extends Model
{
    protected $guarded = ['id'];
    public function member()
    {
        return $this->belongsTo(Member::class);
    }
    public function deposit()
    {
        return $this->belongsTo(Deposit::class);
    }
}