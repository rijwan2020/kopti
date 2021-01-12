<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class StoreItemUpload extends Model
{
    public function suplier()
    {
        return $this->belongsTo(StoreSuplier::class);
    }
}