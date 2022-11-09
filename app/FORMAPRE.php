<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FORMAPRE extends Model
{
    //
    protected $table = 'FORMAPRE';

    protected $fillable = [
        'FormaPDesc',
        'FormaEst',
        'user_id',
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
