<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FORMAFAR extends Model
{
    //
    protected $table = 'FORMAFAR';

    protected $fillable = [
        'FormaFDesc',
        'FormaFEst',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function suministro()
    {
        return $this->hasOne(SUMINISTROS::class);
    }

}
