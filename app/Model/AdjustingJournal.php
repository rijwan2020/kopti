<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class AdjustingJournal extends Model
{
    protected $guarded = ['id'];
    public function detail()
    {
        return $this->hasMany(AdjustingJournalDetail::class);
    }
    public function userInput()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function userEdit()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}