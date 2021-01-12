<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class StoreItemCard extends Model
{
    protected $guarded = ['id'];
    public function item()
    {
        return $this->belongsTo(StoreItem::class, 'item_id', 'id');
    }

    public function detail()
    {
        return $this->hasMany(StoreItemCardDetail::class, 'card_id', 'id');
    }
}