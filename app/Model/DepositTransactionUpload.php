<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DepositTransactionUpload extends Model
{
    protected $guarded = ['id'];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}