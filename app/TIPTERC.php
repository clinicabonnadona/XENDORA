<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TIPTERC extends Model
{
    //
    protected $table = 'TIPTERC';

    protected $fillable = [
        'TipTerDesc',
        'TipTerEst',
        'user_id',
    ];

    public function suministro()
    {
        return $this->hasOne(SUMINISTROS::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
