<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class CloseMonthlyBook extends Model
{
    protected $guarded = ['id'];
    public function userInput()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}