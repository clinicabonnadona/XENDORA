<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TIPDESC extends Model
{
    //
    protected $table = 'TIPDESC';

    protected $fillable = [
        'TDDesc',
        'TDEst',
    ];

    public function maenegotipdesc()
    {
        return $this->hasOne(MAENEGO_TIPDESC::class);
    }

}
