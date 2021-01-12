<?php

namespace App;

use App\Model\Member;
use App\Model\StoreWarehouseUser;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }

    public function last_access()
    {
        $this->last_access = date('Y-m-d H:i:s');
        $this->update();
    }

    public function isDev()
    {
        return ($this->exists && $this->level_id === 0);
    }

    public function isMember()
    {
        if ($this->level_id <= 99 and $this->level_id > 90) {
            return true;
        }
        return false;
    }

    public function isAdmin()
    {
        if ($this->level_id < 10 and $this->level_id > 0) {
            return true;
        }
        return false;
    }

    public function isPengurus()
    {
        if ($this->level_id >= 10 and $this->level_id <= 50) {
            return true;
        }
        return false;
    }
    public function isKaryawan()
    {
        if ($this->level_id >= 51 and $this->level_id <= 90) {
            return true;
        }

        return false;
    }
    public function isGudang()
    {
        if ($this->level_id == 71) {
            return true;
        }
        return false;
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function hasRule($param)
    {
        if ($this->isDev()) {
            return true;
        }
        $rule = json_decode($this->level->rule);
        if (isset($rule->$param) && $rule->$param == 1) {
            return true;
        }
        return false;
    }

    public function member()
    {
        return $this->hasOne(Member::class);
    }

    public function getWarehouseId()
    {
        $data = StoreWarehouseUser::where('user_id', $this->id)->first();
        return $data->warehouse_id;
    }
}