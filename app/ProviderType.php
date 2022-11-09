<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProviderType extends Model
{
    //

    protected $table = 'provider_types';
    protected $fillable = [
        'descripcion',
        'estado',
    ];

    public function provider()
    {
        return $this->hasOne(Provider::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
