<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $guarded = ['id'];
    public function group()
    {
        return $this->belongsTo(AccountGroup::class, 'group_id', 'id');
    }
    public function jurnalTransaksi()
    {
        return $this->hasMany(JournalDetail::class, 'account_code', 'code');
    }
    public function jurnalPenyesuaian()
    {
        return $this->hasMany(AdjustingJournalDetail::class, 'account_code', 'code');
    }
}