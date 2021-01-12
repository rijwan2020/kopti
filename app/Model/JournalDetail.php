<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class JournalDetail extends Model
{
    protected $guarded = ['id'];
    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_code', 'code');
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