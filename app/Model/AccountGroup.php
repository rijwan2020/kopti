<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AccountGroup extends Model
{
    protected $guarded = ['id'];
    public function account()
    {
        return $this->hasMany(Account::class, 'group_id', 'id');
    }
    public function golongan()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }
}