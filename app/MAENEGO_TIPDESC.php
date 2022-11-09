<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MAENEGO_TIPDESC extends Model
{
    //
    protected $table = 'MAENEGO_TIPDESC';

    protected $fillable = [
        'maenego_id',
        'tipdesc_id',
        'MTPorDesc',
        'MTxCada',
        'MtxBonifica',
    ];

    public function maenego()
    {
        return $this->belongsTo(MAENEGO::class);
    }

    public function tipdesc()
    {
        return $this->belongsTo(TIPDESC::class);
    }

}
