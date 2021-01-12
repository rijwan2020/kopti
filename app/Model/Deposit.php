<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    protected $guarded = ['id'];
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
    public function bill()
    {
        return $this->hasOne(DepositBill::class);
    }
    public function transaction()
    {
        return $this->hasMany(DepositTransaction::class);
    }
}