<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProviderAgentLineProviderAgent extends Model
{
    //
    protected $table = "provider_agent_line_provider_agent";

    protected $fillable = [
        'provider_agent_id',
        'provider_agent_line_id',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
