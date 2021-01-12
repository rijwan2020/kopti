<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DepositType extends Model
{
    protected $guarded = ['id'];

    public function deposit()
    {
        return $this->hasMany(Deposit::class);
    }
}