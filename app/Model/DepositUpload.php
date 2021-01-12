<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DepositUpload extends Model
{
    protected $guraded = ['id'];
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
    public function type()
    {
        return $this->belongsTo(DepositType::class, 'deposit_type_id', 'id');
    }
    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}