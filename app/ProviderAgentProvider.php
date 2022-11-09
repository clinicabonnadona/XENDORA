<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProviderAgentProvider extends Model
{
    //
    protected $table = "provider_agent_provider";

    protected $fillable = [
        'provider_id',
        'provider_agent_id',
        'estado',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
