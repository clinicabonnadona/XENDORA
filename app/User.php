<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class User extends Authenticatable
{
    use Notifiable, HasRoles;

    //public $timestamps = false;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'lastName',
        'username',
        'doctype_id',
        'document',
        'address',
        'phone',
        'email',
        'password',
        'state',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function doctype()
    {
        return $this->belongsTo('App\Doctype');
    }

    public function formafar()
    {
        return $this->hasOne(FORMAFAR::class);
    }

    public function formapre()
    {
        return $this->hasOne(FORMAPRE::class);
    }

    public function tercero()
    {
        return $this->hasOne(TERCEROS::class);
    }

    public function tipdoc()
    {
        return $this->hasOne(TIPDOC::class);
    }

    public function tipterc()
    {
        return $this->hasOne(TIPTERC::class);
    }

    public function suministro()
    {
        return $this->hasOne(SUMINISTROS::class);
    }

    public function maenego()
    {
        return $this->hasOne(MAENEGO::class);
    }

    public function shippingPurchaseOrders()
    {
        return $this->hasOne(ShippingPurchaseOrders::class);
    }

    public function familyIncomeRecord()
    {
        return $this->belongsTo(FamilyIncomeRecord::class);
    }

    public function provider()
    {
        return $this->hasOne(Provider::class);
    }

    public function provider_type()
    {
        return $this->hasOne(ProviderType::class);
    }

    public function provider_agent()
    {
        return $this->hasOne(ProviderAgent::class);
    }

    /* public function provider_agent_line()
    {
        return $this->hasOne(ProviderAgentLine::class);
    }

    public function provider_agent_line_provider_agent()
    {
        return $this->hasOne(ProviderAgentLineProviderAgent::class);
    } */


    public function getAllPermissionsAttribute()
    {
        $permissions = [];

        foreach (Permission::all() as $permission) {
            if (Auth::user()->can($permission->name)) {
                $permissions[] = $permission->name;
            }
        }
        return $permissions;
    }
}
