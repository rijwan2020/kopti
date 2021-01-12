<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $guarded = ['id'];
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
    public function village()
    {
        return $this->belongsTo(Village::class);
    }
    public function district()
    {
        return $this->belongsTo(District::class);
    }
    public function regency()
    {
        return $this->belongsTo(Regency::class);
    }
    public function province()
    {
        return $this->belongsTo(Province::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function deposit()
    {
        return $this->hasMany(Deposit::class);
    }
    public function transaksi()
    {
        return $this->hasMany(AdjustingJournal::class);
    }
    public function penjualan()
    {
        return $this->hasMany(StoreSale::class, 'member_id', 'id');
    }
    public function utangHistori()
    {
        return $this->hasMany(StoreSaleDebtHistori::class);
    }
    public function saleDebt()
    {
        return $this->hasMany(StoreSaleDebt::class);
    }
}