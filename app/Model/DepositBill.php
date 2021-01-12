<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DepositBill extends Model
{
    protected $guarded = ['id'];
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
    public function depositType()
    {
        return $this->belongsTo(DepositType::class, 'deposit_type_id', 'id');
    }
    public function member()
    {
        return $this->belongsTo(Member::class);
    }
    public function deposit()
    {
        return $this->belongsTo(Deposit::class);
    }
}