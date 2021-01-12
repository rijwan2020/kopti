<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    protected $guarded = ['id'];
    public function detail()
    {
        return $this->hasMany(JournalDetail::class);
    }
    public function userInput()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function userEdit()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
    public function userDelete()
    {
        return $this->belongsTo(User::class, 'deleted_by', 'id');
    }
}