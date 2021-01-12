<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $guarded = ['id'];
    public function level()
    {
        return $this->belongsTo('App\Level');
    }
}
