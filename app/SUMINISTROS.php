<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SUMINISTROS extends Model
{
    //
    protected $table = 'SUMINISTROS';

    //public $timestamps = false;


    protected $fillable = [
        'SumCod',
        'SumNomG',
        'SumNomC',
        'formafar_id',
        'formapre_id',
        'SumPosNoPos',
        'SumReg',
        'SumPreReg',
        'SumAltCos',
        'SumEst',
        'user_id',
    ];

    public function maenego()
    {
        return $this->hasOne(MAENEGO::class);
    }

    public function formafar()
    {
        return $this->belongsTo(FORMAFAR::class);
    }

    public function formapre()
    {
        return $this->belongsTo(FORMAPRE::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
