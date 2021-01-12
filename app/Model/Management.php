<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Management extends Model
{
    protected $table = 'managements';
    protected $guarded = ['id'];
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function member()
    {
        return $this->belongsTo(Member::class);
    }
    public function position()
    {
        return $this->belongsTo(Position::class);
    }
}