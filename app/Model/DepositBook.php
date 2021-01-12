<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class DepositBook extends Model
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
    public function userInput()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}