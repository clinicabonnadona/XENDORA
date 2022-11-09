<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    //
    protected $table = 'provider';
    protected $fillable = [
        'nit',
        'razon_social',
        'direccion',
        'ciudad',
        'estado',
        'provider_type_id',
        'user_id'
    ];

    public function provider_type()
    {
        return $this->belongsTo(ProviderType::class);
    }

    public function provider_agents()
    {
        return $this->belongsToMany(ProviderAgent::class, 'provider_agent_provider', 'provider_id');
        //return $this->belongsToMany(ProviderAgent::class, 'provider_agent_provider', 'provider_agent_id');
        //return $this->belongsToMany(ProviderAgent::class, 'provider_agent_provider_to_evaluate', 'provider_to_evaluate_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    //
}
