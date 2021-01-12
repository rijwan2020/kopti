<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ShuConfig extends Model
{
    protected $guarded = ['id'];

    public function dataAccount()
    {
        return $this->belongsTo(Account::class, 'account', 'code');
    }
}