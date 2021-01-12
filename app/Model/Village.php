<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    protected $casts = ['id' => 'string'];
    public $incrementing = false;
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
}