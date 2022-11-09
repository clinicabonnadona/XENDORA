<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TIPDOC extends Model
{
    //
    protected $table = 'TIPDOC';

    protected $fillable = [
        'TDocDesc',
        'TDocIni',
        'TDocEst',
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
