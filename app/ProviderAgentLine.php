<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProviderAgentLine extends Model
{
    //
    protected $table = "provider_agent_line";

    protected $fillable = [
        'nombre',
        'estado',
        'user_id'
    ];

    /* public function provider_agent_line_provider_agent()
    {
        return $this->hasMany(ProviderAgentLineProviderAgent::class);
    } */

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
