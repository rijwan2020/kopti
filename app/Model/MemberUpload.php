<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MemberUpload extends Model
{
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
}