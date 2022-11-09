<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProviderAgent extends Model
{
    //
    protected $table = "provider_agent";

    protected $fillable = [
        'nombres',
        'apellidos',
        'telefono',
        'email',
        'estado',
        'user_id'
    ];

    public function providers()
    {
        return $this->belongsToMany(Provider::class, 'provider_agent_provider', 'provider_id');
        //return $this->belongsToMany(Provider::class, 'provider_agent_provider', 'provider_agent_id');
        //return $this->belongsToMany(Provider::class, 'provider_agent_provider_to_evaluate', 'provider_to_evaluate_id', 'provider_agent_id');
        //return $this->belongsToMany(Provider::class, 'provider_agent_provider_to_evaluate', 'provider_to_evaluate_id', 'provider_agent_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
