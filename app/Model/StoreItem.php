<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class StoreItem extends Model
{
    protected $guarded = ['id'];
    public function detail()
    {
        return $this->hasMany(StoreItemDetail::class, 'item_id', 'id');
    }
}