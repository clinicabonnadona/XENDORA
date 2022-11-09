<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MAENEGO extends Model
{
    //
    protected $table = 'MAENEGO';

    public $timestamps = false;

    protected $fillable = [
        'suministro_id',
        'tercero_id',
        'MNegoPreAA',
        'MNegoPreVig',
        'MNegoObs',
        'MNegoObsFar',
        'MNValDesc',
        'MNValNc',
        'MNxCada',
        'MNxBonifica',
        'MNegoPreDDes',
        'MNegoUtiReg',
        'MNegoAproFar',
        'user_id',
        'MNegoEst',
        'created_at',
        'updated_at'
    ];

    public function suministro()
    {
        return $this->belongsTo(SUMINISTROS::class);
    }

    public function tercero()
    {
        return $this->belongsTo(TERCEROS::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function maenegotipdesc()
    {
        return $this->hasOne(MAENEGO_TIPDESC::class);
    }

}
