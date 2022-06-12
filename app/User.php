<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;

    protected $fillable = [
        'name', 'email', 'password', "phone", "company_name", "role_id", "biller_id", "warehouse_id", "is_active", "is_deleted", 'google2fa_secret',
    ];

    protected $hidden = [
        'password', 'remember_token', 'google2fa_secret',
    ];

    // public function setGoogle2faSecretAttribute($value)
    // {
    //     $this->attributes['google2fa_secret'] = encrypt($value);
    // }

    // public function getGoogle2faSecretAttribute($value)
    // {
    //     return decrypt($value);
    // }

    public function isActive()
    {
        return $this->is_active;
    }

    public function holiday()
    {
        return $this->hasMany('App\Holiday');
    }
}
